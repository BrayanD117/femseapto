import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

// Environment component
import { environment } from '../../environments/environment.development';

@Injectable({
  providedIn: 'root'
})
export class LineasCreditoService {
    private apiUrl: string = environment.apiUrl;
    
    constructor(private http: HttpClient) { }
  
    obtenerLineasCredito(): Observable<any> {
      return this.http.get<any>(`${this.apiUrl}/lineascredito.php`);
    }
  
    obtenerLineaCreditoPorId(id: number): Observable<any> {
      return this.http.get<any>(`${this.apiUrl}/lineascredito.php?id=${id}`);
    }
  
    crearLineaCredito(lineaCredito: any): Observable<any> {
      return this.http.post<any>(`${this.apiUrl}/lineascredito.php`, lineaCredito);
    }
  
    actualizarLineaCredito(id: number, lineaCredito: any): Observable<any> {
      return this.http.put<any>(`${this.apiUrl}/lineascredito.php?id=${id}`, lineaCredito);
    }
}
