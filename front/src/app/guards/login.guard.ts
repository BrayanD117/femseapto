import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';
import { CookieService } from 'ngx-cookie-service';
import { JwtHelperService } from '@auth0/angular-jwt';

@Injectable({
  providedIn: 'root'
})
export class LoginGuard implements CanActivate {

  constructor(private cookieService: CookieService, private router: Router, private jwtHelper: JwtHelperService) {}

   canActivate(
    next: ActivatedRouteSnapshot,
    state: RouterStateSnapshot): boolean {
    const token = this.cookieService.get('auth_token');
    if (token) {
      try {
        if (!this.jwtHelper.isTokenExpired(token)) {
          return true;
        }
      } catch (error) {
        if (error instanceof Error) {
          console.error('Error decoding token:', error.message);
        } else {
          console.error('Unexpected error', error);
        }
      }
    }

    console.log('No valid token found, redirecting to /login...');
    this.router.navigate(['/login']).then(() => {
      console.log('Redirect successful');
    }).catch(err => {
      console.error('Redirect failed', err);
    });
    return false;
  }
}
