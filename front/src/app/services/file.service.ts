import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class FileService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  listFiles(): Observable<string[]> {
    const url = `${this.apiUrl}/list-files.php`;
    return this.http.get<string[]>(url, { withCredentials: true});
  }

  downloadFile(fileName: string): Observable<Blob> {
    const url = `${this.apiUrl}/download.php?file=${fileName}`;
    return this.http.get(url, { responseType: 'blob' });
  }
}