import { Component, Input, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { UserService, User } from '../../../../../services/user.service';
import { NaturalpersonService } from '../../../../../services/naturalperson.service';
import { CitiesService } from '../../../../../services/cities.service';
import { SolicitudAhorroService } from '../../../../../services/request-saving.service';
import { forkJoin } from 'rxjs';

@Component({
  selector: 'app-generate-saving-request',
  standalone: true,
  templateUrl: './generate-saving-request.component.html',
  styleUrls: ['./generate-saving-request.component.css'],
})
export class GenerateSavingRequestComponent implements OnInit {
  @Input() userId: number = 0;
  @Input() idSolicitudAhorro: number = 0;
  @Input() Quincena: String = '';

  nombreCompleto: string = '';
  numeroDocumento: number = 0;
  municipioExpedicionDocumento: string = '';
  valorTotalAhorro: number = 0;
  quincena: string = '';
  mes: string = '';
  celular: string = '';

  constructor(
    private http: HttpClient,
    private userService: UserService,
    private naturalPersonService: NaturalpersonService,
    private citiesService: CitiesService,
    private solicitudAhorroService: SolicitudAhorroService
  ) {}

  ngOnInit() {
    this.loadData();
  }

  loadData() {
    if (this.userId !== null) {
      this.userService.getById(this.userId).subscribe({
        next: (user: User) => {
          this.nombreCompleto = `${user.primerNombre || ''} ${user.segundoNombre || ''} ${user.primerApellido || ''} ${user.segundoApellido || ''}`.trim();
          this.numeroDocumento = Number(user.numeroDocumento);
          this.naturalPersonService.getByUserId(this.userId).subscribe({
            next: (person) => {
              console.log('PERSON ',person);
              this.celular = person.celular;
              this.citiesService.getById(person.mpioExpDoc).subscribe({
                next: (city) => {
                  
                  this.municipioExpedicionDocumento = city.nombre;
                  this.solicitudAhorroService.getById(this.idSolicitudAhorro).subscribe({
                    next: (solicitud) => {
                      this.valorTotalAhorro = solicitud.montoTotalAhorrar;
                      this.quincena = solicitud.quincena;
                      this.mes = solicitud.mes;
                      this.logData();
                    },
                    error: (err) => {
                      console.error('Error al obtener la solicitud de ahorro', err);
                    }
                  });
                },
                error: (err) => {
                  console.error('Error al obtener el municipio de expedición del documento', err);
                }
              });
            },
            error: (err) => {
              console.error('Error al obtener los datos de la persona natural', err);
            }
          });
        },
        error: (err) => {
          console.error('Error al obtener los datos del usuario', err);
        }
      });
    }
  }

  logData() {
    console.log('Nombre Completo:', this.nombreCompleto);
    console.log('Número de Documento:', this.numeroDocumento);
    console.log('Municipio de Expedición del Documento:', this.municipioExpedicionDocumento);
    console.log('Valor Total del Ahorro:', this.valorTotalAhorro);
    console.log('Quincena:', this.quincena);
    console.log('Mes:', this.mes);
    console.log('Celular:', this.celular);
  }
}
