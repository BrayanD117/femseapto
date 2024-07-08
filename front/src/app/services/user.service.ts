import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment.development';
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

  getAll(): Observable<any> {
    return this.http.get<User[]>(`${this.apiUrl}/usuarios.php`, { withCredentials: true });
  }

  create(user: User): Observable<User> {
    const url = `${this.apiUrl}/usuarios.php`;
    return this.http.post<User>(url, user, { withCredentials: true });
  }

  update(user: User): Observable<User> {
    const url = `${this.apiUrl}/usuarios.php`;
    return this.http.put<User>(url, user, { withCredentials: true });
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
  activo: boolean;
  creadoEl: Date;
  actualizadoEl: Date;
}