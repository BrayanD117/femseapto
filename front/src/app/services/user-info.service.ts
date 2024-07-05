import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

// Environment component
import { environment } from '../../environments/environment.development';

@Injectable({
  providedIn: 'root'
})
export class UserInfoService {

  private apiUrl: string = environment.apiUrl;


  constructor(private http: HttpClient) { }

  getUserInfo(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/userInfo.php`, { withCredentials: true });
  }

  getPersona(idUsuario: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/personasnaturales.php?idUsuario=${idUsuario}`, { withCredentials: true });
  }
}
