import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { LineasCreditoService } from '../../../../services/lineas-credito.service';
import { CurrencyFormatPipe } from '../../../pipes/currency-format.pipe';

@Component({
  selector: 'app-simulator',
  standalone: true,
  imports: [CommonModule, FormsModule, CurrencyFormatPipe],
  templateUrl: './simulator.component.html',
  styleUrls: ['./simulator.component.css']
})
export class SimulatorComponent implements OnInit {
  lineasCredito: any[] = [];
  selectedCreditLine: number | undefined;
  selectedCreditLineDetails: any;
  loanAmount = 0;
  interestRate: number | undefined;
  biweeklyPayment = 0;
  totalPayment = 0;

  constructor(private lineasCreditoService: LineasCreditoService) { }

  ngOnInit(): void {
    this.lineasCreditoService.obtenerLineasCredito().subscribe(
      data => {
        this.lineasCredito = data;
      },
      error => {
        console.error('Error al obtener líneas de crédito:', error);
      }
    );
  }

  onCreditLineChange(): void {
    if (this.selectedCreditLine) {
      this.lineasCreditoService.obtenerLineaCreditoPorId(this.selectedCreditLine).subscribe(
        data => {
          this.selectedCreditLineDetails = data;
          this.setInterestRate();
        },
        error => {
          console.error('Error al obtener detalles de la línea de crédito:', error);
        }
      );
    }
  }

  onLoanTermChange(): void {
    this.setInterestRate();
  }

  onLoanAmountChange(value: string): void {
    // Remove any non-numeric characters
    const numericValue = value.replace(/[^0-9]/g, '');
    this.loanAmount = parseInt(numericValue, 10);

    // Update the displayed value
    const inputElement = document.getElementById('loanAmount') as HTMLInputElement;
    if (inputElement) {
      inputElement.value = new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(this.loanAmount);
    }
  }

  setInterestRate(): void {
    if (this.selectedCreditLineDetails) {
      const loanTerm = Number((document.getElementById('loanTerm') as HTMLInputElement).value);
      this.interestRate = this.selectedCreditLineDetails.tasa_interes_1; // Default to tasa_interes_1
      if (loanTerm > 120 && this.selectedCreditLineDetails.tasa_interes_2) {
        this.interestRate = this.selectedCreditLineDetails.tasa_interes_2;
      }
    }
  }

  calculate(): void {
    const interestRate = this.interestRate ? this.interestRate / 100 : 0;
    const loanTerm = Number((document.getElementById('loanTerm') as HTMLInputElement).value);

    if (this.loanAmount && interestRate && loanTerm) {
      const biweeklyInterestRate = interestRate / 2;
      const biweeklyPayment = this.loanAmount * biweeklyInterestRate / (1 - Math.pow(1 + biweeklyInterestRate, -loanTerm));
      const totalPayment = biweeklyPayment * loanTerm;

      this.biweeklyPayment = biweeklyPayment;
      this.totalPayment = totalPayment;
    } else {
      this.biweeklyPayment = 0;
      this.totalPayment = 0;
    }
  }
}
