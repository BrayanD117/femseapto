import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

// Environment component
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class CitiesService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: string): Observable<City> {
    return this.http.get<City>(`${this.apiUrl}/municipios.php?id=${id}`);
  }

  getByDepartmentId(dptmId: string): Observable<City[]> {
    return this.http.get<City[]>(`${this.apiUrl}/municipios.php?idDpto=${dptmId}`);
  }
}

export interface City {
  id: string;
  nombre: string;
  idDepartamento: string;
}