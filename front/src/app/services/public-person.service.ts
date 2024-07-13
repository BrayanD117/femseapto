import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class PublicPersonService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<any> {
    return this.http.get<PublicPerson>(`${this.apiUrl}/personaspublicas.php?id=${id}`, { withCredentials: true });
  }

  getByUserId(userId: number): Observable<any> {
    return this.http.get<PublicPerson>(`${this.apiUrl}/personaspublicas.php?idUsuario=${userId}`, { withCredentials: true });
  }

  getAll(): Observable<any> {
    return this.http.get<PublicPerson[]>(`${this.apiUrl}/personaspublicas.php`, { withCredentials: true });
  }

  create(info: PublicPerson): Observable<PublicPerson> {
    const url = `${this.apiUrl}/personaspublicas.php`;
    return this.http.post<PublicPerson>(url, info, { withCredentials: true });
  }

  update(info: PublicPerson): Observable<PublicPerson> {
    const url = `${this.apiUrl}/personaspublicas.php`;
    return this.http.put<PublicPerson>(url, info, { withCredentials: true });
  }
}

export interface PublicPerson {
  id: number;
  idUsuario: number;
  poderPublico: string;
  manejaRecPublicos: string;
  reconocimientoPublico: string;
  funcionesPublicas: string;
  actividadPublica: string;
  funcionarioPublicoExtranjero: string;
  famFuncionarioPublico: string;
  socioFuncionarioPublico: string;
  creadoEl: Date;
  actualizadoEl: Date;
}