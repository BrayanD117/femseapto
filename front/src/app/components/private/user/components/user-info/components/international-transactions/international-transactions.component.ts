import { Component, OnInit } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';

import { InternationalTransaction, InternationalTransactionsService } from '../../../../../../../services/international-transactions.service';
import { LoginService } from '../../../../../../../services/login.service';
import { Country, CountriesService } from '../../../../../../../services/countries.service';

@Component({
  selector: 'app-international-transactions',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, ToastModule],
  providers: [MessageService],
  templateUrl: './international-transactions.component.html',
  styleUrl: './international-transactions.component.css'
})
export class InternationalTransactionsComponent implements OnInit {
  intTransForm: FormGroup;
  intTrans!: InternationalTransaction;
  userId: number | null = null;

  countries: Country[] = [];

  constructor(
    private fb: FormBuilder,
    private interTransService: InternationalTransactionsService,
    private loginService: LoginService,
    private countryService: CountriesService,
    private messageService: MessageService
  ) {
    this.intTransForm = this.fb.group({
      id: [''],
      transaccionesMonedaExtranjera: ['', Validators.required],
      transMonedaExtranjera: [{ value: '', disabled: true }],
      otrasOperaciones: [{ value: '', disabled: true }],
      cuentasMonedaExtranjera: ['', Validators.required],
      bancoCuentaExtranjera: [{ value: '', disabled: true }],
      cuentaMonedaExtranjera: [{ value: '', disabled: true }],
      monedaCuenta: [{ value: '', disabled: true }],
      idPaisCuenta: [{ value: '', disabled: true }],
      ciudadCuenta: [{ value: '', disabled: true }]
    });
  }

  ngOnInit(): void {
    this.getUserIdFromToken();
    this.loadInternatTrans();
    this.loadCountries();
  }

  getUserIdFromToken(): void {
    const token = this.loginService.getTokenClaims();
    if (token) {
      this.userId = token.userId;
    }
  }

  loadInternatTrans(): void {
    if(this.userId) {
      this.interTransService.getByUserId(this.userId).subscribe(data => {
        this.intTrans = data;
        this.intTransForm.patchValue(data);
        this.updateFormState(data);
      });
    }
  }

  loadCountries(): void {
    this.countryService.getAll().subscribe(data => {
      this.countries = data;
    });
  }

  onTransaccionesChange(event: any): void {
    const value = event.target.value;
    if (value === 'NO' || value === '') {
      this.intTransForm.get('transMonedaExtranjera')!.disable();
      this.intTransForm.get('transMonedaExtranjera')!.setValue('');
      this.intTransForm.get('otrasOperaciones')!.disable();
      this.intTransForm.get('otrasOperaciones')!.setValue('');
    } else {
      this.intTransForm.get('transMonedaExtranjera')!.enable();
      this.intTransForm.get('otrasOperaciones')!.enable(); 
    }
  }

  onCuentasChange(event: any): void {
    const value = event.target.value;
    if (value === 'NO' || value === '') {
      this.intTransForm.get('bancoCuentaExtranjera')!.disable();
      this.intTransForm.get('bancoCuentaExtranjera')!.setValue('');
      this.intTransForm.get('cuentaMonedaExtranjera')!.disable();
      this.intTransForm.get('cuentaMonedaExtranjera')!.setValue('');
      this.intTransForm.get('monedaCuenta')!.disable();
      this.intTransForm.get('monedaCuenta')!.setValue('');
      this.intTransForm.get('idPaisCuenta')!.disable();
      this.intTransForm.get('idPaisCuenta')!.setValue('');
      this.intTransForm.get('ciudadCuenta')!.disable();
      this.intTransForm.get('ciudadCuenta')!.setValue('');
    } else {
      this.intTransForm.get('bancoCuentaExtranjera')!.enable();
      this.intTransForm.get('cuentaMonedaExtranjera')!.enable();
      this.intTransForm.get('monedaCuenta')!.enable();
      this.intTransForm.get('idPaisCuenta')!.enable();
      this.intTransForm.get('ciudadCuenta')!.enable();
    }
  }

  updateFormState(data: any): void {
    const transaccionesMonedaExtranjera = data.transaccionesMonedaExtranjera || '';
    const cuentasMonedaExtranjera = data.cuentasMonedaExtranjera || '';

    if (transaccionesMonedaExtranjera === 'NO' || transaccionesMonedaExtranjera === '') {
      this.intTransForm.get('transMonedaExtranjera')!.disable();
      this.intTransForm.get('otrasOperaciones')!.disable();
    } else {
      this.intTransForm.get('transMonedaExtranjera')!.enable();
      this.intTransForm.get('otrasOperaciones')!.enable();
    }

    if (cuentasMonedaExtranjera === 'NO' || cuentasMonedaExtranjera === '') {
      this.intTransForm.get('bancoCuentaExtranjera')!.disable();
      this.intTransForm.get('cuentaMonedaExtranjera')!.disable();
      this.intTransForm.get('monedaCuenta')!.disable();
      this.intTransForm.get('idPaisCuenta')!.disable();
      this.intTransForm.get('ciudadCuenta')!.disable();
    } else {
      this.intTransForm.get('bancoCuentaExtranjera')!.enable();
      this.intTransForm.get('cuentaMonedaExtranjera')!.enable();
      this.intTransForm.get('monedaCuenta')!.enable();
      this.intTransForm.get('idPaisCuenta')!.enable();
      this.intTransForm.get('ciudadCuenta')!.enable();
    }
  }

  submit(): void {
    console.log(this.intTrans);
    console.log(this.intTransForm);
    if (this.intTransForm.valid) {
      const data = { ...this.intTrans, ...this.intTransForm.value };
      if (data.id) {
        this.interTransService.update(data).subscribe({
          next: () => {
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Información actualizada correctamente' });
          },
          error: (err) => {
            console.error('Error al actualizar la información', err);
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo actualizar la información. Vuelve a intentarlo' });
          }
        });
      } else {
        this.interTransService.create(data).subscribe({
          next: () => {
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Información creada correctamente' });
          },
          error: (err) => {
            console.error('Error al crear la información', err);
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo crear la información' });
          }
        });
      }
    }
  }
}
