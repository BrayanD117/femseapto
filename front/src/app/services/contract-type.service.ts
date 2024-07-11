import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ContractTypeService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<any> {
    return this.http.get<ContractType>(`${this.apiUrl}/tiposcontrato.php?id=${id}`, { withCredentials: true });
  }

  getAll(): Observable<any> {
    return this.http.get<ContractType[]>(`${this.apiUrl}/tiposcontrato.php`, { withCredentials: true });
  }
}

export interface ContractType {
  id: number;
  nombre: string;
}