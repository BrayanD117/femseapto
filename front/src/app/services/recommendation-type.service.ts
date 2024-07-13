import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class RecommendationTypeService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<any> {
    return this.http.get<RecommendationType>(`${this.apiUrl}/tiposreferencia.php?id=${id}`, { withCredentials: true });
  }

  getAll(): Observable<any> {
    return this.http.get<RecommendationType[]>(`${this.apiUrl}/tiposreferencia.php`, { withCredentials: true });
  }
}

export interface RecommendationType {
  id: number;
  abreviatura: string;
  nombre: string;
}