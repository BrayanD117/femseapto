import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

// Components
import { environment } from '../../environments/environment.development';

@Injectable({
  providedIn: 'root'
})
export class CitiesService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getCityById(id: string): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/municipios.php?id=${id}`);
  }

  getCitiesByDepartment(dptmId: string): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/municipios.php?idDpto=${dptmId}`);
  }
}
