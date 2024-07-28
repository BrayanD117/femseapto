import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class FileUploadService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  uploadFile(file: File): Observable<any> {
    const formData = new FormData();
    formData.append('file', file);

    const url = `${this.apiUrl}/upload.php`;
    return this.http.post(url, formData);
  }

  getFiles(): Observable<any> {
    const url = `${this.apiUrl}/list-files.php`;
    return this.http.get(url);
  }
}