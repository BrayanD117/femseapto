import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { DropdownModule } from 'primeng/dropdown';
import { LineasCreditoService } from '../../../../services/lineas-credito.service';
import { CurrencyFormatPipe } from '../../../pipes/currency-format.pipe';

@Component({
  selector: 'app-simulator',
  standalone: true,
  imports: [CommonModule, FormsModule, DropdownModule, CurrencyFormatPipe],
  templateUrl: './simulator.component.html',
  styleUrls: ['./simulator.component.css']
})
export class SimulatorComponent implements OnInit {
  lineasCredito: any[] = [];
  selectedCreditLine: any;
  selectedCreditLineDetails: any;
  loanAmount = 0;
  interestRate: number | undefined;
  biweeklyPayment = 0;
  totalPayment = 0;
  isLoanTermInvalid = false;

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
      this.lineasCreditoService.obtenerLineaCreditoPorId(this.selectedCreditLine.id).subscribe(
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
    const loanTerm = Number((document.getElementById('loanTerm') as HTMLInputElement).value);
    if (this.selectedCreditLineDetails && loanTerm > this.selectedCreditLineDetails.plazo) {
      this.isLoanTermInvalid = true;
    } else {
      this.isLoanTermInvalid = false;
    }
    this.setInterestRate();
  }

  onLoanAmountInput(event: Event): void {
    const inputElement = event.target as HTMLInputElement;
    const numericValue = inputElement.value.replace(/[^0-9]/g, '');
    this.loanAmount = parseInt(numericValue, 10) || 0;

    inputElement.value = `$ ${this.loanAmount.toLocaleString('es-ES')}`;
  }

  setInterestRate(): void {
    if (this.selectedCreditLineDetails) {
      const loanTerm = Number((document.getElementById('loanTerm') as HTMLInputElement).value);
      this.interestRate = this.selectedCreditLineDetails.tasa_interes_1;
      if (loanTerm > 120 && this.selectedCreditLineDetails.tasa_interes_2) {
        this.interestRate = this.selectedCreditLineDetails.tasa_interes_2;
      }
    }
  }

  calculate(): void {
    if (this.isLoanTermInvalid) {
      return;
    }

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
