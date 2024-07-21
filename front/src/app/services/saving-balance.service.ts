import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class SavingBalanceService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getByUserId(userId: number): Observable<SavingBalance[]> {
    return this.http.get<SavingBalance[]>(`${this.apiUrl}/saldoahorros.php?idUsuario=${userId}`, { withCredentials: true });
  }
}

export interface SavingBalance {
  id: number;
  idUsuario: number;
  idLineaAhorro: number;
  valorSaldo: number;
  creadoEl: Date;
  actualizadoEl: Date;
}