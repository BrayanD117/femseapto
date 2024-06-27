import { Component, ElementRef, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DividerModule } from 'primeng/divider';
import { RouterLink } from '@angular/router';

import { SimulatorComponent } from './components/simulator/simulator.component';

@Component({
  selector: 'app-credit-service',
  standalone: true,
  imports: [DividerModule, CommonModule, RouterLink, SimulatorComponent],
  templateUrl: './credit-service.component.html',
  styleUrl: './credit-service.component.css'
})
export class CreditServiceComponent {
  selectedCreditLine: string = 'CREDITO COMPRA O MEJORAS DE VIVIENDA';

  @ViewChild('card') card: ElementRef | undefined;

  selectCreditLine(line: string) {
    this.selectedCreditLine = line;
    setTimeout(() => {
      if (this.card) {
        this.card.nativeElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }, 100); // Espera para asegurarte de que la vista se ha actualizado
  }
}
