import { Component } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';

import { PublicPerson, PublicPersonService } from '../../../../../../../services/public-person.service';
import { LoginService } from '../../../../../../../services/login.service';

@Component({
  selector: 'app-public-person',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, ToastModule],
  providers: [MessageService],
  templateUrl: './public-person.component.html',
  styleUrl: './public-person.component.css'
})
export class PublicPersonComponent {
  publicPersonForm: FormGroup;
  userId: number | null = null;

  isSubmitting: boolean = false;

  constructor(
    private fb: FormBuilder,
    private publicPersonService: PublicPersonService,
    private loginService: LoginService,
    private messageService: MessageService
  ) {
    this.publicPersonForm = this.fb.group({
      id: [''],
      idUsuario: [''],
      poderPublico: ['', Validators.required],
      manejaRecPublicos: ['', Validators.required],
      reconocimientoPublico: ['', Validators.required],
      funcionesPublicas: ['', Validators.required],
      actividadPublica: [{ value: '', disabled: true }],
      funcionarioPublicoExtranjero: ['', Validators.required],
      famFuncionarioPublico: ['', Validators.required],
      socioFuncionarioPublico: ['', Validators.required]
    });

    this.publicPersonForm.get('funcionesPublicas')?.valueChanges.subscribe(value => {
      this.toggleFieldsPubFunc(value);
    });
  }

  ngOnInit(): void {
    this.getUserIdFromToken();
    this.loadInfo();
  }

  getUserIdFromToken(): void {
    const token = this.loginService.getTokenClaims();
    if (token) {
      this.userId = token.userId;

      this.publicPersonForm.patchValue({
        idUsuario: this.userId
      });
    }
  }

  loadInfo(): void {
    if(this.userId) {
      this.publicPersonService.getByUserId(this.userId).subscribe(data => {
        this.publicPersonForm.patchValue(data);
        this.toggleFieldsPubFunc(this.publicPersonForm.get('funcionesPublicas')?.value);
      });
    }
  }

  toggleFieldsPubFunc(value: string): void {
    const actividadPublicaControl = this.publicPersonForm.get('actividadPublica');

    if (value === 'NO' || value === '') {
      actividadPublicaControl?.setValue('');
      actividadPublicaControl?.disable();
    } else {
      actividadPublicaControl?.enable();
    }
  }

  submit(): void {
    if (this.isSubmitting) {
      return;
    }

    this.isSubmitting = true;

    if (this.publicPersonForm.valid) {
      const data: PublicPerson = this.publicPersonForm.value;
      if (data.id) {
        this.publicPersonService.update(data).subscribe({
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
        this.publicPersonService.create(data).subscribe({
          next: (response) => {
            console.log(response);
            this.publicPersonForm.patchValue({ id: response.id });
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
