import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class RequestSavingWithdrawalService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<RequestSavingWithdrawal> {
    return this.http.get<RequestSavingWithdrawal>(`${this.apiUrl}/solicitudesretiroahorro.php?id=${id}` , { withCredentials: true });
  }

  getByUserId(userId: number): Observable<RequestSavingWithdrawal> {
    return this.http.get<RequestSavingWithdrawal>(`${this.apiUrl}/solicitudesretiroahorro.php?idUsuario=${userId}` , { withCredentials: true });
  }

  getAll(params: any): Observable<any> {
    let httpParams = new HttpParams();
    Object.keys(params).forEach(key => {
      if (params[key] !== undefined && params[key] !== null) {
        httpParams = httpParams.append(key, params[key]);
      }
    });
    return this.http.get<any>(`${this.apiUrl}/solicitudesretiroahorro.php`, { params: httpParams, withCredentials: true });
  }

  create(data: RequestSavingWithdrawal): Observable<RequestSavingWithdrawal> {
    return this.http.post<RequestSavingWithdrawal>(`${this.apiUrl}/solicitudesretiroahorro.php`, data, { withCredentials: true });
  }
}

export interface RequestSavingWithdrawal {
  id: number;
  idUsuario: number;
  idLineaAhorro: number;
  montoRetirar: number;
  banco: string;
  numeroCuenta: string;
  devolucionCaja: string;
  observaciones: string;
  continuarAhorro: string;
  fechaSolicitud: Date;
}