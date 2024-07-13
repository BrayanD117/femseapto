import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class InternationalTransactionsService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<any> {
    return this.http.get<InternationalTransaction>(`${this.apiUrl}/operacionesinternacionales.php?id=${id}`, { withCredentials: true });
  }

  getByUserId(userId: number): Observable<any> {
    return this.http.get<InternationalTransaction>(`${this.apiUrl}/operacionesinternacionales.php?idUsuario=${userId}`, { withCredentials: true });
  }

  getAll(): Observable<any> {
    return this.http.get<InternationalTransaction[]>(`${this.apiUrl}/operacionesinternacionales.php`, { withCredentials: true });
  }

  create(intTrans: InternationalTransaction): Observable<InternationalTransaction> {
    const url = `${this.apiUrl}/operacionesinternacionales.php`;
    return this.http.post<InternationalTransaction>(url, intTrans, { withCredentials: true });
  }

  update(intTrans: InternationalTransaction): Observable<InternationalTransaction> {
    const url = `${this.apiUrl}/operacionesinternacionales.php`;
    return this.http.put<InternationalTransaction>(url, intTrans, { withCredentials: true });
  }
}

export interface InternationalTransaction {
  id: number;
  idUsuario: number;
  transaccionesMonedaExtranjera: number;
  transMonedaExtranjera: number;
  otrasOperaciones: number;
  cuentasMonedaExtranjera: number;
  bancoCuentaExtranjera: number;
  cuentaMonedaExtranjera: number;
  monedaCuenta: number;
  idPaisCuenta: number;
  ciudadCuenta: string;
  creadoEl: Date;
  actualizadoEl: Date;
}