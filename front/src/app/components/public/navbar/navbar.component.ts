import { Component, ViewEncapsulation, OnInit, OnDestroy } from '@angular/core';
import { MenubarModule } from 'primeng/menubar';
import { Subscription } from 'rxjs';
import { Router } from '@angular/router';
import { LoginService } from '../../../services/login.service';
import { CommonModule } from '@angular/common';
import { CookieService } from 'ngx-cookie-service';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [MenubarModule, CommonModule],
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.css'],
  encapsulation: ViewEncapsulation.None,
})
export class NavbarComponent implements OnInit, OnDestroy {
  isMenuCollapsed = true;
  isAuthenticated = false;
  showLogoutModal = false;
  private authListenerSubs: Subscription | undefined;

  constructor(
    private loginService: LoginService,
    private router: Router,
    private cookieService: CookieService
  ) {}

  ngOnInit() {
    this.authListenerSubs = this.loginService
      .getAuthStatusListener()
      .subscribe((isAuthenticated) => {
        this.isAuthenticated = isAuthenticated;
      });
  }

  ngOnDestroy() {
    if (this.authListenerSubs) {
      this.authListenerSubs.unsubscribe();
    }
  }

  checkAuthentication() {
    this.isAuthenticated = !this.loginService.isTokenExpired();
  }

  openLogoutModal() {
    console.log("Modal abierto");  // Agregar el console.log aquí
    this.showLogoutModal = true;
  }

  closeLogoutModal() {
    this.showLogoutModal = false;
  }

  confirmLogout() {
    localStorage.removeItem('auth_token');
    this.cookieService.delete('auth_token');
    this.loginService.updateAuthStatus(false);
    this.router.navigate(['/login']);
    this.closeLogoutModal();
  }

  toggleMenu() {
    this.isMenuCollapsed = !this.isMenuCollapsed;
  }

  closeMenu() {
    this.isMenuCollapsed = true;
  }
}