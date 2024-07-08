import { Injectable } from '@angular/core';

import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment.development';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class NaturalpersonService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  get(userId: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/personasnaturales.php?idUsuario=${userId}`, { withCredentials: true });
  }
}