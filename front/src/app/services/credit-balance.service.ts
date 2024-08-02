import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class CreditBalanceService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getByUserId(userId: number): Observable<any> {
    return this.http.get<CreditBalance[]>(`${this.apiUrl}/saldocreditos.php?idUsuario=${userId}`, { withCredentials: true });
  }

  uploadData(data: any[]): Observable<any> {
    return this.http.post(`${this.apiUrl}/saldocreditos.php`, { data }, { withCredentials: true })
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
