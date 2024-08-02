import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, catchError, throwError } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class SavingBalanceService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getByUserId(userId: number): Observable<SavingBalance[]> {
    return this.http.get<SavingBalance[]>(`${this.apiUrl}/saldoahorros.php?idUsuario=${userId}`, { withCredentials: true });
  }

  uploadData(data: any[]): Observable<any> {
    return this.http.post(`${this.apiUrl}/saldoahorros.php`, { data }, { withCredentials: true })
      .pipe(
        catchError(this.handleError)
      );
  }

  private handleError(error: HttpErrorResponse) {
    let errorMessage = '';
    if (error.error instanceof ErrorEvent) {
      // Error en el cliente
      errorMessage = `Error: ${error.error.message}`;
    } else {
      // Error en el servidor
      errorMessage = `Error Code: ${error.status}\nMessage: ${error.message}`;
    }
    return throwError(errorMessage);
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