import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class RoleService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<Role> {
    return this.http.get<Role>(`${this.apiUrl}/roles.php?id=${id}`, { withCredentials: true });
  }

  getAll(): Observable<Role[]> {
    return this.http.get<Role[]>(`${this.apiUrl}/roles.php`, { withCredentials: true });
  }
}

export interface Role {
  id: number;
  nombre: string;
}