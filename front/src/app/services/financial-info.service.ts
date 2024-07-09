import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

// Environment component
import { environment } from '../../environments/environment.development';

@Injectable({
  providedIn: 'root'
})
export class FinancialInfoService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getFinancialInfo(userId: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/informacionfinanciera.php?idUsuario=${userId}`, { withCredentials: true });
  }

  updateFinancialInfo(userId: number, data: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/informacionfinanciera.php`, { idUsuario: userId, ...data }, { withCredentials: true });
  }
}
