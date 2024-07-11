import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class CreditBalanceService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getByUserId(userId: number): Observable<any> {
    return this.http.get<CreditBalance[]>(`${this.apiUrl}/saldocreditos.php?idUsuario=${userId}`, { withCredentials: true });
  }
}

export interface CreditBalance {
  id: number;
  idUsuario: number;
  idLineaCredito: number;
  cuotaActual: number;
  cuotasTotales: number;
  valorSolicitado: string;
  valorPagado: string;
  valorSaldo: string;
  creadoEl: Date;
  actualizadoEl: Date;
}