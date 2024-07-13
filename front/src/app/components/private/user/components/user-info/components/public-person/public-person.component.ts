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
  publicPerson!: PublicPerson;
  userId: number | null = null;

  constructor(
    private fb: FormBuilder,
    private publicPersonService: PublicPersonService,
    private loginService: LoginService,
    private messageService: MessageService
  ) {
    this.publicPersonForm = this.fb.group({
      id: [''],
      poderPublico: ['', Validators.required],
      manejaRecPublicos: ['', Validators.required],
      reconocimientoPublico: ['', Validators.required],
      funcionesPublicas: ['', Validators.required],
      actividadPublica: [{ value: '', disabled: true }],
      funcionarioPublicoExtranjero: ['', Validators.required],
      famFuncionarioPublico: ['', Validators.required],
      socioFuncionarioPublico: ['', Validators.required]
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
    }
  }

  loadInfo(): void {
    if(this.userId) {
      this.publicPersonService.getByUserId(this.userId).subscribe(data => {
        this.publicPerson = data;
        this.publicPersonForm.patchValue(data);
        this.updateFormState(data);
      });
    }
  }

  onPublicFunctionsChange(): void {
    const value = this.publicPersonForm.get('funcionesPublicas')!.value;
    if (value === 'NO' || value === '') {
      this.publicPersonForm.get('actividadPublica')!.disable();
      this.publicPersonForm.get('actividadPublica')!.setValue('');
    } else {
      this.publicPersonForm.get('actividadPublica')!.enable();
    }
  }

  updateFormState(data: any): void {
    const publicFunctions = data.funcionesPublicas || '';

    if (publicFunctions === 'NO' || publicFunctions === '') {
      this.publicPersonForm.get('actividadPublica')!.disable();
    } else {
      this.publicPersonForm.get('actividadPublica')!.enable();
    }
  }

  submit(): void {
    console.log(this.publicPerson);
    console.log(this.publicPersonForm);
    if (this.publicPersonForm.valid) {
      const data = { ...this.publicPerson, ...this.publicPersonForm.value };
      if (data.id) {
        this.publicPersonService.update(data).subscribe({
          next: () => {
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Información actualizada correctamente' });
          },
          error: (err) => {
            console.error('Error al actualizar la información', err);
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo actualizar la información. Vuelve a intentarlo' });
          }
        });
      } else {
        this.publicPersonService.create(data).subscribe({
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
