import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class HouseTypeService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<any> {
    return this.http.get<HouseType>(`${this.apiUrl}/tiposvivienda.php?id=${id}`, { withCredentials: true });
  }

  getAll(): Observable<any> {
    return this.http.get<HouseType[]>(`${this.apiUrl}/tiposvivienda.php`, { withCredentials: true });
  }
}

export interface HouseType {
  id: number;
  nombre: string;
}