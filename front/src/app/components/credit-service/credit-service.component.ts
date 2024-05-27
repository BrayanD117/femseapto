import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DividerModule } from 'primeng/divider';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-credit-service',
  standalone: true,
  imports: [DividerModule, CommonModule, RouterLink],
  templateUrl: './credit-service.component.html',
  styleUrl: './credit-service.component.css'
})
export class CreditServiceComponent {
  selectedCreditLine: string = 'CREDITO COMPRA O MEJORAS DE VIVIENDA';

  selectCreditLine(line: string) {
    this.selectedCreditLine = line;
  }

}
