import { Component, OnInit } from '@angular/core';
import { SavingBalance, SavingBalanceService } from '../../../../services/saving-balance.service';
import { SavingLinesService, SavingLine } from '../../../../services/saving-lines.service';
import { LoginService } from '../../../../services/login.service';
import { TableModule } from 'primeng/table';
import { CommonModule } from '@angular/common';
import { forkJoin } from 'rxjs';
import { map } from 'rxjs/operators';

@Component({
  selector: 'app-saving-balance',
  standalone: true,
  imports: [CommonModule, TableModule],
  templateUrl: './saving-balance.component.html',
  styleUrl: './saving-balance.component.css'
})
export class SavingBalanceComponent implements OnInit {

  savingsBalance: SavingBalance[] = [];

  constructor(private savingBalanceService: SavingBalanceService,
              private savingLinesService: SavingLinesService,
              private loginService: LoginService) {}

  ngOnInit() {
    const token = this.loginService.getTokenClaims();

    this.savingBalanceService.getByUserId(token.userId).subscribe((data: SavingBalance[]) => {
      const requests = data.map((saving: SavingBalance) => {
        return this.savingLinesService.getById(saving.idLineaAhorro).pipe(
          map((linea: SavingLine) => ({
            ...saving,
            lineaAhorroNombre: linea.nombre,
            valorSaldo: this.formatNumber(saving.valorSaldo.toString()),            
            ahorroQuincenal: this.formatNumber(saving.ahorroQuincenal.toString()),
          }))
        );
      });

      forkJoin(requests).subscribe(results => {
        this.savingsBalance = results as unknown as SavingBalance[];
      });
    });
  }

  formatNumber(value: string): string {
    const numericValue = parseFloat(value.replace(',', '.'));
    return new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP',
      minimumFractionDigits: 2
    }).format(numericValue);
  }
}
