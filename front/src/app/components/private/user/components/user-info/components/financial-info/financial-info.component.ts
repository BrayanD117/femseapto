import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { LoginService } from '../../../../../../../services/login.service';
import { FinancialInfoService } from '../../../../../../../services/financial-info.service';
import { CommonModule } from '@angular/common';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';

@Component({
  selector: 'app-financial-info',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, ToastModule],
  providers: [MessageService],
  templateUrl: './financial-info.component.html',
  styleUrls: ['./financial-info.component.css']
})
export class FinancialInfoComponent implements OnInit {
  financialForm: FormGroup;

  constructor(
    private fb: FormBuilder,
    private financialInfoService: FinancialInfoService,
    private loginService: LoginService,
    private messageService: MessageService
  ) {
    this.financialForm = this.fb.group({
      nombreBanco: ['', Validators.required],
      idTipoCuentaBanc: ['', Validators.required],
      numeroCuentaBanc: ['', Validators.required],
      ingresosMensuales: ['', Validators.required],
      primaProductividad: ['', Validators.required],
      otrosIngresosMensuales: ['', Validators.required],
      conceptoOtrosIngresosMens: [''],
      totalIngresosMensuales: ['', Validators.required],
      egresosMensuales: ['', Validators.required],
      obligacionFinanciera: ['', Validators.required],
      otrosEgresosMensuales: ['', Validators.required],
      totalEgresosMensuales: ['', Validators.required],
      totalActivos: ['', Validators.required],
      totalPasivos: ['', Validators.required],
      totalPatrimonio: ['', Validators.required],
      montoMaxAhorro: ['', Validators.required]
    });
  }

  ngOnInit(): void {
    const token = this.loginService.getTokenClaims();

    if (token) {
      this.financialInfoService.getFinancialInfo(token.userId).subscribe(financialInfo => {
        financialInfo.ingresosMensuales = this.formatNumber(financialInfo.ingresosMensuales);
        financialInfo.primaProductividad = this.formatCurrency(financialInfo.primaProductividad);
        financialInfo.otrosIngresosMensuales = this.formatCurrency(financialInfo.otrosIngresosMensuales);
        financialInfo.totalIngresosMensuales = this.formatCurrency(financialInfo.totalIngresosMensuales);
        financialInfo.egresosMensuales = this.formatCurrency(financialInfo.egresosMensuales);
        financialInfo.obligacionFinanciera = this.formatCurrency(financialInfo.obligacionFinanciera);
        financialInfo.otrosEgresosMensuales = this.formatCurrency(financialInfo.otrosEgresosMensuales);
        financialInfo.totalEgresosMensuales = this.formatCurrency(financialInfo.totalEgresosMensuales);
        financialInfo.totalActivos = this.formatCurrency(financialInfo.totalActivos);
        financialInfo.totalPasivos = this.formatCurrency(financialInfo.totalPasivos);
        financialInfo.totalPatrimonio = this.formatCurrency(financialInfo.totalPatrimonio);
        financialInfo.montoMaxAhorro = this.formatCurrency(financialInfo.montoMaxAhorro);

        this.financialForm.patchValue(financialInfo);
      });
    }
  }

  formatCurrency(value: number): string {
    if (value == null) {
      return '';
    }
    const formattedValue = Math.round(value).toLocaleString('es-ES');
    return `$ ${formattedValue}`;
  }

  formatNumber(value: string): string {
    const numericValue = parseFloat(value.replace(',', '.')); // Asegura que el formato de número sea válido
    return new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP',
      minimumFractionDigits: 0
    }).format(numericValue);
  }

  formatCurrencyInput(event: Event, controlName: string): void {
    const inputElement = event.target as HTMLInputElement;
    const numericValue = inputElement.value.replace(/[^0-9]/g, '');
    const value = numericValue ? parseInt(numericValue, 10) : 0;
    this.financialForm.get(controlName)?.setValue(value);
    inputElement.value = `$ ${value.toLocaleString('es-ES')}`;
  }

  onSubmit(): void {
    if (this.financialForm.valid) {
      const token = this.loginService.getTokenClaims();
      const formattedData = { ...this.financialForm.value };
      Object.keys(formattedData).forEach(key => {
        if (typeof formattedData[key] === 'string') {
          formattedData[key] = parseInt(formattedData[key].replace(/[^0-9]/g, ''), 10);
        }
      });
      this.financialInfoService.updateFinancialInfo(token.userId, formattedData).subscribe({
        next: () => {
          this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Información financiera actualizada correctamente' });
        },
        error: (err) => {
          this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo actualizar la información financiera' });
        }
      });
    }
  }
}
