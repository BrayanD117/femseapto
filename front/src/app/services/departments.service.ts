import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';


// Environment component
import { environment } from '../../environments/environment.development';

@Injectable({
  providedIn: 'root'
})
export class DepartmentsService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getDepartments(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/departamentos.php`);
  }
}
