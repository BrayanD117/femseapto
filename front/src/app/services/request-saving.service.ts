import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment.development';

@Injectable({
  providedIn: 'root'
})
export class SolicitudAhorroService {
  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getFinancialInfo(userId: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/informacionfinanciera.php?idUsuario=${userId}` , { withCredentials: true });
  }

  createSavingsRequest(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/solicitudAhorro.php`, data);
  }
}
