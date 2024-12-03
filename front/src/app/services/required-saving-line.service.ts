import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class RequiredSavingLineService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<RequiredSavingLine> {
    return this.http.get<RequiredSavingLine>(`${this.apiUrl}/lineasahorroobligatorias.php?id=${id}`, { withCredentials: true });
  }

  getNameById(id: number): Observable<string> {
    return this.http.get<any>(`${this.apiUrl}/lineasahorroobligatorias.php?id=${id}`).pipe(
      map(response => response.nombre)
    );
  }

  getAll(): Observable<RequiredSavingLine[]> {
    return this.http.get<RequiredSavingLine[]>(`${this.apiUrl}/lineasahorroobligatorias.php`, { withCredentials: true });
  }
}

export interface RequiredSavingLine {
  id: number;
  nombre: string;
}