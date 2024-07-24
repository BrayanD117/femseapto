import { Component } from '@angular/core';
import { RequestSavingWithdrawal, RequestSavingWithdrawalService } from '../../../../../services/request-saving-withdrawal.service';
import { User, UserService } from '../../../../../services/user.service';
import { forkJoin } from 'rxjs';
import { GenerateSavingWithdrawalRequestComponent } from '../generate-saving-withdrawal-request/generate-saving-withdrawal-request.component';
import { CommonModule } from '@angular/common';
import { SavingLine, SavingLinesService } from '../../../../../services/saving-lines.service';

@Component({
  selector: 'app-saving-withdrawal-request',
  standalone: true,
  imports: [CommonModule, GenerateSavingWithdrawalRequestComponent],
  templateUrl: './saving-withdrawal-request.component.html',
  styleUrl: './saving-withdrawal-request.component.css'
})
export class SavingWithdrawalRequestComponent {
  savingWdRequests: any[] = [];
  totalRecords: number = 0;
  loading: boolean = true;
  searchQuery: string = '';
  rows: number = 10;
  currentPage: number = 1;
  totalPages: number = 0;
  pages: number[] = [];

  constructor(
    private savingWdRequestService: RequestSavingWithdrawalService,
    private userService: UserService,
    private savingLineService: SavingLinesService
  ) {}

  ngOnInit(): void {
    this.loadSavingWdRequests();
  }

  loadSavingWdRequests(page: number = 1, size: number = 10): void {
    this.loading = true;
    this.savingWdRequestService.getAll({ page, size, search: this.searchQuery }).subscribe({
      next: response => {
        const requests = response.data;
        const userObservables = requests.map((request: any) => this.userService.getById(request.idUsuario));
        const savingLineObservables = requests.map((request: any) => this.savingLineService.getById(request.idLineaAhorro));
        const observables = [...userObservables, ...savingLineObservables];

        forkJoin(observables).subscribe((responses: any[]) => {
          const users = responses.slice(0, requests.length) as User[];
          const savingLines = responses.slice(requests.length) as SavingLine[];

          this.savingWdRequests = requests.map((request: any, index: number) => {
            const user = users[index];
            const savingLine = savingLines[index];
            return {
              ...request,
              numeroDocumento: user?.numeroDocumento || '',
              nombreAsociado: `${user.primerNombre || ''} ${user.segundoNombre || ''} ${user.primerApellido || ''} ${user.segundoApellido || ''}`.trim(),
              nombreLineaAhorro: savingLine?.nombre || '',
              montoRetirar: this.formatNumber(request.montoRetirar)
            };
          });

          this.totalRecords = response.total;
          this.totalPages = Math.ceil(this.totalRecords / this.rows);
          this.pages = Array(this.totalPages).fill(0).map((x, i) => i + 1);
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
    this.loadSavingWdRequests();
  }

  onPageChange(page: number): void {
    this.currentPage = page;
    this.loadSavingWdRequests(this.currentPage, this.rows);
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
