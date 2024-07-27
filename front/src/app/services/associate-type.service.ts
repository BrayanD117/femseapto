import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AssociateTypeService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<AssociateType> {
    return this.http.get<AssociateType>(`${this.apiUrl}/tiposasociado.php?id=${id}`, { withCredentials: true });
  }

  getAll(): Observable<AssociateType[]> {
    return this.http.get<AssociateType[]>(`${this.apiUrl}/tiposasociado.php`, { withCredentials: true });
  }
}

export interface AssociateType {
  id: number;
  nombre: string;
}