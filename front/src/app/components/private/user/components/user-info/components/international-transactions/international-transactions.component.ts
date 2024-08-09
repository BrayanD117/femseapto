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
  userId: number | null = null;

  countries: Country[] = [];

  isSubmitting: boolean = false;

  constructor(
    private fb: FormBuilder,
    private interTransService: InternationalTransactionsService,
    private loginService: LoginService,
    private countryService: CountriesService,
    private messageService: MessageService
  ) {
    this.intTransForm = this.fb.group({
      id: [''],
      idUsuario: [''],
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

    this.intTransForm.get('transaccionesMonedaExtranjera')?.valueChanges.subscribe(value => {
      this.toggleFieldsTrans(value);
    });

    this.intTransForm.get('cuentasMonedaExtranjera')?.valueChanges.subscribe(value => {
      this.toggleFieldsAccounts(value);
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

      this.intTransForm.patchValue({
        idUsuario: this.userId
      });
    }
  }

  loadInternatTrans(): void {
    if(this.userId) {
      this.interTransService.getByUserId(this.userId).subscribe(data => {
        this.intTransForm.patchValue(data);
        this.toggleFieldsTrans(this.intTransForm.get('transaccionesMonedaExtranjera')?.value);
        this.toggleFieldsAccounts(this.intTransForm.get('cuentasMonedaExtranjera')?.value);
      });
    }
  }

  loadCountries(): void {
    this.countryService.getAll().subscribe(data => {
      this.countries = data;
    });
  }

  toggleFieldsTrans(value: string): void {
    const transMonedaExtranjeraControl = this.intTransForm.get('transMonedaExtranjera');
    const otrasOperacionesControl = this.intTransForm.get('otrasOperaciones');

    if (value === 'NO' || value === '') {
      transMonedaExtranjeraControl?.setValue('');
      transMonedaExtranjeraControl?.disable();
      otrasOperacionesControl?.setValue('');
      otrasOperacionesControl?.disable();
    } else {
      transMonedaExtranjeraControl?.enable();
      otrasOperacionesControl?.enable();
    }
  }

  toggleFieldsAccounts(value: string): void {
    const bancoCuentaExtranjeraControl = this.intTransForm.get('bancoCuentaExtranjera');
    const cuentaMonedaExtranjeraControl = this.intTransForm.get('cuentaMonedaExtranjera');
    const monedaCuentaControl = this.intTransForm.get('monedaCuenta');
    const idPaisCuentaControl = this.intTransForm.get('idPaisCuenta');
    const ciudadCuentaControl = this.intTransForm.get('ciudadCuenta');

    if (value === 'NO' || value === '') {
      bancoCuentaExtranjeraControl?.setValue('');
      bancoCuentaExtranjeraControl?.disable();
      cuentaMonedaExtranjeraControl?.setValue('');
      cuentaMonedaExtranjeraControl?.disable();
      monedaCuentaControl?.setValue('');
      monedaCuentaControl?.disable();
      idPaisCuentaControl?.setValue('');
      idPaisCuentaControl?.disable();
      ciudadCuentaControl?.setValue('');
      ciudadCuentaControl?.disable();
    } else {
      bancoCuentaExtranjeraControl?.enable();
      cuentaMonedaExtranjeraControl?.enable();
      monedaCuentaControl?.enable();
      idPaisCuentaControl?.enable();
      ciudadCuentaControl?.enable();
    }
  }

  submit(): void {
    if (this.isSubmitting) {
      return;
    }

    this.isSubmitting = true;
    
    if (this.intTransForm.valid) {
      const data: InternationalTransaction = this.intTransForm.value;
      if (data.id) {
        this.interTransService.update(data).subscribe({
          next: () => {
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Información actualizada correctamente' });
            setTimeout(() => {
              this.isSubmitting = false;
            }, 500);
          },
          error: (err) => {
            console.error('Error al actualizar la información', err);
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo actualizar la información. Vuelve a intentarlo' });
            setTimeout(() => {
              this.isSubmitting = false;
            }, 500);
          }
        });
      } else {
        this.interTransService.create(data).subscribe({
          next: (response) => {
            //console.log(response);
            this.intTransForm.patchValue({ id: response.id });
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Información creada correctamente' });
            setTimeout(() => {
              this.isSubmitting = false;
            }, 500);
          },
          error: (err) => {
            console.error('Error al crear la información', err);
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo crear la información' });
            setTimeout(() => {
              this.isSubmitting = false;
            }, 500);
          }
        });
      }
    }
  }
}