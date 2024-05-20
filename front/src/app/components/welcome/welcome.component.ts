import { Component, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { UserInfoService } from '../../services/user-info.service';

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

  constructor(private userInfoService: UserInfoService) { }

  ngOnInit(): void {
    this.userInfoService.getUserInfo().subscribe(
      response => {
        if (response.success) {
          this.primerNombre = response.data.primerNombre;
          this.primerApellido = response.data.primerApellido;
        } else {
          console.error('Error al obtener la información del usuario:', response.message);
        }
      },
      error => {
        console.error('Error de conexión:', error);
      }
    );
  }
}
