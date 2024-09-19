import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms'; 
import { Router } from '@angular/router';
// Login Animation
import { LottieComponent, AnimationOptions } from 'ngx-lottie';
import { RouterLink } from '@angular/router';
import { JwtHelperService } from '@auth0/angular-jwt';
import { LoginService } from '../../../services/login.service';
import { CookieService } from 'ngx-cookie-service';
import { MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [LottieComponent, FormsModule, RouterLink, ToastModule],
  providers: [MessageService],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent {

  usuario: string = '';
  contrasenia: string = '';

  constructor(private loginService: LoginService, private cookieService: CookieService,  private router: Router, private jwtHelper: JwtHelperService,
    private messageService: MessageService,
  ) {}

  onLogin(): void {
    this.loginService.login(this.usuario, this.contrasenia).subscribe({
      next: (response) => {
        if (response.success) {
          localStorage.setItem('auth_token', response.token);
          this.cookieService.set('auth_token', response.token, { expires: (1 / 24), path: '/' });
  
          const decodedToken = this.jwtHelper.decodeToken(response.token);
          const rol = decodedToken.id_rol;
          if (response.primer_ingreso === 0) {
            this.router.navigate(['/auth/settings']);
          } else {
            if (rol === 1) {
              this.router.navigate(['/auth/admin']);
            } else if (rol === 3) {
              this.router.navigate(['/auth/executive']);
            } else {
              this.router.navigate(['/auth/user']);
            }
          }
        } else {
          this.messageService.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Usuario o contraseña incorrecta. Vuelve a intentarlo.',
          });
        }
      },
      error: (error) => {
        console.error('Error en la petición:', error);
      },
    });
  }  
  

  options: AnimationOptions = {
    path: '../../../assets/json/WelcomePeople.json',
  };
}
