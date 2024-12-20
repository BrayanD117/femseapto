import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

// Environment component
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class DepartmentsService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getAll(): Observable<Department[]> {
    return this.http.get<Department[]>(`${this.apiUrl}/departamentos.php`);
  }

  getById(id: number): Observable<Department> {
    return this.http.get<Department>(`${this.apiUrl}/departamentos.php?id=${id}`);
  }
}

export interface Department {
  id: string;
  nombre: string;
}