import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { JwtHelperService } from '@auth0/angular-jwt';

@Injectable({
  providedIn: 'root',
})
export class LoginService {
  constructor(private http: HttpClient, private jwtHelper: JwtHelperService) {}

  login(usuario: string, contrasenia: string): Observable<any> {
    const url = 'http://localhost/femseapto/back/auth/login.php';
    return this.http.post(
      url,
      {
        usuario,
        contrasenia,
      },
    //   { withCredentials: true }
    );
  }

  isTokenExpired(): boolean {
    const token = localStorage.getItem('auth_token') || '';
    return this.jwtHelper.isTokenExpired(token);
  }

  getTokenClaims(): any {
    const token = localStorage.getItem('auth_token');
    return token ? this.jwtHelper.decodeToken(token) : null;
  }

}
