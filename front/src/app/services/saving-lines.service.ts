import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class SavingLinesService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<SavingLine> {
    return this.http.get<SavingLine>(`${this.apiUrl}/lineasahorro.php?id=${id}`, { withCredentials: true });
  }

  getAll(): Observable<SavingLine[]> {
    return this.http.get<SavingLine[]>(`${this.apiUrl}/lineasahorro.php`, { withCredentials: true });
  }
}

export interface SavingLine {
  id: number;
  nombre: string;
}