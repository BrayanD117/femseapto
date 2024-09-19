import { Component, Input, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink } from '@angular/router';
import { LoginService } from '../../../../services/login.service';
import { CookieService } from 'ngx-cookie-service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-sidebar',
  standalone: true,
  imports: [RouterLink, CommonModule],
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.css']
})
export class SidebarComponent {
  @Input() userRole?: number;

  constructor(
    private loginService: LoginService,
    private router: Router,
    private cookieService: CookieService
  ) {}

  /*confirmLogout() {
    localStorage.removeItem('auth_token');
    this.cookieService.delete('auth_token', '/');
    this.loginService.updateAuthStatus(false);
    this.router.navigate(['/login']);
  }*/

  confirmLogout() {
    localStorage.removeItem('auth_token');
    
    this.loginService.logout().subscribe(() => {
      this.cookieService.delete('auth_token', '/');
      this.router.navigate(['/login']);
    });
  }
}