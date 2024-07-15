import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class BankAccountTypeService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getById(id: number): Observable<any> {
    return this.http.get<BankAccountType>(`${this.apiUrl}/tiposcuentabancaria.php?id=${id}`, { withCredentials: true });
  }

  getAll(): Observable<any> {
    return this.http.get<BankAccountType[]>(`${this.apiUrl}/tiposcuentabancaria.php`, { withCredentials: true });
  }
}

export interface BankAccountType {
  id: number;
  nombre: string;
}