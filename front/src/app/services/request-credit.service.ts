import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
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
}
