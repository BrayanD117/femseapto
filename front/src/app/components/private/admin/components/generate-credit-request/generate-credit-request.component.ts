import { Component, Input, OnInit } from '@angular/core';
import * as ExcelJS from 'exceljs';
import { saveAs } from 'file-saver';
import { HttpClient } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';
import { LineasCreditoService } from '../../../../../services/lineas-credito.service';
import { UserService } from '../../../../../services/user.service';

@Component({
  selector: 'app-generate-credit-request',
  standalone: true,
  templateUrl: './generate-credit-request.component.html',
  styleUrls: ['./generate-credit-request.component.css']
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
  @Input() userId: number | null = null;

  lineaCreditoNombre: string = '';
  nombreAsociado: string = '';

  constructor(
    private http: HttpClient,
    private lineasCreditoService: LineasCreditoService,
    private userService: UserService
  ) {}

  ngOnInit() {
    if (this.lineaCredito) {
      this.lineasCreditoService.getNameById(Number(this.lineaCredito)).subscribe({
        next: nombre => {
          this.lineaCreditoNombre = nombre;
        },
        error: err => {
          console.error('Error al obtener el nombre de la línea de crédito', err);
        }
      });
    }

    if (this.userId) {
      this.userService.getById(this.userId).subscribe({
        next: user => {
          this.nombreAsociado = `${user.primerNombre || ''} ${user.segundoNombre || ''} ${user.primerApellido || ''} ${user.segundoApellido || ''}`.trim();
        },
        error: err => {
          console.error('Error al obtener el nombre del usuario', err);
        }
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
      }

      const buffer = await workbook.xlsx.writeBuffer();
      const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
      saveAs(blob, 'Solicitud_Credito.xlsx');
    } catch (error) {
      console.error('Error loading template', error);
    }
  }

  async loadTemplate(workbook: ExcelJS.Workbook) {
    try {
      const data: ArrayBuffer = await firstValueFrom(this.http.get('/assets/SOLICITAR_CREDITO.xlsx', { responseType: 'arraybuffer' }));
      await workbook.xlsx.load(data);
    } catch (error) {
      console.error('Error reading the template file:', error);
      throw error;
    }
  }
}
