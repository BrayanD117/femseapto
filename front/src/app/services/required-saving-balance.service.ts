import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, catchError, throwError } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class RequiredSavingBalanceService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getByUserId(userId: number): Observable<RequiredSavingBalance[]> {
    return this.http.get<RequiredSavingBalance[]>(`${this.apiUrl}/saldoahorrosobligatorios.php?idUsuario=${userId}`, { withCredentials: true });
  }

  uploadData(data: any[]): Observable<any> {
    return this.http.post(`${this.apiUrl}/saldoahorrosobligatorios.php`, { data }, { withCredentials: true })
      .pipe(
        catchError(this.handleError)
      );
  }

  private handleError(error: HttpErrorResponse) {
    let errorMessage = '';
    if (error.error instanceof ErrorEvent) {
      errorMessage = `Error: ${error.error.message}`;
    } else {
      errorMessage = `Error Code: ${error.status}\nMessage: ${error.message}`;
    }
    return throwError(errorMessage);
  }
}

export interface RequiredSavingBalance {
  id: number;
  idUsuario: number;
  idLineaAhorroObligatoria: number;
  ahorroQuincenal?: number;
  valorSaldo: number;
  fechaCorte: Date;
  creadoEl: Date;
  actualizadoEl: Date;
}