import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
    providedIn: 'root'
})
export class MediaService {
    private apiUrl: string = environment.apiUrl;

    constructor(private http: HttpClient) { }

    getMedia(): Observable<any> {
        const url = `${this.apiUrl}/medioscomunicacion.php`;
        return this.http.get(url);
    }

    getMediaById(id: number): Observable<any> {
        const url = `${this.apiUrl}/medioscomunicacion.php/${id}`;
        return this.http.get(url);
    }

}