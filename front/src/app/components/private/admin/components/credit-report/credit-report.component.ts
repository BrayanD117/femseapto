import { Component, ViewEncapsulation } from '@angular/core';
import { RequestCreditService } from '../../../../../services/request-credit.service';
import * as ExcelJS from 'exceljs';
import { saveAs } from 'file-saver';
import { FormsModule } from '@angular/forms';
import { ToastModule } from 'primeng/toast';
import { MessageService, PrimeNGConfig } from 'primeng/api';

@Component({
  selector: 'app-credit-report',
  standalone: true,
  imports: [FormsModule, ToastModule],
  providers: [MessageService],
  templateUrl: './credit-report.component.html',
  styleUrls: ['./credit-report.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class CreditReportComponent {
  startDate: string | null = null;
  endDate: string | null = null;

  constructor(
    private requestCreditService: RequestCreditService,
    private messageService: MessageService,
    private primengConfig: PrimeNGConfig
  ) {}

  ngOnInit() {
    this.primengConfig.ripple = true;
  }

  generateExcel(): void {
    console.log("Fechas enviadas:", this.startDate, this.endDate);
    if (this.startDate && this.endDate) {
      this.requestCreditService
        .getCreditsByDateRange(this.startDate, this.endDate)
        .subscribe({
          next: (credits) => {
            if (Array.isArray(credits) && credits.length === 0) {
              this.messageService.add({ severity: 'warn', summary: 'Advertencia', detail: 'No hay créditos solicitados en el rango de fechas seleccionado.' });
            } else {
              console.log("Datos recibidos del backend:", credits);
              this.createExcelFile(credits);
            }
          },
          error: (err) => {
            console.error('Error al obtener los créditos:', err);
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Ocurrió un error al obtener los créditos.' });
          }
        });
    } else {
      this.messageService.add({ severity: 'warn', summary: 'Advertencia', detail: 'Por favor, seleccione ambas fechas.' });
    }
  }

  private createExcelFile(credits: any[]): void {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Solicitudes de Crédito');

    worksheet.columns = [
      { header: 'ID', key: 'id', width: 10 },
      { header: 'Usuario', key: 'idUsuario', width: 20 },
      { header: 'Monto Solicitado', key: 'montoSolicitado', width: 20 },
      { header: 'Plazo Quincenal', key: 'plazoQuincenal', width: 20 },
      { header: 'Valor Cuota', key: 'valorCuotaQuincenal', width: 20 },
      { header: 'Tasa de Interés %', key: 'tasaInteres', width: 20 },
      { header: 'Fecha de Solicitud', key: 'fechaSolicitud', width: 20 },
    ];

    credits.forEach((credit) => {
      worksheet.addRow({
        id: credit.id,
        idUsuario: credit.idUsuario,
        montoSolicitado: credit.montoSolicitado,
        plazoQuincenal: credit.plazoQuincenal,
        valorCuotaQuincenal: credit.valorCuotaQuincenal,
        tasaInteres: credit.tasaInteres,
        fechaSolicitud: credit.fechaSolicitud,
      });
    });

    workbook.xlsx.writeBuffer().then((data) => {
      const blob = new Blob([data], {
        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      });
      saveAs(blob, 'Solicitudes_Credito.xlsx');
    });
  }
}
