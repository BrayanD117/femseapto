import { Component, Input, OnInit } from '@angular/core';
import * as ExcelJS from 'exceljs';
import { saveAs } from 'file-saver';
import { HttpClient } from '@angular/common/http';
import { firstValueFrom, forkJoin } from 'rxjs';
import { LineasCreditoService } from '../../../../../services/lineas-credito.service';
import { UserService } from '../../../../../services/user.service';
import { NaturalpersonService } from '../../../../../services/naturalperson.service';
import { CitiesService } from '../../../../../services/cities.service';
import { DepartmentsService } from '../../../../../services/departments.service';
import { FamilyService } from '../../../../../services/family.service';
import { ContractTypeService } from '../../../../../services/contract-type.service';
import { EducationLevelService } from '../../../../../services/education-level.service';
import { FinancialInfoService } from '../../../../../services/financial-info.service';
import { BankAccountTypeService } from '../../../../../services/bank-account-type.service';

@Component({
  selector: 'app-generate-credit-request',
  standalone: true,
  templateUrl: './generate-credit-request.component.html',
  styleUrls: ['./generate-credit-request.component.css'],
})
export class GenerateCreditRequestComponent implements OnInit {
  @Input() montoSolicitado: number | string = 0;
  @Input() plazoQuincenal: number | string = 0;
  @Input() valorCuotaQuincenal: number | string = 0;
  @Input() fechaSolicitud: string = '';
  @Input() lineaCredito: string = '';
  @Input() reestructurado: string = '';
  @Input() periocidadPago: string = '';
  @Input() tasaInteres: number | string = 0;
  @Input() userId: number = 0;

  lineaCreditoNombre: string = '';
  nombreAsociado: string = '';
  numeroDocumento: number = 0;
  ciudadExpedicionDocumento: string = '';
  fechaExpedicionDocumento: string = '';
  ciudadNacimiento: string = '';
  fechaNacimiento: string = '';
  genero: number = 0;
  personasACargo: number | string = 0;
  estadoCivil: string = '';
  direccionResidencia: string = '';
  municipioResidencia: string = '';
  departamentoResidencia: string = '';
  email: string = '';
  tipoContrato: number = 0;
  nivelEducativo: number = 0;
  nombreConyuge: string = '';
  cedulaLugarExpConyuge: string = '';
  telefonoConyuge: string = '';
  jefeInmediato: string = '';
  tipoVivienda: number = 0;
  telefono: string = '';
  celular: string = '';
  telefonoOficina: string = '';
  permanenciaVivienda: string = '';
  antiguedadEmpresa: string = '';
  salidaVacaciones: string = '';
  nombreBanco: string = '';
  idTipoCuentaBanco: number = 0;
  numCuentaBanco: string = '';
  salarioMensual: number | string = 0;
  primaProductividad: number | string = 0;
  otrosIngresos: number | string = 0;
  egresosMensuales: number | string = 0;
  obligFinancieras: number | string = 0;
  otrosEgresos: number | string = 0;
  totalActivos: number | string = 0;
  totalPasivos: number | string = 0;

  constructor(
    private http: HttpClient,
    private lineasCreditoService: LineasCreditoService,
    private userService: UserService,
    private naturalPersonService: NaturalpersonService,
    private citiesService: CitiesService,
    private departmentsService: DepartmentsService,
    private familyService: FamilyService,
    private contractTypeService: ContractTypeService,
    private educationLevelService: EducationLevelService,
    private financialInfoService: FinancialInfoService,
    private bankAccountTypeService: BankAccountTypeService
  ) {}

