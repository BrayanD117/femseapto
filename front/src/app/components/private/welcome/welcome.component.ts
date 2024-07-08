import { Component, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { UserService } from '../../../services/user.service';
import { LoginService } from '../../../services/login.service';
import { UserInfoService } from '../../../services/user-info.service';

@Component({
  selector: 'app-welcome',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './welcome.component.html',
  styleUrls: ['./welcome.component.css']
})
export class WelcomeComponent implements OnInit {
  primerNombre: string = '';
  primerApellido: string = '';

  constructor(private userService: UserService, private loginService: LoginService, private userInfo: UserInfoService) { }

  ngOnInit(): void {
    const token = this.loginService.getTokenClaims();

    this.userService.getById(token.userId).subscribe({
      next: (response) => {
        this.primerNombre = response.primerNombre;
        this.primerApellido = response.primerApellido;

      },
      error: (error) => {
        console.error('Error al obtener la información del usuario:', error);
      }
    });

    this.userInfo.getUserInfo().subscribe({
      next: (response) => {
        console.log(response);
      },
      error: (error) => {
        console.error('Error al obtener la información del usuario:', error);
      }
    });
  }
}
