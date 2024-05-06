import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms'; 
import { Router } from '@angular/router';
// Login Animation
import { LottieComponent, AnimationOptions } from 'ngx-lottie';
import { LoginService } from '../../services/login.service';
import { CookieService } from 'ngx-cookie-service'; 

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [LottieComponent, FormsModule],
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
          this.cookieService.set('auth_token', response.token, { expires: 1, path: '/' });
          this.router.navigate(['/welcome']);
        } else {
          console.error('Error en login:', response.message);
        }
      },
      error: (error) => {
        console.error('Error en la petición:', error);
      }
    });
  }

  options: AnimationOptions = {
    path: '../../../assets/json/WelcomePeople.json'
  };
}
