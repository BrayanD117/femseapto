import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { LoginService } from '../../../../../../../services/login.service';
import { FinancialInformation, FinancialInfoService } from '../../../../../../../services/financial-info.service';
import { CommonModule } from '@angular/common';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { InputNumberModule } from 'primeng/inputnumber';

import { BankAccountType, BankAccountTypeService } from '../../../../../../../services/bank-account-type.service';

@Component({
  selector: 'app-financial-info',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, ToastModule, InputNumberModule],
  providers: [MessageService],
  templateUrl: './financial-info.component.html',
  styleUrls: ['./financial-info.component.css']
})
export class FinancialInfoComponent implements OnInit {
  financialForm: FormGroup;
  userId: number | null = null;

  bankAccountTypes: BankAccountType[] = [];
  
  constructor(
    private fb: FormBuilder,
    private financialInfoService: FinancialInfoService,
    private loginService: LoginService,
    private bankAccountTypeService: BankAccountTypeService,
    private messageService: MessageService
  ) {
    this.financialForm = this.fb.group({
      id: [''],
      idUsuario: ['', Validators.required],
      nombreBanco: ['', Validators.required],
      idTipoCuentaBanc: ['', Validators.required],
      numeroCuentaBanc: ['', Validators.required],
      ingresosMensuales: ['', Validators.required],
      primaProductividad: ['', Validators.required],
      otrosIngresosMensuales: ['', Validators.required],
      conceptoOtrosIngresosMens: [''],
      totalIngresosMensuales: [{ value: '', disabled: true }, Validators.required],
      egresosMensuales: ['', Validators.required],
      obligacionFinanciera: ['', Validators.required],
      otrosEgresosMensuales: ['', Validators.required],
      totalEgresosMensuales: [{ value: '', disabled: true }, Validators.required],
      totalActivos: [{ value: '', disabled: true }, Validators.required],
      totalPasivos: [{ value: '', disabled: true }, Validators.required],
      totalPatrimonio: [{ value: '', disabled: true }, Validators.required]
    });
  }

  ngOnInit(): void {
    const token = this.loginService.getTokenClaims();

    if (token) {
      this.userId = token.userId;

      this.financialForm.patchValue({
        idUsuario: this.userId
      });

      this.financialInfoService.getByUserId(token.userId).subscribe(financialInfo => {
        this.financialForm.patchValue(financialInfo);
      });
      
    }

    this.loadBankAccountTypes();

    // Escuchar cambios en los campos relevantes para recalcular totales
    this.financialForm.get('ingresosMensuales')?.valueChanges.subscribe(() => this.calculateTotals());
    this.financialForm.get('otrosIngresosMensuales')?.valueChanges.subscribe(() => this.calculateTotals());
    this.financialForm.get('egresosMensuales')?.valueChanges.subscribe(() => this.calculateTotals());
    this.financialForm.get('obligacionFinanciera')?.valueChanges.subscribe(() => this.calculateTotals());
    this.financialForm.get('otrosEgresosMensuales')?.valueChanges.subscribe(() => this.calculateTotals());
  }

  loadBankAccountTypes(): void {
    this.bankAccountTypeService.getAll().subscribe(data => {
      this.bankAccountTypes = data;
    });
  }

  calculateTotals(): void {
    const formValues = this.financialForm.value;

    const ingresos = parseFloat(formValues.ingresosMensuales) || 0;
    const otrosIngresos = parseFloat(formValues.otrosIngresosMensuales) || 0;
    const egresos = parseFloat(formValues.egresosMensuales) || 0;
    const obligacionFinanciera = parseFloat(formValues.obligacionFinanciera) || 0;
    const otrosEgresos = parseFloat(formValues.otrosEgresosMensuales) || 0;

    const totalIngresos = ingresos + otrosIngresos;
    const totalEgresos = egresos + obligacionFinanciera + otrosEgresos;
    const totalActivos = totalIngresos;
    const totalPasivos = totalEgresos;
    const totalPatrimonio = totalActivos - totalPasivos;

    this.financialForm.patchValue({
      totalIngresosMensuales: totalIngresos.toFixed(2),
      totalEgresosMensuales: totalEgresos.toFixed(2),
      totalActivos: totalActivos.toFixed(2),
      totalPasivos: totalPasivos.toFixed(2),
      totalPatrimonio: totalPatrimonio.toFixed(2)
    });
  }

  onSubmit(): void {
    console.log(this.financialForm.value);
    if (this.financialForm.valid) {
      const parsedData = { ...this.financialForm.value };

      console.log("ENTRA", parsedData);

      if(parsedData.id) {
        this.financialInfoService.update(parsedData).subscribe({
          next: () => {
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Información financiera actualizada correctamente' });
          },
          error: (err) => {
            console.error('Error al actualizar la información financiera', err);
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo actualizar la información financiera. Vuelve a intentarlo.' });
          }
        });
      } else {
        this.financialInfoService.create(parsedData).subscribe({
          next: () => {
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Información financiera creada correctamente' });
          },
          error: (err) => {
            console.error('Error al actualizar la información financiera', err);
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo crear la información financiera. Vuelve a intentarlo.' });
          }
        });
      }     
    } else {
      this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Vuelve a iniciar sesión e inténtalo de nuevo.' });
    }
  }
}
