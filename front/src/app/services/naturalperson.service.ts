import { Injectable } from '@angular/core';

import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment.development';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class NaturalpersonService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getByUserId(userId: number): Observable<any> {
    return this.http.get<NaturalPerson>(`${this.apiUrl}/personasnaturales.php?idUsuario=${userId}`, { withCredentials: true });
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
  mpioExpDoc: string;
  fechaNacimiento: string;
  paisNacimiento: string;
  mpioNacimiento: string;
  otroLugarNacimiento?: string;
  mpioResidencia: string;
  idZonaResidencia: number;
  idTipoVivienda: number;
  estrato: number;
  direccionResidencia: string;
  aniosAntigVivienda: number;
  idEstadoCivil: number;
  cabezaFamilia: boolean;
  personasACargo: number;
  tieneHijos: boolean;
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
  aniosAntigEmpresa: number;
  mesesAntigEmpresa: number;
  mesSaleVacaciones: string;
  nombreEmergencia: string;
  numeroCedulaEmergencia: string;
  numeroCelularEmergencia: string;
}