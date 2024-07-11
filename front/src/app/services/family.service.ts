import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class FamilyService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/usuarios.php?id=${id}`, { withCredentials: true });
  }

  getAll(): Observable<any> {
    return this.http.get<any[]>(`${this.apiUrl}/usuarios.php`, { withCredentials: true });
  }

  create(user: any): Observable<any> {
    const url = `${this.apiUrl}/usuarios.php`;
    return this.http.post<any>(url, user, { withCredentials: true });
  }

  update(user: any): Observable<any> {
    const url = `${this.apiUrl}/usuarios.php`;
    return this.http.put<any>(url, user, { withCredentials: true });
  }
}
