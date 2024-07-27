import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class UserService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<any> {
    return this.http.get<User>(`${this.apiUrl}/usuarios.php?id=${id}`, { withCredentials: true });
  }

  getAll(params: { page: number; size: number; idRol: number }): Observable<{ data: User[], total: number }> {
    return this.http.get<{ data: User[], total: number }>(`${this.apiUrl}/usuarios.php`, { params , withCredentials: true });
  }

  create(user: User): Observable<User> {
    const url = `${this.apiUrl}/usuarios.php`;
    return this.http.post<User>(url, user, { withCredentials: true });
  }

  update(user: User): Observable<User> {
    const url = `${this.apiUrl}/usuarios.php`;
    return this.http.put<User>(url, user, { withCredentials: true });
  }

  changeState(id: number): Observable<any> {
    return this.http.patch(`${this.apiUrl}/usuarios.php?id=${id}`, null, { withCredentials: true });
  }
}

export interface User {
  id: number;
  id_rol: number;
  usuario: string;
  contrasenia: string;
  primerNombre: string;
  segundoNombre: string;
  primerApellido: string;
  segundoApellido: string;
  idTipoDocumento: number;
  numeroDocumento: string;
  id_tipo_asociado: number;
  activo: number;
  creadoEl: Date;
  actualizadoEl: Date;
}