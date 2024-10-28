import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient, HttpParams } from '@angular/common/http';
import { firstValueFrom, Observable } from 'rxjs';
import saveAs from 'file-saver';

@Injectable({
  providedIn: 'root'
})
export class RequestCreditService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  create(formData: FormData): Observable<any> {
    return this.http.post(`${this.apiUrl}/solicitudescredito.php`, formData, { withCredentials: true });
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

  async downloadCreditRequestPdf(idSolicitudCredito: number, numeroDocumento: number): Promise<void> {
    try {
      const pdfData = await firstValueFrom(
        this.http.get(`${this.apiUrl}/solicitudescredito.php?id=${idSolicitudCredito}&download=pdf`, {
          withCredentials: true,
          responseType: 'blob'
        })
      );
      saveAs(pdfData, `Solicitud_Credito_${idSolicitudCredito}_${numeroDocumento}.pdf`);
    } catch (error) {
      console.error('Error al descargar el PDF:', error);
    }
  }
}