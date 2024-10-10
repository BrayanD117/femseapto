import { Component, OnInit, ViewChild } from '@angular/core';
import { RequestCreditService } from '../../../../../services/request-credit.service';
import { UserService, User } from '../../../../../services/user.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { GenerateCreditRequestComponent } from '../generate-credit-request/generate-credit-request.component';
import { CreditReportComponent } from '../credit-report/credit-report.component';
import { forkJoin } from 'rxjs';
import { LineasCreditoService } from '../../../../../services/lineas-credito.service';

import { Table, TableModule } from 'primeng/table';
import { TagModule } from 'primeng/tag';
import { IconFieldModule } from 'primeng/iconfield';
import { InputIconModule } from 'primeng/inputicon';
import { HttpClientModule } from '@angular/common/http';
import { InputTextModule } from 'primeng/inputtext';
import { MultiSelectModule } from 'primeng/multiselect';
import { DropdownModule } from 'primeng/dropdown';


@Component({
  selector: 'app-credit-requests',
  standalone: true,
  imports: [CommonModule, FormsModule, GenerateCreditRequestComponent, CreditReportComponent, TableModule, TagModule, IconFieldModule, InputTextModule, InputIconModule, MultiSelectModule, DropdownModule, HttpClientModule],
  templateUrl: './credit-requests.component.html',
  styleUrls: ['./credit-requests.component.css']
})
export class CreditRequestsComponent implements OnInit {
  @ViewChild('dt2') dt2!: Table;

  creditRequests: any[] = [];
  totalRecords: number = 0;
  loading: boolean = true;
  searchQuery: string = '';
  rows: number = 10;
  currentPage: number = 1;
  totalPages: number = 0;
  pages: number[] = [];

  constructor(
    private requestCreditService: RequestCreditService,
    private userService: UserService,
    private creditLineService: LineasCreditoService
    
  ) {}

  ngOnInit(): void {
    this.loadCreditRequests();
  }

  onFilterGlobal(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target) {
      this.dt2.filterGlobal(target.value, 'contains');
    }
  }

  loadCreditRequests(page: number = 1, size: number = 10): void {
    this.loading = true;
    this.requestCreditService.getAll({ page, size, search: this.searchQuery }).subscribe({
      next: response => {
        const requests = response.data;
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
          //this.totalPages = Math.ceil(this.totalRecords / this.rows);
          //this.pages = Array(this.totalPages).fill(0).map((x, i) => i + 1);
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
    this.loadCreditRequests();
  }

  onPageChange(page: number): void {
    this.currentPage = page;
    this.loadCreditRequests(this.currentPage, this.rows);
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
