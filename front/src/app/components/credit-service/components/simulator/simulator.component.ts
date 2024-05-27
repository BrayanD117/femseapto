import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { LineasCreditoService } from '../../../../services/lineas-credito.service';

@Component({
  selector: 'app-simulator',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './simulator.component.html',
  styleUrl: './simulator.component.css'
})
export class SimulatorComponent implements OnInit {
  lineasCredito: any[] = [];
  selectedCreditLine: number | undefined;
  selectedCreditLineDetails: any;
  biweeklyPayment: string = '--';
  totalPayment: string = '--';

  constructor(private lineasCreditoService: LineasCreditoService) { }

  ngOnInit(): void {
    this.lineasCreditoService.obtenerLineasCredito().subscribe(data => {
      this.lineasCredito = data;
    });
  }

  onCreditLineChange(): void {
    if (this.selectedCreditLine) {
      this.lineasCreditoService.obtenerLineaCreditoPorId(this.selectedCreditLine).subscribe(data => {
        this.selectedCreditLineDetails = data;
      });
    }
  }

  calculate(): void {
    const loanAmount = Number((document.getElementById('loanAmount') as HTMLInputElement).value);
    const interestRate = Number((document.getElementById('interestRate') as HTMLInputElement).value) / 100;
    const loanTerm = Number((document.getElementById('loanTerm') as HTMLInputElement).value);

    if (loanAmount && interestRate && loanTerm) {
      const biweeklyInterestRate = interestRate / 2;
      const biweeklyPayment = loanAmount * biweeklyInterestRate / (1 - Math.pow(1 + biweeklyInterestRate, -loanTerm));
      const totalPayment = biweeklyPayment * loanTerm;

      this.biweeklyPayment = biweeklyPayment.toFixed(2);
      this.totalPayment = totalPayment.toFixed(2);
    } else {
      this.biweeklyPayment = '--';
      this.totalPayment = '--';
    }
  }
}
