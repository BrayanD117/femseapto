import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  FormBuilder,
  FormGroup,
  Validators,
  ReactiveFormsModule,
} from '@angular/forms';
import { ToastModule } from 'primeng/toast';
import { MediaService } from '../../../../../../../services/media.service';
import { UsersMediaService } from '../../../../../../../services/usersMedia.service';
import { LoginService } from '../../../../../../../services/login.service';

@Component({
  selector: 'app-contact',
  standalone: true,
  imports: [ToastModule, CommonModule, ReactiveFormsModule],
  templateUrl: './contact.component.html',
  styleUrl: './contact.component.css',
})
export class ContactComponent {
  selectForm!: FormGroup;
  options: { id: number; nombre: string }[] = [];
  selectedOptions: string[] = [];
  isLoading = true;
  userId: number | null = null;

  constructor(
    private fb: FormBuilder,
    private mediaService: MediaService,
    private usersMediaService: UsersMediaService,
    private loginService: LoginService
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

        console.log('Opciones cargadas:', this.options);

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
        const userOptions = response.map((media: any) => media.nombre);

        this.selectedOptions = userOptions.length > 0
          ? [...userOptions]
          : this.options.map((option) => option.nombre);

        this.selectForm.get('selectedOptions')?.setValue(this.selectedOptions);
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Error al cargar las opciones del usuario:', error);
        this.setDefaultOptions();
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
      const selectedIds = this.selectedOptions
        .map((option) =>
          this.options.find((o) => o.nombre === option)?.id
        )
        .filter((id): id is number => id !== undefined);

      const requestBody = {
        idUsuario: this.userId,
        idsMedios: selectedIds,
      };

      console.log('Petición a enviar:', requestBody);

      this.usersMediaService.updateMediaForUser(requestBody).subscribe({
        next: (response) => {
          console.log('Respuesta del servidor:', response);
        },
        error: (error) => {
          console.error('Error al enviar la información:', error);
        },
      });
    } else {
      console.log('Formulario inválido o falta userId.');
    }
  }
}