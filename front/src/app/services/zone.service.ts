import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment.development';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ZoneService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<any> {
    return this.http.get<Zone>(`${this.apiUrl}/zonas.php?id=${id}`, { withCredentials: true });
  }

  getAll(): Observable<any> {
    return this.http.get<Zone[]>(`${this.apiUrl}/zonas.php`, { withCredentials: true });
  }
}

export interface Zone {
  id: number;
  nombre: string;
}