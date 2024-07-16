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
  financialInfo: FinancialInformation | null = null;

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
      ingresosMensuales: [0, Validators.required],
      primaProductividad: [0, Validators.required],
      otrosIngresosMensuales: [0, Validators.required],
      conceptoOtrosIngresosMens: [''],
      totalIngresosMensuales: [{ value: 0, disabled: true }],
      egresosMensuales: [0, Validators.required],
      obligacionFinanciera: [0, Validators.required],
      otrosEgresosMensuales: [0, Validators.required],
      totalEgresosMensuales: [{ value: 0, disabled: true }],
      totalActivos: [{ value: 0, disabled: true }],
      totalPasivos: [{ value: 0, disabled: true }],
      totalPatrimonio: [{ value: 0, disabled: true }]
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
        this.financialInfo = financialInfo;

        if(financialInfo) {
          this.financialForm.patchValue({
            ...financialInfo,
            ingresosMensuales: financialInfo.ingresosMensuales || 0,
            primaProductividad: financialInfo.primaProductividad || 0,
            otrosIngresosMensuales: financialInfo.otrosIngresosMensuales || 0,
            egresosMensuales: financialInfo.egresosMensuales || 0,
            obligacionFinanciera: financialInfo.obligacionFinanciera || 0,
            otrosEgresosMensuales: financialInfo.otrosEgresosMensuales || 0
          });

          // Calcular los totales iniciales si hay datos de la base de datos
          this.updateTotalIncome();
          this.updateTotalExpense();
          this.updateTotals();
        }    
      });
      
    }

    // Subscribe to value changes of value1 and value2
    this.financialForm.get('ingresosMensuales')?.valueChanges.subscribe(() => this.updateTotalIncome());
    this.financialForm.get('otrosIngresosMensuales')?.valueChanges.subscribe(() => this.updateTotalIncome());
    this.financialForm.get('primaProductividad')?.valueChanges.subscribe(() => this.updateTotalIncome());

    this.financialForm.get('egresosMensuales')?.valueChanges.subscribe(() => this.updateTotalExpense());
    this.financialForm.get('obligacionFinanciera')?.valueChanges.subscribe(() => this.updateTotalExpense());
    this.financialForm.get('otrosEgresosMensuales')?.valueChanges.subscribe(() => this.updateTotalExpense());

    this.financialForm.get('totalIngresosMensuales')?.valueChanges.subscribe(() => this.updateTotals());
    this.financialForm.get('totalEgresosMensuales')?.valueChanges.subscribe(() => this.updateTotals());

    this.loadBankAccountTypes();
  }

  loadBankAccountTypes(): void {
    this.bankAccountTypeService.getAll().subscribe(data => {
      this.bankAccountTypes = data;
    });
  }

  updateTotalIncome(): void {
    if(this.financialInfo) {
      const income = this.financialForm.get('ingresosMensuales')?.value || 0;
      const otherIncome = this.financialForm.get('otrosIngresosMensuales')?.value || 0;
      const prod = this.financialForm.get('primaProductividad')?.value || 0;
      const totalIncome = income + otherIncome + prod;
      this.financialForm.get('totalIngresosMensuales')?.setValue(totalIncome, { emitEvent: true });
    }
    
  }

  updateTotalExpense(): void {
    if(this.financialInfo) {
      const expense = this.financialForm.get('egresosMensuales')?.value || 0;
      const oblig = this.financialForm.get('obligacionFinanciera')?.value || 0;
      const otherExpense = this.financialForm.get('otrosEgresosMensuales')?.value || 0;
      const totalExpense = expense + oblig + otherExpense;
      this.financialForm.get('totalEgresosMensuales')?.setValue(totalExpense, { emitEvent: true });
    }  
  }

  updateTotals(): void {
    if(this.financialInfo) {
      const income = this.financialForm.get('totalIngresosMensuales')?.value || 0;
      const expense = this.financialForm.get('totalEgresosMensuales')?.value || 0;
  
      const totalAssets = income - expense;
  
      this.financialForm.get('totalActivos')?.setValue(income, { emitEvent: true });
      this.financialForm.get('totalPasivos')?.setValue(expense, { emitEvent: true });
      this.financialForm.get('totalPatrimonio')?.setValue(totalAssets, { emitEvent: true });
    } 
  }

  onSubmit(): void {
    this.financialForm.get('totalIngresosMensuales')?.enable();
    this.financialForm.get('totalEgresosMensuales')?.enable();
    this.financialForm.get('totalActivos')?.enable();
    this.financialForm.get('totalPasivos')?.enable();
    this.financialForm.get('totalPatrimonio')?.enable();
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

    this.financialForm.get('totalIngresosMensuales')?.disable();
    this.financialForm.get('totalEgresosMensuales')?.disable();
    this.financialForm.get('totalActivos')?.disable();
    this.financialForm.get('totalPasivos')?.disable();
    this.financialForm.get('totalPatrimonio')?.disable();
  }
}
