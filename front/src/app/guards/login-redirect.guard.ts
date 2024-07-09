import { Injectable } from '@angular/core';
import { CanActivate, Router } from '@angular/router';
import { CookieService } from 'ngx-cookie-service';
import { JwtHelperService } from '@auth0/angular-jwt';

@Injectable({
  providedIn: 'root'
})
export class LoginRedirectGuard implements CanActivate {

  constructor(private cookieService: CookieService, private router: Router, private jwtHelper: JwtHelperService) {}

  canActivate(): boolean {
    const token = this.cookieService.get('auth_token');
    if (token && !this.jwtHelper.isTokenExpired(token)) {
      const decodedToken = this.jwtHelper.decodeToken(token);
      if (decodedToken.id_rol === 1) {
        this.router.navigate(['/auth/admin']);
      } else if (decodedToken.id_rol === 2) {
        this.router.navigate(['/auth/user']);
      }
      return false;
    }
    return true;
  }
}
