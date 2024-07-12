import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class RelationshipService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<any> {
    return this.http.get<Relationship>(`${this.apiUrl}/parentescos.php?id=${id}`, { withCredentials: true });
  }

  getAll(): Observable<any> {
    return this.http.get<Relationship[]>(`${this.apiUrl}/parentescos.php`, { withCredentials: true });
  }

  create(relationship: Relationship): Observable<Relationship> {
    const url = `${this.apiUrl}/parentescos.php`;
    return this.http.post<Relationship>(url, relationship, { withCredentials: true });
  }

  update(relationship: Relationship): Observable<Relationship> {
    const url = `${this.apiUrl}/parentescos.php`;
    return this.http.put<Relationship>(url, relationship, { withCredentials: true });
  }
}

export interface Relationship {
  id: number;
  nombre: string;
}