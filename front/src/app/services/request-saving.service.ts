import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

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
    return this.http.post(`${this.apiUrl}/solicitudesahorro.php`, data, { withCredentials: true });
  }

  getSavingLines(): Observable<any> {
    return this.http.get(`${this.apiUrl}/lineasAhorro.php`, { withCredentials: true });
  }

  getAll(params: any): Observable<any> {
    let httpParams = new HttpParams();
    Object.keys(params).forEach(key => {
      if (params[key] !== undefined && params[key] !== null) {
        httpParams = httpParams.append(key, params[key]);
      }
    });
    return this.http.get<any>(`${this.apiUrl}/solicitudesahorro.php`, { params: httpParams, withCredentials: true });
  }

  getById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/solicitudesahorro.php?id=${id}`, { withCredentials: true });
  }
}