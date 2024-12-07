import { Component, OnInit, ViewChild, ViewEncapsulation } from '@angular/core';
import { RequestCreditService } from '../../../../../services/request-credit.service';
import { UserService, User } from '../../../../../services/user.service';
import { CommonModule } from '@angular/common';
import { FormControl, FormsModule, ReactiveFormsModule } from '@angular/forms';
import { GenerateCreditRequestComponent } from '../generate-credit-request/generate-credit-request.component';
import { CreditReportComponent } from '../credit-report/credit-report.component';
import { forkJoin } from 'rxjs';
import { LineasCreditoService } from '../../../../../services/lineas-credito.service';
import { debounceTime, distinctUntilChanged } from 'rxjs/operators';

import { Table, TableModule } from 'primeng/table';
import { TagModule } from 'primeng/tag';
import { IconFieldModule } from 'primeng/iconfield';
import { InputIconModule } from 'primeng/inputicon';
import { HttpClientModule } from '@angular/common/http';
import { InputTextModule } from 'primeng/inputtext';
import { MultiSelectModule } from 'primeng/multiselect';
import { DropdownModule } from 'primeng/dropdown';
import { InputGroupModule } from 'primeng/inputgroup';
import { ToolbarModule } from 'primeng/toolbar';
import { InputGroupAddonModule } from 'primeng/inputgroupaddon';
import { ButtonModule } from 'primeng/button';

@Component({
  selector: 'app-credit-requests',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, FormsModule, GenerateCreditRequestComponent, CreditReportComponent, TableModule, TagModule, IconFieldModule, InputTextModule, InputIconModule, MultiSelectModule, DropdownModule, HttpClientModule, InputGroupModule, ToolbarModule, InputGroupAddonModule, ButtonModule],
  templateUrl: './credit-requests.component.html',
  styleUrls: ['./credit-requests.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class CreditRequestsComponent implements OnInit {
  @ViewChild('dt2') dt2!: Table;

  creditRequests: any[] = [];

  searchControl: FormControl;
  dateControl: FormControl;

  totalRecords: number = 0;
  loading: boolean = true;
  searchQuery: string = '';
  dateQuery: string = '';
  rows: number = 10;
  currentPage: number = 1;
  totalPages: number = 0;
  pages: number[] = [];

  constructor(
    private requestCreditService: RequestCreditService,
    private userService: UserService,
    private creditLineService: LineasCreditoService
    
  ) {
    this.searchControl = new FormControl('');
    this.dateControl = new FormControl('');
  }

  ngOnInit(): void {
    this.loadCreditRequests();
  }

  onFilterGlobal(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target) {
      this.dt2.filterGlobal(target.value, 'contains');
    }
  }

  loadCreditRequests(page: number = 1, size: number = 10, search: string = '', date: string = ''): void {
    this.loading = true;
    this.requestCreditService.getAll({ page, size, search, date }).subscribe({
      next: response => {
        const requests = response.data;
        if (requests.length === 0) {
          this.creditRequests = [];
          this.totalRecords = 0;
          this.loading = false;
          return;
        }

        const userObservables = requests.map((request: any) => this.userService.getById(request.idUsuario));
        const creditLineObservables = requests.map((request: any) => this.creditLineService.obtenerLineaCreditoPorId(request.idLineaCredito));
        const observables = [...userObservables, ...creditLineObservables];

        forkJoin(observables).subscribe((responses: any[]) => {
          const userArray = responses.slice(0, requests.length) as User[];
          const creditLineArray = responses.slice(requests.length) as any[];
          
          this.creditRequests = requests.map((request: any, index: number) => {
            const user = userArray[index];
            const creditLine = creditLineArray[index];
            return {
              ...request,
              numeroDocumento: user?.numeroDocumento || '',
              nombreAsociado: `${user.primerNombre || ''} ${user.segundoNombre || ''} ${user.primerApellido || ''} ${user.segundoApellido || ''}`.trim(),
              montoSolicitado: this.formatNumber(request.montoSolicitado),
              valorCuotaQuincenal: this.formatNumber(request.valorCuotaQuincenal),
              nombreLineaCredito: creditLine?.nombre || '',
            };
          });

          this.totalRecords = response.total;
          this.loading = false;
        });
      },
      error: err => {
        console.error('Error al cargar solicitudes de crédito', err);
        this.loading = false;
      }
    });
  }

  onSearch(): void {
    this.currentPage = 1;
    this.searchQuery = this.searchControl.value || '';
    this.dateQuery = this.dateControl.value || '';
    this.loadCreditRequests(this.currentPage, this.rows, this.searchQuery, this.dateQuery);
  }

  onPageChange(page: number): void {
    this.currentPage = page;
    this.loadCreditRequests(this.currentPage, this.rows, this.searchQuery, this.dateQuery);
  }

  onClear(): void {
    this.searchControl.setValue('');
    this.dateControl.setValue('');
    this.currentPage = 1;
    this.loadCreditRequests();
  }  

  formatNumber(value: string): string {
    const numericValue = parseFloat(value.replace(',', '.'));
    return new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP',
      minimumFractionDigits: 0
    }).format(numericValue);
  }
}