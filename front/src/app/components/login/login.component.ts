import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms'; 
// Login Animation
import { LottieComponent, AnimationOptions } from 'ngx-lottie';
import { LoginService } from '../../services/login.service';

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

  constructor(private loginService: LoginService) {}

  onLogin(): void {
    this.loginService.login(this.usuario, this.contrasenia).subscribe({
      next: (response) => {
        if (response.success) {
          console.log('Login exitoso:', response);
          // Aquí puedes redireccionar al usuario o hacer otra acción
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
