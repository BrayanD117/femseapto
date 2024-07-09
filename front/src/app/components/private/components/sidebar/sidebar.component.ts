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
    console.log("Modal abierto");
  }

  closeLogoutModal() {
    this.showLogoutModal = false;
    console.log("Modal cerrado");
  }

  confirmLogout() {
    localStorage.removeItem('auth_token');
    this.cookieService.delete('auth_token');
    this.loginService.updateAuthStatus(false);
    this.closeLogoutModal();
    window.location.pathname = '/login';
  }
}