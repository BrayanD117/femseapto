import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class RequestCreditService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  create(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/solicitudescredito.php`, data, { withCredentials: true });
  }

  getAll(params: { page: number; size: number; search: string }): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/solicitudescredito.php`, { params , withCredentials: true });
  }

  getById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/solicitudescredito.php?id=${id}`, { withCredentials: true });
  }

  getCreditsByDateRange(startDate: string, endDate: string): Observable<any[]> {
    const params = new HttpParams()
      .set('startDate', startDate)
      .set('endDate', endDate);
    return this.http.get<any[]>(`${this.apiUrl}/solicitudescredito.php`, { params, withCredentials: true });
  }
  
}
