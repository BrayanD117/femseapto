import { Component, ViewEncapsulation, OnInit } from '@angular/core';
import { MenubarModule } from 'primeng/menubar';
import { Subscription } from 'rxjs';
import { Router } from '@angular/router';
import { LoginService } from '../../services/login.service';
import { CommonModule } from '@angular/common';
import { CookieService } from 'ngx-cookie-service';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [MenubarModule, CommonModule],
  templateUrl: './navbar.component.html',
  styleUrl: './navbar.component.css',
  encapsulation: ViewEncapsulation.None,
})
export class NavbarComponent implements OnInit {
  isMenuCollapsed = true;
  isAuthenticated = false;
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

  logout() {
    localStorage.removeItem('auth_token');
    this.cookieService.delete('auth_token');
    this.loginService.updateAuthStatus(false);
    this.router.navigate(['/login']);
  }

  toggleMenu() {
    this.isMenuCollapsed = !this.isMenuCollapsed;
  }

  closeMenu() {
    this.isMenuCollapsed = true;
  }
}