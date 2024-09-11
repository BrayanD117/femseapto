import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { UserService } from '../../../../services/user.service';
import { MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';
import { ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { JwtHelperService } from '@auth0/angular-jwt';
import { CookieService } from 'ngx-cookie-service';

@Component({
  selector: 'app-user-settings',
  standalone: true,
  imports: [ReactiveFormsModule, CommonModule, ToastModule],
  templateUrl: './user-settings.component.html',
  styleUrls: ['./user-settings.component.css'],
  providers: [MessageService]
})
export class UserSettingsComponent {
  changePasswordForm: FormGroup;
  passwordMismatch = false;
  showCurrentPassword = false;
  showNewPassword = false;
  userId = 0;

  constructor(
    private fb: FormBuilder,
    private userService: UserService,
    private messageService: MessageService,
    private router: Router,
    private jwtHelper: JwtHelperService,
    private cookieService: CookieService
  ) {
    this.changePasswordForm = this.fb.group({
      currentPassword: ['', Validators.required],
      newPassword: ['', [Validators.required, Validators.minLength(6)]],
      confirmPassword: ['', Validators.required]
    });

    const token = this.cookieService.get('auth_token');
    if (token) {
      const decodedToken = this.jwtHelper.decodeToken(token);
      this.userId = decodedToken.userId;
    }
  }

  onSubmit() {
    if (this.changePasswordForm.valid) {
      const { currentPassword, newPassword, confirmPassword } = this.changePasswordForm.value;

      if (newPassword !== confirmPassword) {
        this.passwordMismatch = true;
        this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Las contraseñas no coinciden' });
        return;
      }

      this.passwordMismatch = false;

      console.log("this.userId",this.userId)

      this.userService.changePassword(currentPassword, newPassword).subscribe({
        next: (response) => {
          this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Contraseña actualizada correctamente', life: 3000 });

          this.userService.updatePrimerIngreso(this.userId, 1).subscribe({
            next: (response) => {
              console.log('Respuesta del servidor:', response);
              this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Primer ingreso actualizado' });
              this.router.navigate(['/auth/user']);
            },
            error: (err) => {
              console.error('Error al actualizar el primer ingreso:', err);
            }
          });          
        },
        error: (error) => {
          const errorMsg = error.error?.message || 'No se pudo cambiar la contraseña';
          this.messageService.add({ severity: 'error', summary: 'Error', detail: errorMsg });
        }
      });
    }
  }

  togglePasswordVisibility(field: string) {
    if (field === 'current') {
      this.showCurrentPassword = !this.showCurrentPassword;
    } else if (field === 'new') {
      this.showNewPassword = !this.showNewPassword;
    }
  }
}