  ngOnInit() {
    if (this.lineaCredito) {
      this.lineasCreditoService
        .getNameById(Number(this.lineaCredito))
        .subscribe({
          next: (nombre) => {
            this.lineaCreditoNombre = nombre;
          },
          error: (err) => {
            console.error(
              'Error al obtener el nombre de la línea de crédito',
              err
            );
          },
        });
    }

    if (this.userId !== null) {
      this.userService.getById(this.userId).subscribe({
        next: (user) => {
          this.nombreAsociado = `${user.primerNombre || ''} ${
            user.segundoNombre || ''
          } ${user.primerApellido || ''} ${user.segundoApellido || ''}`.trim();
          this.numeroDocumento = Number(user.numeroDocumento);
        },
        error: (err) => {
          console.error('Error al obtener el nombre del usuario', err);
        },
      });

      this.naturalPersonService.getByUserId(this.userId).subscribe({
        next: (person) => {
          this.ciudadExpedicionDocumento = person.mpioExpDoc;
          this.fechaExpedicionDocumento = person.fechaExpDoc;
          this.ciudadNacimiento = person.mpioNacimiento;
          this.fechaNacimiento = person.fechaNacimiento;
          this.genero = person.idGenero;
          this.personasACargo = person.personasACargo;
          this.estadoCivil = person.estadoCivil || '';
          this.direccionResidencia = person.direccionResidencia;
          this.municipioResidencia = person.mpioResidencia;
          this.email = person.correoElectronico;
          this.jefeInmediato = person.jefeInmediato;
          this.tipoContrato = person.idTipoContrato;
          this.nivelEducativo = person.idNivelEducativo;
          this.tipoVivienda = person.idTipoVivienda;
          this.telefono = person.telefono;
          this.celular = person.celular;
          this.telefonoOficina = person.telefonoOficina;
          this.permanenciaVivienda = `${
            person.aniosAntigVivienda || ''
          } AÑOS`.trim();
          this.antiguedadEmpresa = `${
            person.aniosAntigEmpresa || ''
          } AÑOS`.trim();
          this.salidaVacaciones = person.mesSaleVacaciones;

          const departamentoId = this.municipioResidencia.slice(0, 2);
          forkJoin([
            this.citiesService.getById(this.ciudadExpedicionDocumento),
            this.citiesService.getById(this.ciudadNacimiento),
            this.citiesService.getById(this.municipioResidencia),
            this.departmentsService.getAll(),
            this.familyService.getByUserId(this.userId),
          ]).subscribe({
            next: ([
              expCity,
              birthCity,
              residenceCity,
              departments,
              familyMembers,
            ]) => {
              console.log(residenceCity);
              this.ciudadExpedicionDocumento = expCity.nombre;
              this.ciudadNacimiento = birthCity.nombre;
              this.municipioResidencia = residenceCity.nombre;
              const departamento = departments.find(
                (d) => d.id === departamentoId
              );
              this.departamentoResidencia = departamento
                ? departamento.nombre
                : '';
              const conyuge = familyMembers.find(
                (f) => f.idParentesco === 5 || f.idParentesco === 6
              );
              if (conyuge) {
                this.nombreConyuge = conyuge.nombreCompleto;
                this.cedulaLugarExpConyuge = `${conyuge.numeroDocumento} ${conyuge.idMpioExpDoc}`;
                this.telefonoConyuge = `Telefono Conyuge: ${conyuge.celular}`;
              }
            },
            error: (err) => {
              console.error(
                'Error al obtener las ciudades y departamentos',
                err
              );
            },
          });
        },
        error: (err) => {
          console.error(
            'Error al obtener los datos de la persona natural',
            err
          );
        },
      });

      this.familyService.getByUserId(this.userId).subscribe({
        next: (family) => {
          const conyuge = family.find(
            (f) => f.idParentesco === 5 || f.idParentesco === 6
          );
          if (conyuge) {
            this.nombreConyuge = conyuge.nombreCompleto;
            this.telefonoConyuge = `Telefono Conyuge: ${conyuge.celular}`;

            /*forkJoin([
              this.citiesService.getById(conyuge.idMpioExpDoc),
            ]).subscribe({
              next: ([
                expCity
              ]) => {
                console.log("expcity", expCity);
                this.cedulaLugarExpConyuge = `${conyuge.numeroDocumento} ${expCity.nombre}`;
                this.ciudadExpedicionDocumento = expCity.nombre;
                this.ciudadNacimiento = birthCity.nombre;
                this.municipioResidencia = residenceCity.nombre;
                const departamento = departments.find(
                  (d) => d.id === departamentoId
                );
                this.departamentoResidencia = departamento
                  ? departamento.nombre
                  : '';
                const conyuge = familyMembers.find(
                  (f) => f.idParentesco === 5 || f.idParentesco === 6
                );
                if (conyuge) {
                  this.nombreConyuge = conyuge.nombreCompleto;
                  this.cedulaLugarExpConyuge = `${conyuge.numeroDocumento} ${conyuge.idMpioExpDoc}`;
                  this.telefonoConyuge = `Telefono Conyuge: ${conyuge.celular}`;
                }
              },
              error: (err) => {
                console.error(
                  'Error al obtener las ciudades y departamentos',
                  err
                );
              },
            });*/
          }    
        },
        error: (err) => {
          console.error(
            'Error al obtener los datos de la persona natural',
            err
          );
        },
      });

      this.financialInfoService.getByUserId(this.userId).subscribe({
        next: (financialInfo) => {
          this.nombreBanco = financialInfo.nombreBanco;
          this.idTipoCuentaBanco = financialInfo.idTipoCuentaBanc;
          this.numCuentaBanco = financialInfo.numeroCuentaBanc;
          this.salarioMensual = financialInfo.ingresosMensuales;
          this.primaProductividad = financialInfo.primaProductividad;
          this.otrosIngresos = financialInfo.otrosIngresosMensuales;
          this.egresosMensuales = financialInfo.egresosMensuales;
          this.obligFinancieras = financialInfo.obligacionFinanciera;
          this.otrosEgresos = financialInfo.otrosEgresosMensuales;
          this.totalActivos = financialInfo.totalActivos;
          this.totalPasivos = financialInfo.totalPasivos;
        },
        error: (err) => {
          console.error(
            'Error al obtener los datos de información financiera',
            err
          );
        },
      });
    }
  }

