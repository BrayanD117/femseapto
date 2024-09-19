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
export class SidebarComponent implements OnInit, OnDestroy {
  @Input() userRole?: number;
  private authListenerSubs: Subscription | undefined;
  isAuthenticated = false;

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

  confirmLogout() {
    localStorage.removeItem('auth_token');
    this.cookieService.delete('auth_token');
    
    if (!this.cookieService.check('auth_token')) {
      this.loginService.updateAuthStatus(false);
      this.router.navigate(['/login']);
    } else {
      console.error('Error al eliminar la cookie');
    }
  }

  handleLogout() {
    this.confirmLogout();
    this.closeModal();
  }

  closeModal() {
    const modalElement = document.getElementById('logoutModal');
    if (modalElement) {
      const modalInstance = new (window as any).bootstrap.Modal(modalElement);
      modalInstance.hide();
    }
  }
}
