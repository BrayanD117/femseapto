import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms'; 
import { Router } from '@angular/router';
// Login Animation
import { LottieComponent, AnimationOptions } from 'ngx-lottie';
import { RouterLink } from '@angular/router';
import { JwtHelperService, JWT_OPTIONS } from '@auth0/angular-jwt';
import { LoginService } from '../../../services/login.service';
import { CookieService } from 'ngx-cookie-service'; 

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [LottieComponent, FormsModule, RouterLink],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent {

  usuario: string = '';
  contrasenia: string = '';

  constructor(private loginService: LoginService, private cookieService: CookieService,  private router: Router) {}

  onLogin(): void {
    this.loginService.login(this.usuario, this.contrasenia).subscribe({
      next: (response) => {
        if (response.success) {
          console.log('Login exitoso:', response);
          localStorage.setItem('auth_token', response.token);
          this.cookieService.set('auth_token', response.token, {
            expires: 1,
            path: '/',
          });
          if (!this.loginService.isTokenExpired()) {
            window.location.href = 'auth/user/welcome'
          } else {
            console.error('Token ha expirado');
            this.router.navigate(['/login']);
          }
        } else {
          console.error('Error en login:', response.message);
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
