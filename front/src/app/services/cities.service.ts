import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

// Environment component
import { environment } from '../../environments/environment.development';

interface City {
  id: string;
  nombre: string;
  id_departamento: string;
}

@Injectable({
  providedIn: 'root'
})
export class CitiesService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getCityById(id: string): Observable<City> {
    return this.http.get<City>(`${this.apiUrl}/municipios.php?id=${id}`);
  }

  getCitiesByDepartment(dptmId: string): Observable<City[]> {
    return this.http.get<City[]>(`${this.apiUrl}/municipios.php?idDpto=${dptmId}`);
  }
}
