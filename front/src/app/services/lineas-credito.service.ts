import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

// Environment component
import { environment } from '../../environments/environment';

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

    getNameById(id: number): Observable<string> {
      return this.http.get<any>(`${this.apiUrl}/lineascredito.php?id=${id}`).pipe(
        map(response => response.nombre) // Suponiendo que 'nombre' es la propiedad que contiene el nombre de la línea de crédito
      );
    }
  
    crearLineaCredito(lineaCredito: any): Observable<any> {
      return this.http.post<any>(`${this.apiUrl}/lineascredito.php`, lineaCredito);
    }
  
    actualizarLineaCredito(id: number, lineaCredito: any): Observable<any> {
      return this.http.put<any>(`${this.apiUrl}/lineascredito.php?id=${id}`, lineaCredito);
    }
}
