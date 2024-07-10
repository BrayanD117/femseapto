import { Component, OnInit } from '@angular/core';
import { CreditBalance, CreditBalanceService } from '../../../../services/credit-balance.service';
import { LoginService } from '../../../../services/login.service';
import { TableModule } from 'primeng/table';
import { CommonModule } from '@angular/common';
import { LineasCreditoService } from '../../../../services/lineas-credito.service';
import { forkJoin } from 'rxjs';
import { map } from 'rxjs/operators';

@Component({
  selector: 'app-credit-balance',
  standalone: true,
  imports: [CommonModule, TableModule],
  templateUrl: './credit-balance.component.html',
  styleUrl: './credit-balance.component.css'
})
export class CreditBalanceComponent implements OnInit {
  
  creditsBalance: CreditBalance[] = [];

  constructor(private creditBalanceService: CreditBalanceService,
    private creditLineService: LineasCreditoService,
    private loginService: LoginService
  ) {}

  ngOnInit() {
    const token = this.loginService.getTokenClaims();

    this.creditBalanceService.getByUserId(token.userId).subscribe((data: CreditBalance[]) => {
      const requests = data.map((credit: CreditBalance) => {
        return this.creditLineService.getNameById(credit.idLineaCredito).pipe(
          map((nombre: string) => ({
            ...credit,
            lineaCreditoNombre: nombre,
            valorSolicitado: this.formatNumber(credit.valorSolicitado),
            valorPagado: this.formatNumber(credit.valorPagado),
            valorSaldo: this.formatNumber(credit.valorSaldo)
          }))
        );
      });

      forkJoin(requests).subscribe(results => {
        this.creditsBalance = results as CreditBalance[];
      });
    });
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