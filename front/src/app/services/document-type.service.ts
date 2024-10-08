import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class DocumentTypeService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<DocumentType> {
    return this.http.get<DocumentType>(`${this.apiUrl}/tiposdocumento.php?id=${id}`, { withCredentials: true });
  }

  getAll(): Observable<DocumentType[]> {
    return this.http.get<DocumentType[]>(`${this.apiUrl}/tiposdocumento.php`, { withCredentials: true });
  }
}

export interface DocumentType {
  id: number;
  abreviatura: string;
  nombre: string;
}