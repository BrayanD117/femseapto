import { Component, OnInit } from '@angular/core';
import { RequiredSavingBalance, RequiredSavingBalanceService } from '../../../../services/required-saving-balance.service';
import { RequiredSavingLineService, RequiredSavingLine } from '../../../../services/required-saving-line.service';
import { LoginService } from '../../../../services/login.service';
import { TableModule } from 'primeng/table';
import { CommonModule } from '@angular/common';
import { forkJoin } from 'rxjs';
import { map } from 'rxjs/operators';

@Component({
  selector: 'app-required-saving-balance',
  standalone: true,
  imports: [CommonModule, TableModule],
  templateUrl: './required-saving-balance.component.html',
  styleUrl: './required-saving-balance.component.css'
})
export class RequiredSavingBalanceComponent implements OnInit {
  
  requiredSavingsBalance: RequiredSavingBalance[] = [];

  constructor(private requiredSavingBalanceService: RequiredSavingBalanceService,
              private requiredSavingLineService: RequiredSavingLineService,
              private loginService: LoginService) {}

  ngOnInit() {
    const token = this.loginService.getTokenClaims();

    this.requiredSavingBalanceService.getByUserId(token.userId).subscribe((data: RequiredSavingBalance[]) => {
      const requests = data.map((saving: RequiredSavingBalance) => {
        return this.requiredSavingLineService.getById(saving.idLineaAhorroObligatoria).pipe(
          map((linea: RequiredSavingLine) => ({
            ...saving,
            lineaAhorroNombre: linea.nombre,
            valorSaldo: this.formatNumber(saving.valorSaldo.toString())
          }))
        );
      });

      forkJoin(requests).subscribe(results => {
        this.requiredSavingsBalance = results as unknown as RequiredSavingBalance[];
      });
    });
  }

  formatNumber(value: string): string {
    const numericValue = parseFloat(value.replace(',', '.'));
    return new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(numericValue);
  }
}