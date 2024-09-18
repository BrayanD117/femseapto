import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink } from '@angular/router';
import { LoginService } from '../../../../services/login.service';
import { CookieService } from 'ngx-cookie-service';

@Component({
  selector: 'app-sidebar',
  standalone: true,
  imports: [RouterLink, CommonModule],
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.css']
})
export class SidebarComponent {
  @Input() userRole?: number;
  showLogoutModal = false;

  constructor(
    private loginService: LoginService,
    private router: Router,
    private cookieService: CookieService
  ) {}

  openLogoutModal() {
    this.showLogoutModal = true;
  }

  closeLogoutModal() {
    this.showLogoutModal = false;
  }

  confirmLogout() {
    localStorage.removeItem('auth_token');
    this.cookieService.delete('auth_token');
    this.loginService.updateAuthStatus(false);
    //this.closeLogoutModal();
    this.router.navigate(['/login']).then(() => {
      console.log('Redirección exitosa');
    }).catch(err => {
      console.error('Error al redirigir:', err);
    });
  }
}
