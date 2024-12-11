import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
    providedIn: 'root'
})
export class UsersMediaService {
    private apiUrl: string = environment.apiUrl;

    constructor(private http: HttpClient) { }

    getMediaByUser(userId: number): Observable<any> {
        return this.http.get<any>(`${this.apiUrl}/usuarioscomunicacion.php?idUsuario=${userId}`);
    }

    updateMediaForUser(data: { idUsuario: number; idsMedios: number[] }): Observable<any> {
        return this.http.post<any>(`${this.apiUrl}/usuarioscomunicacion.php`, data);
    }           
}