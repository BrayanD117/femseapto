import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

// Environment component
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class FinancialInfoService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<any> {
    return this.http.get<FinancialInformation>(`${this.apiUrl}/informacionfinanciera.php?id=${id}`, { withCredentials: true });
  }

  getByUserId(userId: number): Observable<any> {
    return this.http.get<FinancialInformation>(`${this.apiUrl}/informacionfinanciera.php?idUsuario=${userId}`, { withCredentials: true });
  }

  validate(userId: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/informacionfinanciera.php?val=${userId}`, { withCredentials: true });
  }

  create(user: FinancialInformation): Observable<FinancialInformation> {
    const url = `${this.apiUrl}/informacionfinanciera.php`;
    return this.http.post<FinancialInformation>(url, user, { withCredentials: true });
  }

  update(user: FinancialInformation): Observable<FinancialInformation> {
    const url = `${this.apiUrl}/informacionfinanciera.php`;
    return this.http.put<FinancialInformation>(url, user, { withCredentials: true });
  }
}

export interface FinancialInformation {
  id: number;
  idUsuario: number;
  nombreBanco: string;
  idTipoCuentaBanc: number;
  numeroCuentaBanc: string;
  ingresosMensuales: number;
  primaProductividad: number;
  otrosIngresosMensuales: number;
  conceptoOtrosIngresosMens: string;
  totalIngresosMensuales: number;
  egresosMensuales: number;
  obligacionFinanciera: number;
  otrosEgresosMensuales: number;
  totalEgresosMensuales: number;
  totalActivos: number;
  totalPasivos: number;
  totalPatrimonio: number;
  montoMaxAhorro: number;
  creadoEl: Date;
  actualizadoEl: Date;
}