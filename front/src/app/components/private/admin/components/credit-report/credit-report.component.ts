import { Component, ViewEncapsulation } from '@angular/core';
import { forkJoin } from 'rxjs';
import { map } from 'rxjs/operators';
import { RequestCreditService } from '../../../../../services/request-credit.service';
import { UserService } from '../../../../../services/user.service';
import { LineasCreditoService } from '../../../../../services/lineas-credito.service';
import * as ExcelJS from 'exceljs';
import { saveAs } from 'file-saver';
import { FormsModule } from '@angular/forms';
import { ToastModule } from 'primeng/toast';
import { CalendarModule } from 'primeng/calendar';
import { ButtonModule } from 'primeng/button';
import { MessageService, PrimeNGConfig } from 'primeng/api';

@Component({
  selector: 'app-credit-report',
  standalone: true,
  imports: [FormsModule, ToastModule, CalendarModule, ButtonModule],
  providers: [MessageService],
  templateUrl: './credit-report.component.html',
  styleUrls: ['./credit-report.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class CreditReportComponent {
  startDate: string | null = null;
  endDate: string | null = null;
  maxDate: Date = new Date();

  constructor(
    private requestCreditService: RequestCreditService,
    private userService: UserService,
    private lineasCreditoService: LineasCreditoService,
    private messageService: MessageService,
    private primengConfig: PrimeNGConfig
  ) {}

  ngOnInit() {
    this.primengConfig.ripple = true;
    this.primengConfig.setTranslation({
      dayNames: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
      dayNamesShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
      dayNamesMin: ["D", "L", "M", "X", "J", "V", "S"],
      monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
      monthNamesShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
      today: "Hoy",
      clear: "Limpiar",
      dateFormat: "dd-mm-yyyy",
      firstDayOfWeek: 1
    });
  }  
  private formatDate(date: Date): string {
    const year = date.getFullYear();
    const month = ('0' + (date.getMonth() + 1)).slice(-2);
    const day = ('0' + date.getDate()).slice(-2);
    return `${year}-${month}-${day}`;
  }

  private getMonthName(date: Date): string {
    const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    return monthNames[date.getMonth()];
  }

  private getYear(date: Date): string {
    return date.getFullYear().toString();
  }

  generateExcel(): void {
    if (this.startDate && this.endDate) {
      const formattedStartDate = this.formatDate(new Date(this.startDate));
      const formattedEndDate = this.formatDate(new Date(this.endDate));

      this.requestCreditService
        .getCreditsByDateRange(formattedStartDate, formattedEndDate)
        .subscribe({
          next: (credits) => {
            if (Array.isArray(credits) && credits.length === 0) {
              this.messageService.add({ severity: 'warn', summary: 'Advertencia', detail: 'No hay créditos solicitados en el rango de fechas seleccionado.' });
            } else {
              console.log("Datos recibidos del backend:", credits);
              this.fetchAdditionalInfo(credits);
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

  private fetchAdditionalInfo(credits: any[]): void {
    const requests = credits.map((credit) => {
      const userRequest = this.userService.getById(credit.idUsuario);
      const creditLineRequest = this.lineasCreditoService.obtenerLineaCreditoPorId(credit.idLineaCredito);
      return forkJoin([userRequest, creditLineRequest]).pipe(
        map(([user, creditLine]) => ({
          ...credit,
          numeroDocumento: user.numeroDocumento,
          nombreCompleto: `${user.primerNombre} ${user.segundoNombre || ''} ${user.primerApellido} ${user.segundoApellido || ''}`,
          nombreLineaCredito: creditLine.nombre
        }))
      );
    });

    forkJoin(requests).subscribe((enhancedCredits) => {
      this.createExcelFile(enhancedCredits);
    });
  }

  private createExcelFile(credits: any[]): void {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Solicitudes de Crédito');

    worksheet.columns = [
      { header: 'ID Crédito', key: 'id', width: 10 },
      { header: 'Documento Usuario', key: 'numeroDocumento', width: 20 },
      { header: 'Nombre Completo', key: 'nombreCompleto', width: 35 },
      { header: 'Línea de Crédito', key: 'nombreLineaCredito', width: 25 },
      { header: 'Monto Solicitado', key: 'montoSolicitado', width: 20 },
      { header: 'Tasa de Interés (%)', key: 'tasaInteres', width: 20 },
      { header: 'Plazo Quincenal', key: 'plazoQuincenal', width: 15 },
      { header: 'Valor Cuota', key: 'valorCuotaQuincenal', width: 15 },
      { header: 'Fecha de Solicitud', key: 'fechaSolicitud', width: 20 },
    ];

    credits.forEach((credit) => {
      worksheet.addRow({
        id: credit.id,
        numeroDocumento: Number(credit.numeroDocumento),
        nombreCompleto: credit.nombreCompleto,
        nombreLineaCredito: credit.nombreLineaCredito,
        montoSolicitado: Number(credit.montoSolicitado),
        tasaInteres: Number(credit.tasaInteres),
        plazoQuincenal: Number(credit.plazoQuincenal),
        valorCuotaQuincenal: Number(credit.valorCuotaQuincenal),
        fechaSolicitud: credit.fechaSolicitud,
      });
    });

    const monthStart = this.startDate ? this.getMonthName(new Date(this.startDate)) : '';
    const monthEnd = this.endDate ? this.getMonthName(new Date(this.endDate)) : '';
    const reportYear = this.startDate ? this.getYear(new Date(this.startDate)) : '';

    const fileName = `Solicitudes_Creditos_${monthStart}_${monthEnd}_${reportYear}.xlsx`;

    workbook.xlsx.writeBuffer().then((data) => {
      const blob = new Blob([data], {
        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      });
      saveAs(blob, fileName);
    });
  }
}
