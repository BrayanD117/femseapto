import { Component, OnInit } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { SidebarComponent } from './components/sidebar/sidebar.component';
import { LoginService } from '../../services/login.service';

@Component({
  selector: 'app-private',
  standalone: true,
  imports: [RouterOutlet, SidebarComponent],
  templateUrl: './private.component.html',
  styleUrls: ['./private.component.css']
})
export class PrivateComponent implements OnInit {
  userRole: number = 0;

  constructor(private loginService: LoginService) {}

  ngOnInit() {
    const tokenClaims = this.loginService.getTokenClaims();
    if (tokenClaims) {
      this.userRole = tokenClaims.id_rol;
    } else {
      console.error('No se pudo obtener el rol del usuario');
    }
  }
}
