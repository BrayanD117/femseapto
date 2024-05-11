import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class UserInfoService {
  
  private apiUrl = 'http://localhost/femseapto/back/api/userInfo.php';  // Ajusta según tu configuración

  constructor(private http: HttpClient) { }

  getUserInfo(): Observable<any> {
    return this.http.get<any>(this.apiUrl, { withCredentials: true });
  }
}
