import { Component, OnInit } from '@angular/core';
import { TableModule } from 'primeng/table';
import { CommonModule } from '@angular/common';
import { map } from 'rxjs/operators';
import { forkJoin } from 'rxjs';

import { SolicitudAhorroService } from '../../../../services/request-saving.service';
import { RequestSavingWithdrawalService, RequestSavingWithdrawal } from '../../../../services/request-saving-withdrawal.service';
import { LoginService } from '../../../../services/login.service';
import { SavingLinesService, SavingLine } from '../../../../services/saving-lines.service';

import { GenerateSavingRequestComponent } from '../../admin/components/generate-saving-request/generate-saving-request.component';
import { GenerateSavingWithdrawalRequestComponent } from '../../admin/components/generate-saving-withdrawal-request/generate-saving-withdrawal-request.component';


@Component({
  selector: 'app-saving-request-history',
  standalone: true,
  imports: [CommonModule, TableModule, GenerateSavingRequestComponent, GenerateSavingWithdrawalRequestComponent],
  templateUrl: './saving-request-history.component.html',
  styleUrl: './saving-request-history.component.css'
})
export class SavingRequestHistoryComponent implements OnInit {

  savingRequests: any[] = [];
  savingWithdrawalRequests: RequestSavingWithdrawal[] = [];

  constructor(private savingRequestService: SolicitudAhorroService,
    private savingWithdrawalRequestService: RequestSavingWithdrawalService,
    private loginService: LoginService,
    private savingLinesService: SavingLinesService
  ) {}

  ngOnInit() {
    const token = this.loginService.getTokenClaims();
  
    this.getSavingRequests(token.userId);
    this.getSavingWithdrawalRequests(token.userId);  
  }

  getSavingRequests(userId: number): void {
    this.savingRequestService.getByUserId(userId).subscribe({
      next: (response: any[]) => {
        this.savingRequests = response.map(request => ({
          ...request,
          montoTotalAhorrar: this.formatNumber(request.montoTotalAhorrar)
        }));
      },
      error: (error) => {
        console.error('Error al obtener las solicitudes de retiro de ahorro:', error);
      }
    });
  }

  getSavingWithdrawalRequests(userId: number): void {
    this.savingWithdrawalRequestService.getByUserId(userId).subscribe((data: RequestSavingWithdrawal[]) => {
      const requests = data.map((request: RequestSavingWithdrawal) => {
        return this.savingLinesService.getNameById(request.idLineaAhorro).pipe(
          map((nombre: string) => ({
            ...request,
            lineaAhorroNombre: nombre,
            montoRetirarFormateado: this.formatNumber(request.montoRetirar.toString())
          }))
        );
      });

      forkJoin(requests).subscribe(results => {
        this.savingWithdrawalRequests = results as RequestSavingWithdrawal[];
      });
    });
  }

  formatNumber(value: string): string {
    const numericValue = parseFloat(value.replace(',', '.')); // Asegura que el formato de número sea válido
    return new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP',
      minimumFractionDigits: 2
    }).format(numericValue);
  }

}
