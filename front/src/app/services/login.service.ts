import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class LoginService {
  constructor(private http: HttpClient) {}

  login(usuario: string, contrasenia: string): Observable<any> {
    const url = 'http://localhost/femseapto/back/auth/login.php'; // Ajusta esta URL al entorno de tu backend
    return this.http.post(
      url,
      {
        usuario,
        contrasenia,
      },
      //{ withCredentials: true }
    );
  }
}
