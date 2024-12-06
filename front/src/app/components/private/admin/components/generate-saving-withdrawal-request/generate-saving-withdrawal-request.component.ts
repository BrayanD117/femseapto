import { Component, OnInit, Input } from '@angular/core';
import { User, UserService } from '../../../../../services/user.service';
import { NaturalPerson, NaturalpersonService } from '../../../../../services/naturalperson.service';
import { CitiesService } from '../../../../../services/cities.service';
import { RequestSavingWithdrawal, RequestSavingWithdrawalService } from '../../../../../services/request-saving-withdrawal.service';
import { CompanyService } from '../../../../../services/company.service';

import * as ExcelJS from 'exceljs';
import { saveAs } from 'file-saver';
import { HttpClient } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';

import { CommonModule, DatePipe } from '@angular/common';

@Component({
  selector: 'app-generate-saving-withdrawal-request',
  standalone: true,
  imports: [CommonModule],
  providers: [DatePipe],
  templateUrl: './generate-saving-withdrawal-request.component.html',
  styleUrl: './generate-saving-withdrawal-request.component.css'
})
export class GenerateSavingWithdrawalRequestComponent implements OnInit {
  @Input() userId: number = 0;
  @Input() savingWdRequestId: number = 0;

  isLoading: boolean = false;

  fullName: string = '';
  docNumber: number = 0;
  date: string | undefined;
  company: string = '';
  phoneNumber: string = '';
  city: string = '';
  totalAmount: number = 0;
  bank: string = '';
  accountNumber: string = '';
  savingLineId: number = 0;
  comments: string = '';
  continueSaving: string = '';

  constructor(private userService: UserService, private naturalPersonService: NaturalpersonService,
    private citiesService: CitiesService, private companyService: CompanyService,
    private savingWdRequestService: RequestSavingWithdrawalService, private http: HttpClient,
    private datePipe: DatePipe) { }

  ngOnInit() {
    this.loadData();
  }

  loadData() {
    if (this.userId) {
      this.userService.getById(this.userId).subscribe({
        next: (user: User) => {
          this.fullName = `${user.primerNombre || ''} ${user.segundoNombre || ''} ${user.primerApellido || ''} ${user.segundoApellido || ''}`.trim();
          this.docNumber = Number(user.numeroDocumento);
          
          this.naturalPersonService.getByUserId(this.userId).subscribe({
            next: (person: NaturalPerson) => {
              this.phoneNumber = person.celular;

              this.citiesService.getById(person.mpioResidencia).subscribe({
                next: (city) => {               
                  this.city = city.nombre;       
                },
                error: (err) => {
                  console.error('Error al obtener el municipio de residencia', err);
                }
              });

              this.companyService.getById(person.idEmpresaLabor).subscribe({
                next: (company) => {               
                  this.company = company.nombre;       
                },
                error: (err) => {
                  console.error('Error al obtener la empresa', err);
                }
              });
            },
            error: (err) => {
              console.error('Error al obtener los datos de persona natural', err);
            }
          });
        },
        error: (err) => {
          console.error('Error al obtener los datos del usuario', err);
        }
      });
    }


    this.savingWdRequestService.getById(this.savingWdRequestId).subscribe({
      next: (request: RequestSavingWithdrawal) => {
        this.date = request.fechaSolicitud.toString();
        this.totalAmount = Number(request.montoRetirar);
        this.bank = request.banco;
        this.accountNumber = request.numeroCuenta;
        this.savingLineId = request.idLineaAhorro;
        this.comments = request.observaciones;
        this.continueSaving = request.continuarAhorro;
        //this.logData();
      },
      error: (err) => {
        console.error('Error al obtener la solicitud de retiro de ahorro', err);
      }
    });
  }


  async generateExcel() {
    const workbook = new ExcelJS.Workbook();
    try {
      await this.loadTemplate(workbook);
      const worksheet = workbook.getWorksheet(1);

      // Agregar datos dinámicos
      if (worksheet) {

        await worksheet.protect('femseapto2024', { 
          selectLockedCells: true, 
          selectUnlockedCells: true 
        });

        worksheet.getCell('B8').value = this.fullName;
        worksheet.getCell('B9').value = this.docNumber;
        worksheet.getCell('I9').value = this.date;
        worksheet.getCell('B10').value = this.company;
        worksheet.getCell('I10').value = this.phoneNumber;
        worksheet.getCell('B11').value = this.city;
        worksheet.getCell('I11').value = Number(this.totalAmount);

        switch (this.savingLineId) {
          case 1: // Ahorro Vivienda
            worksheet.getCell('B17').value = 'X';
            break;
          case 2: // Ahorro Navideño
            worksheet.getCell('B16').value = 'X';
            break;
          case 3: // Ahorro Vacacional
            worksheet.getCell('B15').value = 'X';
            break;
          case 4: // Ahorro Extraordinario
            worksheet.getCell('B14').value = 'X';
            break;
          default:
            break;
        }

        if (this.bank !== '' && this.accountNumber !== '') {
          worksheet.getCell('F13').value = 'X';
        }

        worksheet.getCell('G13').value = `Banco: ${this.bank}`;
        worksheet.getCell('K13').value = `Nº Cuenta: ${this.accountNumber}`;

        worksheet.getCell('C16').value = this.comments;

        if (this.continueSaving === 'SI') {
          worksheet.getCell('D18').value = 'X';
        } else {
          worksheet.getCell('F18').value = 'X';
        }
      }

      const buffer = await workbook.xlsx.writeBuffer();
      const blob = new Blob([buffer], {
        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      });
      saveAs(blob, `Solicitud_Retiro_Ahorro_${this.docNumber}.xlsx`);
    } catch (err) {
      console.error('Error loading template', err);
    }
  }

  async loadTemplate(workbook: ExcelJS.Workbook) {
    try {
      const data: ArrayBuffer = await firstValueFrom(
        this.http.get('/assets/FORMATO_RETIRO_AHORRO.xlsx', {
          responseType: 'arraybuffer',
        })
      );
      await workbook.xlsx.load(data);
    } catch (error) {
      console.error('Error reading the template file:', error);
      throw error;
    }
  }

  onGenerateClick() {
    this.isLoading = true;
    setTimeout(() => {
      this.generateExcel();
      this.isLoading = false;
    }, 5000);
  }
}