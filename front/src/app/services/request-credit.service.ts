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

  getAll(params: any): Observable<any> {
    let httpParams = new HttpParams();
    Object.keys(params).forEach(key => {
      if (params[key] !== undefined && params[key] !== null) {
        httpParams = httpParams.append(key, params[key]);
      }
    });
    return this.http.get<any>(`${this.apiUrl}/solicitudescredito.php`, { params: httpParams, withCredentials: true });
  }
}
