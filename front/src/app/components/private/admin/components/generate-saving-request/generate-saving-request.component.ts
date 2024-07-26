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
  lineasAhorro: any[] = [];
  tipoAsociado: number = 0;

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
          this.tipoAsociado = user.id_tipo_asociado;
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
                          this.lineasAhorro = solicitud.lineas || [];
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

  async generateExcel() {
    const workbook = new ExcelJS.Workbook();
    try {
      const templateUrl = this.tipoAsociado === 1 ? '/assets/SOLICITAR_AHORRO_NOMINA.xlsx' : '/assets/SOLICITAR_AHORRO_COMISION.xlsx';
      await this.loadTemplate(workbook, templateUrl);
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

        if (this.tipoAsociado === 1) {
          const salarioTexto = [
            { text: "Salario $ ", font: { underline: false } },
            { text: this.salario.toString(), font: { underline: true } }
          ];
          worksheet.getCell('B6').value = { richText: salarioTexto };

          const nombreTexto = [
            { text: "NOMBRE: ", font: { underline: false } },
            { text: this.nombreCompleto, font: { underline: true } }
          ];
          worksheet.getCell('B13').value = { richText: nombreTexto };

          const cedulaTexto = [
            { text: "CEDULA: ", font: { underline: false } },
            { text: this.numeroDocumento.toString(), font: { underline: true } }
          ];
          worksheet.getCell('B14').value = { richText: cedulaTexto };

          const celularTexto = [
            { text: "CELULAR: ", font: { underline: false } },
            { text: this.celular, font: { underline: true } }
          ];
          worksheet.getCell('B15').value = { richText: celularTexto };

          this.lineasAhorro.forEach(linea => {
            let cellAddress = '';
            let label = '';
            switch (linea.idLineaAhorro) {
              case 1:
                cellAddress = 'D11';
                label = 'Vivienda: $ ';
                break;
              case 2:
                cellAddress = 'D9';
                label = 'Navideño: $ ';
                break;
              case 3:
                cellAddress = 'D10';
                label = 'Vacacional: $ ';
                break;
              case 4:
                cellAddress = 'D8';
                label = 'Extraordinario: $ ';
                break;
            }
            if (cellAddress) {
              worksheet.getCell(cellAddress).value = 'X';
            }
            if (label && linea.montoAhorrar !== undefined) {
              worksheet.getCell(`E${cellAddress.slice(1)}`).value = {
                richText: [
                  { text: label, font: { underline: false } },
                  { text: linea.montoAhorrar.toString(), font: { underline: true } }
                ]
              };
            }
          });
        } else if (this.tipoAsociado === 2) {
          const nombreTexto = [
            { text: "NOMBRE: ", font: { underline: false } },
            { text: this.nombreCompleto, font: { underline: true } }
          ];
          worksheet.getCell('B10').value = { richText: nombreTexto };

          const cedulaTexto = [
            { text: "CEDULA: ", font: { underline: false } },
            { text: this.numeroDocumento.toString(), font: { underline: true } }
          ];
          worksheet.getCell('B11').value = { richText: cedulaTexto };

          const celularTexto = [
            { text: "CELULAR: ", font: { underline: false } },
            { text: this.celular, font: { underline: true } }
          ];
          worksheet.getCell('B12').value = { richText: celularTexto };

          const linea = this.lineasAhorro.find(linea => linea.idLineaAhorro === 4);
          if (linea) {
            worksheet.getCell('D8').value = 'X';
            worksheet.getCell('E8').value = {
              richText: [
                { text: 'Extraordinario: $ ', font: { underline: false } },
                { text: linea.montoAhorrar.toString(), font: { underline: true } }
              ]
            };
          }
        }

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

  async loadTemplate(workbook: ExcelJS.Workbook, templateUrl: string) {
    try {
      const data: ArrayBuffer = await firstValueFrom(
        this.http.get(templateUrl, {
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