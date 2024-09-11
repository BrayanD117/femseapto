import { Injectable } from '@angular/core';

import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { Observable, of } from 'rxjs';
import { catchError } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class NaturalpersonService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getByUserId(userId: number): Observable<any> {
    return this.http.get<NaturalPerson>(`${this.apiUrl}/personasnaturales.php?idUsuario=${userId}`, { withCredentials: true });
  }

  validate(userId: number): Observable<boolean> {
    return this.http.get<boolean>(`${this.apiUrl}/personasnaturales.php?val=${userId}`, { withCredentials: true }).pipe(
      catchError(() => of(false))
    );
  }

  create(natPerson: NaturalPerson): Observable<NaturalPerson> {
    const url = `${this.apiUrl}/personasnaturales.php`;
    return this.http.post<NaturalPerson>(url, natPerson, { withCredentials: true });
  }

  update(natPerson: NaturalPerson): Observable<NaturalPerson> {
    const url = `${this.apiUrl}/personasnaturales.php`;
    return this.http.put<NaturalPerson>(url, natPerson, { withCredentials: true });
  }
}

export interface NaturalPerson {
  id: number;
  idUsuario: number;
  idGenero: number;
  fechaExpDoc: string;
  idDeptoExpDoc: string;
  mpioExpDoc: string;
  fechaNacimiento: string;
  paisNacimiento: string;
  idDeptoNacimiento: string;
  mpioNacimiento: string;
  otroLugarNacimiento?: string;
  idDeptoResidencia: string;
  mpioResidencia: string;
  idZonaResidencia: number;
  idTipoVivienda: number;
  estrato: number;
  direccionResidencia: string;
  antigVivienda: string;
  idEstadoCivil: number;
  cabezaFamilia: string;
  personasACargo: number;
  tieneHijos: string;
  numeroHijos: number;
  correoElectronico: string;
  telefono: string;
  celular: string;
  telefonoOficina: string;
  idNivelEducativo: number;
  profesion: string;
  ocupacionOficio: string;
  idEmpresaLabor: number;
  idTipoContrato: number;
  dependenciaEmpresa: string;
  cargoOcupa: string;
  jefeInmediato: string;
  antigEmpresa: string;
  mesesAntigEmpresa: number;
  mesSaleVacaciones: string;
  nombreEmergencia: string;
  numeroCedulaEmergencia: string;
  numeroCelularEmergencia: string;
  creadoEl: string;
  actualizadoEl: string;
}