  async generateExcel() {
    const workbook = new ExcelJS.Workbook();
    try {
      await this.loadTemplate(workbook);
      const worksheet = workbook.getWorksheet(1);

      // Agregar datos dinámicos
      if (worksheet) {
        worksheet.getCell('G5').value = Number(this.montoSolicitado);
        worksheet.getCell('O5').value = Number(this.plazoQuincenal);
        worksheet.getCell('U5').value = Number(this.valorCuotaQuincenal);

        const fecha = new Date(this.fechaSolicitud);
        worksheet.getCell('R7').value = fecha.getDate();
        worksheet.getCell('T7').value = fecha.getMonth() + 1;
        worksheet.getCell('V7').value = fecha.getFullYear();

        worksheet.getCell('A9').value = this.lineaCreditoNombre;
        worksheet.getCell('G9').value = this.reestructurado;
        worksheet.getCell('L9').value = this.periocidadPago;
        worksheet.getCell('Q9').value = Number(this.tasaInteres) / 100;
        worksheet.getCell('C11').value = this.nombreAsociado;

        worksheet.getCell('A13').value = this.numeroDocumento;
        worksheet.getCell('F13').value = this.ciudadExpedicionDocumento;
        worksheet.getCell('H13').value = this.fechaExpedicionDocumento
          ? new Date(this.fechaExpedicionDocumento)
          : '';
        worksheet.getCell('K13').value = `${this.ciudadNacimiento} ${
          this.fechaNacimiento
            ? new Date(this.fechaNacimiento).toLocaleDateString()
            : ''
        }`;

        if (this.genero === 1) {
          worksheet.getCell('H15').value = 'X';
        } else if (this.genero === 2) {
          worksheet.getCell('I15').value = 'X';
        }

        worksheet.getCell('N14').value = Number(this.personasACargo);

        switch (this.estadoCivil.toLowerCase()) {
          case 'casado':
            worksheet.getCell('S14').value = 'X';
            break;
          case 'soltero':
            worksheet.getCell('S15').value = 'X';
            break;
          case 'union libre':
            worksheet.getCell('W14').value = 'X';
            break;
          default:
            worksheet.getCell('W15').value = 'X';
            break;
        }

        worksheet.getCell('E16').value = this.direccionResidencia;
        worksheet.getCell('M16').value = this.municipioResidencia;
        worksheet.getCell('T16').value = this.departamentoResidencia;

        if (this.tipoContrato === 1) {
          worksheet.getCell('Q17').value = 'X';
        } else if (this.tipoContrato === 2) {
          worksheet.getCell('Q18').value = 'X';
        }

        switch (this.nivelEducativo) {
          case 1:
            worksheet.getCell('E19').value = 'X';
            break;
          case 2:
            worksheet.getCell('G19').value = 'X';
            break;
          case 3:
            worksheet.getCell('I19').value = 'X';
            break;
          case 4:
            worksheet.getCell('L19').value = 'X';
            break;
          case 5:
            worksheet.getCell('P19').value = 'X';
            break;
        }

        worksheet.getCell('C20').value = this.email;
        worksheet.getCell('N20').value = this.jefeInmediato;
        worksheet.getCell('F21').value = this.nombreConyuge;
        worksheet.getCell('O21').value = this.cedulaLugarExpConyuge;
        worksheet.getCell('R21').value = this.telefonoConyuge;

        if (this.tipoVivienda === 1) {
          worksheet.getCell('D22').value = 'X';
        } else if (this.tipoContrato === 2) {
          worksheet.getCell('D23').value = 'X';
        } else if (this.tipoContrato === 3) {
          worksheet.getCell('D24').value = 'X';
        }

        worksheet.getCell('H22').value = this.telefono;
        worksheet.getCell('H23').value = this.celular;
        worksheet.getCell('H24').value = this.telefonoOficina;

        worksheet.getCell('P22').value = this.permanenciaVivienda;
        worksheet.getCell('P23').value = this.antiguedadEmpresa;
        worksheet.getCell('O24').value = this.salidaVacaciones;

        worksheet.getCell('E25').value = this.nombreBanco;
        if (this.idTipoCuentaBanco === 1) {
          worksheet.getCell('P25').value = 'X';
        } else if (this.idTipoCuentaBanco === 2) {
          worksheet.getCell('P26').value = 'X';
        }
        worksheet.getCell('R25').value = this.numCuentaBanco;

        // Información Financiera
        worksheet.getCell('C28').value = Number(this.salarioMensual);
        worksheet.getCell('C29').value = Number(this.primaProductividad);
        worksheet.getCell('C30').value = Number(this.otrosIngresos);
        worksheet.getCell('O28').value = Number(this.egresosMensuales);
        worksheet.getCell('O29').value = Number(this.obligFinancieras);
        worksheet.getCell('O30').value = Number(this.otrosEgresos);
        worksheet.getCell('O32').value = Number(this.totalActivos);
        worksheet.getCell('O33').value = Number(this.totalPasivos);
      }

      const buffer = await workbook.xlsx.writeBuffer();
      const blob = new Blob([buffer], {
        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      });
      saveAs(blob, 'Solicitud_Credito.xlsx');
    } catch (error) {
      console.error('Error loading template', error);
    }
  }

  async loadTemplate(workbook: ExcelJS.Workbook) {
    try {
      const data: ArrayBuffer = await firstValueFrom(
        this.http.get('/assets/SOLICITAR_CREDITO.xlsx', {
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