import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { catchError } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class FamilyService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<Family> {
    return this.http.get<Family>(`${this.apiUrl}/informacionfamiliares.php?id=${id}`, { withCredentials: true });
  }

  getByUserId(userId: number): Observable<Family[]> {
    return this.http.get<Family[]>(`${this.apiUrl}/informacionfamiliares.php?idUsuario=${userId}`, { withCredentials: true });
  }

  validate(userId: number): Observable<boolean> {
    return this.http.get<boolean>(`${this.apiUrl}/informacionfamiliares.php?val=${userId}`, { withCredentials: true }).pipe(
      catchError(() => of(false))
    );;
  }

  getAll(): Observable<Family[]> {
    return this.http.get<Family[]>(`${this.apiUrl}/informacionfamiliares.php`, { withCredentials: true });
  }

  create(familiar: Family): Observable<Family> {
    const url = `${this.apiUrl}/informacionfamiliares.php`;
    return this.http.post<Family>(url, familiar, { withCredentials: true });
  }

  update(familiar: Family): Observable<Family> {
    const url = `${this.apiUrl}/informacionfamiliares.php`;
    return this.http.put<Family>(url, familiar, { withCredentials: true });
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/informacionfamiliares.php?id=${id}`, { withCredentials: true });
  }
}

export interface Family {
  id: number;
  idUsuario: number;
  nombreCompleto: string;
  idTipoDocumento: number;
  numeroDocumento: string;
  idDptoExpDoc: string;
  idMpioExpDoc: string;
  idParentesco: number;
  idGenero: number;
  fechaNacimiento: Date;
  idNivelEducativo: number;
  trabaja: string;
  celular: string;
  creadoEl: Date;
  actualizadoEl: Date;
}