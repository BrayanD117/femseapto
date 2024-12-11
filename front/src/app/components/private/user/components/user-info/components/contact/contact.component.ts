import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  FormBuilder,
  FormGroup,
  Validators,
  ReactiveFormsModule,
} from '@angular/forms';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { MediaService } from '../../../../../../../services/media.service';
import { UsersMediaService } from '../../../../../../../services/usersMedia.service';
import { LoginService } from '../../../../../../../services/login.service';

@Component({
  selector: 'app-contact',
  standalone: true,
  imports: [ToastModule, CommonModule, ReactiveFormsModule],
  providers: [MessageService],
  templateUrl: './contact.component.html',
  styleUrl: './contact.component.css',
})
export class ContactComponent {
  selectForm!: FormGroup;
  options: { id: number; nombre: string }[] = [];
  selectedOptions: string[] = [];
  isLoading = true;
  isSubmitting = false;
  userId: number | null = null;
  showFirstTimeNotification = false;

  constructor(
    private fb: FormBuilder,
    private mediaService: MediaService,
    private usersMediaService: UsersMediaService,
    private loginService: LoginService,
    private messageService: MessageService
  ) {}

  ngOnInit(): void {
    this.getUserIdFromToken();
    this.selectForm = this.fb.group({
      selectedOptions: [[], [this.validateSelection]],
    });
    this.loadOptions();
  }

  getUserIdFromToken(): void {
    const token = this.loginService.getTokenClaims();
    if (token) {
      this.userId = token.userId;
    }
  }

  loadOptions(): void {
    if (!this.userId) {
      console.error('El userId no está disponible.');
      this.isLoading = false;
      return;
    }

    this.mediaService.getMedia().subscribe({
      next: (response) => {
        this.options = response.map((media: any) => ({
          id: media.id,
          nombre: media.nombre,
        }));

        this.loadUserMedia();
      },
      error: (error) => {
        console.error('Error al cargar las opciones:', error);
        this.isLoading = false;
      },
    });
  }

  loadUserMedia(): void {
    if (!this.userId) {
      this.setDefaultOptions();
      return;
    }

    this.usersMediaService.getMediaByUser(this.userId).subscribe({
      next: (response) => {
        const selectedIds = response.map(
          (media: any) => media.idMedioComunicacion
        );
        this.selectedOptions = this.options
          .filter((option) => selectedIds.includes(option.id))
          .map((option) => option.nombre);

        this.selectForm.get('selectedOptions')?.setValue(this.selectedOptions);
        this.isLoading = false;
      },
      error: (error) => {
        if (error.status === 404) {
          console.warn('No se encontraron registros para el usuario.');
          this.setDefaultOptions();

          this.showFirstTimeNotification = true;
          this.messageService.add({
            closable: false,
            severity: 'warn',
            summary: 'Atención',
            detail:
              'Por favor, actualice la información de contacto requerida por la Ley 2300.',
            sticky: true,
          });
        } else {
          console.error('Error al cargar las opciones del usuario:', error);
        }
      },
    });
  }

  setDefaultOptions(): void {
    this.selectedOptions = this.options.map((option) => option.nombre);
    this.selectForm.get('selectedOptions')?.setValue(this.selectedOptions);
    this.isLoading = false;
  }

  onCheckboxChange(event: any): void {
    const value = event.target.value;
    if (event.target.checked) {
      if (!this.selectedOptions.includes(value)) {
        this.selectedOptions.push(value);
      }
    } else {
      this.selectedOptions = this.selectedOptions.filter(
        (option) => option !== value
      );
    }
    this.selectForm.get('selectedOptions')?.setValue(this.selectedOptions);
    this.selectForm.get('selectedOptions')?.updateValueAndValidity();
  }

  validateSelection(control: any): { [key: string]: any } | null {
    const value = control.value || [];
    if (value.length < 2) {
      return { minSelection: true };
    } else if (value.length > 5) {
      return { maxSelection: true };
    }
    return null;
  }

  onSubmit(): void {
    if (this.selectForm.valid && this.userId !== null) {
      this.isSubmitting = true;
      const selectedIds = this.selectedOptions
        .map((option) => this.options.find((o) => o.nombre === option)?.id)
        .filter((id): id is number => id !== undefined);

      const requestBody = {
        idUsuario: this.userId,
        idsMedios: selectedIds,
      };

      this.usersMediaService.updateMediaForUser(requestBody).subscribe({
        next: () => {
          this.messageService.add({
            severity: 'success',
            summary: 'Guardado Exitoso',
            detail: 'Las opciones seleccionadas se han guardado correctamente.',
          });
          this.isSubmitting = false;
          this.showFirstTimeNotification = false;
          setTimeout(() => {
            this.messageService.clear();
          }, 3000);
        },
        error: (error) => {
          this.messageService.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Ocurrió un error al guardar las opciones seleccionadas.',
          });
          console.error('Error al enviar la información:', error);
          this.isSubmitting = false;
        },
      });
    } else {
      console.log('Formulario inválido o falta userId.');
      this.isSubmitting = false;
    }
  }
}