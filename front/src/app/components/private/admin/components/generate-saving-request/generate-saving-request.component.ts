import { Component, Input, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { UserService, User } from '../../../../../services/user.service';
import { NaturalpersonService } from '../../../../../services/naturalperson.service';
import { CitiesService } from '../../../../../services/cities.service';
import { SolicitudAhorroService } from '../../../../../services/request-saving.service';
import { CompanyService } from '../../../../../services/company.service';
import { FinancialInfoService } from '../../../../../services/financial-info.service';
import { firstValueFrom } from 'rxjs';
import * as ExcelJS from 'exceljs';
import { saveAs } from 'file-saver';

@Component({
  selector: 'app-generate-saving-request',
  standalone: true,
  templateUrl: './generate-saving-request.component.html',
  styleUrls: ['./generate-saving-request.component.css'],
})
export class GenerateSavingRequestComponent implements OnInit {
  @Input() userId: number = 0;
  @Input() idSolicitudAhorro: number = 0;

  nombreCompleto: string = '';
  numeroDocumento: number = 0;
  municipioExpedicionDocumento: string = '';
  valorTotalAhorro: number = 0;
  quincena: string = '';
  mes: string = '';
  celular: string = '';
  nombreEmpresa: string = '';
  salario: number = 0;

  constructor(
    private http: HttpClient,
    private userService: UserService,
    private naturalPersonService: NaturalpersonService,
    private citiesService: CitiesService,
    private solicitudAhorroService: SolicitudAhorroService,
    private companyService: CompanyService,
    private financialInfoService: FinancialInfoService
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
              this.celular = person.celular;
              this.citiesService.getById(person.mpioExpDoc).subscribe({
                next: (city) => {
                  this.municipioExpedicionDocumento = city.nombre;
                  this.companyService.getById(person.idEmpresaLabor).subscribe({
                    next: (company) => {
                      this.nombreEmpresa = company.nombre;
                      this.solicitudAhorroService.getById(this.idSolicitudAhorro).subscribe({
                        next: (solicitud) => {
                          this.valorTotalAhorro = solicitud.montoTotalAhorrar;
                          this.quincena = solicitud.quincena;
                          this.mes = solicitud.mes;
                          this.financialInfoService.getByUserId(this.userId).subscribe({
                            next: (financialInfo) => {
                              this.salario = financialInfo.ingresosMensuales;
                            },
                            error: (err) => {
                              console.error('Error al obtener la información financiera', err);
                            }
                          });
                        },
                        error: (err) => {
                          console.error('Error al obtener la solicitud de ahorro', err);
                        }
                      });
                    },
                    error: (err) => {
                      console.error('Error al obtener el nombre de la empresa', err);
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
    console.log('Nombre Empresa:', this.nombreEmpresa);
    console.log('Salario:', this.salario);
  }

  async generateExcel() {
    const workbook = new ExcelJS.Workbook();
    try {
      await this.loadTemplate(workbook);
      const worksheet = workbook.getWorksheet(1);
      
      if (worksheet) {
        const texto = [
          { text: "Yo ", font: { underline: false } },
          { text: this.nombreCompleto, font: { underline: true } },
          { text: " Identificado con cédula N° ", font: { underline: false } },
          { text: this.numeroDocumento.toString(), font: { underline: true } },
          { text: " de ", font: { underline: false } },
          { text: this.municipioExpedicionDocumento, font: { underline: true } },
          { text: ", autorizo a ", font: { underline: false } },
          { text: this.nombreEmpresa, font: { underline: true } },
          { text: " para descontar de mi salario el valor de $ ", font: { underline: false } },
          { text: this.valorTotalAhorro.toString(), font: { underline: true } },
          { text: " quincenalmente, a partir de la ", font: { underline: false } },
          { text: this.quincena, font: { underline: true } },
          { text: " quincena de ", font: { underline: false } },
          { text: this.mes, font: { underline: true } },
          { text: ".", font: { underline: false } }
        ];

        worksheet.getCell('B5').value = { richText: texto };

        const salarioTexto = [
          { text: "Salario $ ", font: { underline: false } },
          { text: this.salario.toString(), font: { underline: true } }
        ];

        worksheet.getCell('B6').value = { richText: salarioTexto };

        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], {
          type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        });
        saveAs(blob, `Solicitud de Ahorro ${this.numeroDocumento}.xlsx`);
      } else {
        console.error('Worksheet is undefined.');
      }
    } catch (error) {
      console.error('Error loading template', error);
    }
  }

  async loadTemplate(workbook: ExcelJS.Workbook) {
    try {
      const data: ArrayBuffer = await firstValueFrom(
        this.http.get('/assets/SOLICITAR_AHORRO_NOMINA.xlsx', {
          responseType: 'arraybuffer',
        })
      );
      await workbook.xlsx.load(data);
    } catch (error) {
      console.error('Error reading the template file:', error);
      throw error;
    }
  }
}
