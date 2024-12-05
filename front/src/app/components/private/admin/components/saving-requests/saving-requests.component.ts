import { Component, OnInit, ViewChild } from '@angular/core';
import { SolicitudAhorroService } from '../../../../../services/request-saving.service';
import { UserService, User } from '../../../../../services/user.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { GenerateSavingRequestComponent } from '../generate-saving-request/generate-saving-request.component';
import { forkJoin } from 'rxjs';

import { Table, TableModule } from 'primeng/table';
import { TagModule } from 'primeng/tag';
import { IconFieldModule } from 'primeng/iconfield';
import { InputIconModule } from 'primeng/inputicon';
import { HttpClientModule } from '@angular/common/http';
import { InputTextModule } from 'primeng/inputtext';
import { MultiSelectModule } from 'primeng/multiselect';
import { DropdownModule } from 'primeng/dropdown';

@Component({
  selector: 'app-saving-requests',
  standalone: true,
  imports: [CommonModule, FormsModule, GenerateSavingRequestComponent, TableModule, TagModule, IconFieldModule, InputTextModule, InputIconModule, MultiSelectModule, DropdownModule, HttpClientModule],
  templateUrl: './saving-requests.component.html',
  styleUrls: ['./saving-requests.component.css']
})
export class SavingRequestsComponent implements OnInit {
  @ViewChild('dt2') dt2!: Table;
  
  savingRequests: any[] = [];
  totalRecords: number = 0;
  loading: boolean = true;
  searchQuery: string = '';
  rows: number = 10;
  currentPage: number = 1;
  totalPages: number = 0;
  pages: number[] = [];

  constructor(
    private solicitudAhorroService: SolicitudAhorroService,
    private userService: UserService
  ) {}

  ngOnInit(): void {
    this.loadSavingRequests();
  }

  onFilterGlobal(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target) {
      this.dt2.filterGlobal(target.value, 'contains');
    }
  }

  loadSavingRequests(page: number = 1, size: number = 10): void {
    this.loading = true;
    this.solicitudAhorroService.getAll({ page, size, search: this.searchQuery }).subscribe({
      next: response => {
        const requests = response.data;
        const userObservables = requests.map((request: any) => this.userService.getById(request.idUsuario));

        forkJoin(userObservables).subscribe((users) => {
          const userArray = users as User[];
          this.savingRequests = requests.map((request: any, index: number) => {
            const user = userArray[index];
            return {
              ...request,
              numeroDocumento: user?.numeroDocumento || '',
              nombreAsociado: `${user.primerNombre || ''} ${user.segundoNombre || ''} ${user.primerApellido || ''} ${user.segundoApellido || ''}`.trim(),
              montoTotalAhorrar: this.formatNumber(request.montoTotalAhorrar)
            };
          });

          this.totalRecords = response.total;
          this.loading = false;
        });
      },
      error: err => {
        console.error('Error al cargar solicitudes de ahorro', err);
        this.loading = false;
      }
    });
  }

  onSearch(): void {
    this.loadSavingRequests();
  }

  onPageChange(page: number): void {
    this.currentPage = page;
    this.loadSavingRequests(this.currentPage, this.rows);
  }

  formatNumber(value: string): string {
    const numericValue = parseFloat(value.replace(',', '.')); // Asegura que el formato de número sea válido
    return new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP',
      minimumFractionDigits: 0
    }).format(numericValue);
  }
}
