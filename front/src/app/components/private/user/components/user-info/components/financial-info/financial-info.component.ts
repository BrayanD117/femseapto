import { Component, Input, OnInit } from '@angular/core';
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
  @Input() actualizarPerfilFecha: boolean = false;

  financialForm: FormGroup;
  userId: number | null = null;
  financialInfo: FinancialInformation | null = null;

  bankAccountTypes: BankAccountType[] = [];
  
  isSubmitting: boolean = false;

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
      totalActivos: [0, Validators.required],
      totalPasivos: [0, Validators.required],
      totalPatrimonio: [{ value: 0, disabled: true }],
      actualizarPerfilFecha: [false]
    });
  }

  ngOnInit(): void {
    const token = this.loginService.getTokenClaims();

    if (token) {
      this.userId = token.userId;
      //console.log("ENTRA", this.actualizarPerfilFecha);

      this.financialForm.patchValue({
        idUsuario: this.userId,
        actualizarPerfilFecha: this.actualizarPerfilFecha
      });

      this.financialInfoService.getByUserId(token.userId).subscribe(financialInfo => {
        this.financialInfo = financialInfo;

        if(financialInfo) {
          this.financialForm.patchValue({
            ...financialInfo,
            ingresosMensuales: this.formatToCurrency(parseInt(financialInfo.ingresosMensuales, 10)),
            primaProductividad: this.formatToCurrency(parseInt(financialInfo.primaProductividad, 10)),
            otrosIngresosMensuales: this.formatToCurrency(parseInt(financialInfo.otrosIngresosMensuales, 10)),
            totalIngresosMensuales: this.formatToCurrency(parseInt(financialInfo.totalIngresosMensuales, 10)),
            egresosMensuales: this.formatToCurrency(parseInt(financialInfo.egresosMensuales, 10)),
            obligacionFinanciera: this.formatToCurrency(parseInt(financialInfo.obligacionFinanciera, 10)),
            otrosEgresosMensuales: this.formatToCurrency(parseInt(financialInfo.otrosEgresosMensuales, 10)),
            totalEgresosMensuales: this.formatToCurrency(parseInt(financialInfo.totalEgresosMensuales, 10)),
            totalActivos: this.formatToCurrency(parseInt(financialInfo.totalActivos, 10)),
            totalPasivos: this.formatToCurrency(parseInt(financialInfo.totalPasivos, 10)),
            totalPatrimonio: this.formatToCurrency(parseInt(financialInfo.totalPatrimonio, 10))
          });
        }    
      });  
    }

    this.financialForm.get('ingresosMensuales')?.valueChanges.subscribe(() => this.updateTotalIncome());
    this.financialForm.get('otrosIngresosMensuales')?.valueChanges.subscribe(() => this.updateTotalIncome());
    this.financialForm.get('primaProductividad')?.valueChanges.subscribe(() => this.updateTotalIncome());

    this.financialForm.get('egresosMensuales')?.valueChanges.subscribe(() => this.updateTotalExpense());
    this.financialForm.get('obligacionFinanciera')?.valueChanges.subscribe(() => this.updateTotalExpense());
    this.financialForm.get('otrosEgresosMensuales')?.valueChanges.subscribe(() => this.updateTotalExpense());

    this.financialForm.get('totalActivos')?.valueChanges.subscribe(() => this.updateTotals());
    this.financialForm.get('totalPasivos')?.valueChanges.subscribe(() => this.updateTotals());

    this.loadBankAccountTypes();
  }

  loadBankAccountTypes(): void {
    this.bankAccountTypeService.getAll().subscribe(data => {
      this.bankAccountTypes = data;
    });
  }

  updateTotalIncome(): void {
      //const income = this.financialForm.get('ingresosMensuales')?.value || 0;
      //const otherIncome = this.financialForm.get('otrosIngresosMensuales')?.value || 0;
      //const prod = this.financialForm.get('primaProductividad')?.value || 0;
      const income = parseInt(this.financialForm.get('ingresosMensuales')?.value.toString().replace(/\./g, ''), 10);
      const otherIncome = parseInt(this.financialForm.get('otrosIngresosMensuales')?.value.toString().replace(/\./g, ''), 10);
      const prod = parseInt(this.financialForm.get('primaProductividad')?.value.toString().replace(/\./g, ''), 10);
      const totalIncome = income + otherIncome + prod;
      this.financialForm.get('totalIngresosMensuales')?.setValue(this.formatToCurrency(totalIncome), { emitEvent: true }); 
  }

  updateTotalExpense(): void {
      //const expense = this.financialForm.get('egresosMensuales')?.value || 0;
      //const oblig = this.financialForm.get('obligacionFinanciera')?.value || 0;
      //const otherExpense = this.financialForm.get('otrosEgresosMensuales')?.value || 0;
      const expense = parseInt(this.financialForm.get('egresosMensuales')?.value.toString().replace(/\./g, ''), 10);
      const oblig = parseInt(this.financialForm.get('obligacionFinanciera')?.value.toString().replace(/\./g, ''), 10);
      const otherExpense = parseInt(this.financialForm.get('otrosEgresosMensuales')?.value.toString().replace(/\./g, ''), 10);

      const totalExpense = expense + oblig + otherExpense;
      this.financialForm.get('totalEgresosMensuales')?.setValue(this.formatToCurrency(totalExpense), { emitEvent: true }); 
  }

  updateTotals(): void {
    //const income = this.financialForm.get('totalActivos')?.value || 0;
    //const expense = this.financialForm.get('totalPasivos')?.value || 0;
    const income = parseInt(this.financialForm.get('totalActivos')?.value.toString().replace(/\./g, ''), 10);
    const expense = parseInt(this.financialForm.get('totalPasivos')?.value.toString().replace(/\./g, ''), 10);

    const totalAssets = income - expense;

    this.financialForm.get('totalPatrimonio')?.setValue(this.formatToCurrency(totalAssets), { emitEvent: true });
  }

  formatCurrency(controlName: string): void {
    const control = this.financialForm.get(controlName);
    if (control) {
      const value = control.value.replace(/\D/g, '');
      control.setValue(this.formatToCurrency(parseInt(value, 10)), { emitEvent: false });
    }
  }

  formatToCurrency(value: number): string {
    if (!value) return '0';
    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  onSubmit(): void {
    if (this.isSubmitting) {
      return;
    }

    this.isSubmitting = true;

    //this.financialForm.get('totalIngresosMensuales')?.enable();
    //this.financialForm.get('totalEgresosMensuales')?.enable();
    //this.financialForm.get('totalPatrimonio')?.enable();

    if (this.financialForm.valid) {      
      const formData = this.financialForm.getRawValue();
      //console.log("FORM: ", formData);

      const parsedData = { ...formData };

      parsedData.ingresosMensuales = parseInt(this.financialForm.get('ingresosMensuales')?.value.replace(/\./g, ''), 10);
      parsedData.primaProductividad = parseInt(this.financialForm.get('primaProductividad')?.value.replace(/\./g, ''), 10);
      parsedData.otrosIngresosMensuales = parseInt(this.financialForm.get('otrosIngresosMensuales')?.value.replace(/\./g, ''), 10);
      parsedData.totalIngresosMensuales = parseInt(this.financialForm.get('totalIngresosMensuales')?.value.replace(/\./g, ''), 10);
      parsedData.egresosMensuales = parseInt(this.financialForm.get('egresosMensuales')?.value.replace(/\./g, ''), 10);
      parsedData.obligacionFinanciera = parseInt(this.financialForm.get('obligacionFinanciera')?.value.replace(/\./g, ''), 10);
      parsedData.otrosEgresosMensuales = parseInt(this.financialForm.get('otrosEgresosMensuales')?.value.replace(/\./g, ''), 10);
      parsedData.totalEgresosMensuales = parseInt(this.financialForm.get('totalEgresosMensuales')?.value.replace(/\./g, ''), 10);
      parsedData.totalActivos = parseInt(this.financialForm.get('totalActivos')?.value.replace(/\./g, ''), 10);
      parsedData.totalPasivos = parseInt(this.financialForm.get('totalPasivos')?.value.replace(/\./g, ''), 10);
      parsedData.totalPatrimonio = parseInt(this.financialForm.get('totalPatrimonio')?.value.replace(/\./g, ''), 10);

      if(parsedData.id) {
        this.financialInfoService.update(parsedData).subscribe({
          next: () => {
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Información financiera actualizada correctamente' });
            this.resetSubmitState();
          },
          error: (err) => {
            console.error('Error al actualizar la información financiera', err);
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo actualizar la información financiera. Vuelve a intentarlo.' });
            this.resetSubmitState();
          }
        });
      } else {
        this.financialInfoService.create(parsedData).subscribe({
          next: (response) => {
            //console.log(response);
            this.financialForm.patchValue({ id: response.id });
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Información financiera creada correctamente' });
            this.resetSubmitState();
          },
          error: (err) => {
            console.error('Error al actualizar la información financiera', err);
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo crear la información financiera. Vuelve a intentarlo.' });
            this.resetSubmitState();
          }
        });
      }     
    } else {
      this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Vuelve a iniciar sesión e inténtalo de nuevo.' });
      this.resetSubmitState();
    }
  }

  private resetSubmitState(): void {
    setTimeout(() => {
      this.isSubmitting = false;
    }, 500);
  }
}