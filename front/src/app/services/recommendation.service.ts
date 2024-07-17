import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { catchError } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class RecommendationService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<Recommendation> {
    return this.http.get<Recommendation>(`${this.apiUrl}/referencias.php?id=${id}`, { withCredentials: true });
  }

  getByUserId(userId: number): Observable<Recommendation[]> {
    return this.http.get<Recommendation[]>(`${this.apiUrl}/referencias.php?idUsuario=${userId}`, { withCredentials: true });
  }

  validatePersonal(userId: number): Observable<boolean> {
    return this.http.get<boolean>(`${this.apiUrl}/referencias.php?valpers=${userId}`, { withCredentials: true }).pipe(
      catchError(() => of(false))
    );
  }

  validateFamiliar(userId: number): Observable<boolean> {
    return this.http.get<boolean>(`${this.apiUrl}/referencias.php?valfam=${userId}`, { withCredentials: true }).pipe(
      catchError(() => of(false))
    );
  }

  getAll(): Observable<Recommendation[]> {
    return this.http.get<Recommendation[]>(`${this.apiUrl}/referencias.php`, { withCredentials: true });
  }

  create(familiar: Recommendation): Observable<Recommendation> {
    const url = `${this.apiUrl}/referencias.php`;
    return this.http.post<Recommendation>(url, familiar, { withCredentials: true });
  }

  update(familiar: Recommendation): Observable<Recommendation> {
    const url = `${this.apiUrl}/referencias.php`;
    return this.http.put<Recommendation>(url, familiar, { withCredentials: true });
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/referencias.php?id=${id}`, { withCredentials: true });
  }
}

export interface Recommendation {
  id: number;
  idUsuario: number;
  nombreRazonSocial: string;
  parentesco: string;
  idTipoReferencia: number;
  idMunicipio: string;
  direccion: string;
  telefono: string;
  correoElectronico: string;
  creadoEl: Date;
  actualizadoEl: Date;
}