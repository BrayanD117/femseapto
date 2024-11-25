import { Component } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { CalendarModule } from 'primeng/calendar';
import { MessagesModule } from 'primeng/messages';
import { ButtonModule } from 'primeng/button';
import { TableModule } from 'primeng/table';
import { Workbook } from 'exceljs';
import * as fs from 'file-saver';
import { MessageService } from 'primeng/api';
import { UserService } from '../../../../../../services/user.service';
import { CompanyService } from '../../../../../../services/company.service';
import { CountriesService } from '../../../../../../services/countries.service';
import { DepartmentsService } from '../../../../../../services/departments.service';


@Component({
  selector: 'app-users-info-filter',
  standalone: true,
  imports: [CommonModule, FormsModule, CalendarModule, MessagesModule, TableModule, ButtonModule],
  providers: [MessageService],
  templateUrl: './users-info-filter.component.html',
  styleUrls: ['./users-info-filter.component.css'],
})
export class UsersInfoFilterComponent {
  startDate: Date | null = null;
  endDate: Date | null = null;
  users: any[] = [];

  constructor(
    private userService: UserService, 
    private messageService: MessageService,
    private companyService: CompanyService,
    private countriesService: CountriesService,
    private departmentsService: DepartmentsService
  ) {}

  filterByDates() {
    if (!this.startDate || !this.endDate) {
      this.messageService.add({
        severity: 'warn',
        summary: 'Fechas incompletas',
        detail: 'Por favor, selecciona ambas fechas',
      });
      return;
    }

    const fechaInicio = this.startDate.toISOString().split('T')[0];
    const fechaFin = this.endDate.toISOString().split('T')[0];

    this.userService.getUsersByDateRange(fechaInicio, fechaFin).subscribe({
      next: (response: any) => {
        console.log('Datos recibidos del backend:', response.data);
        if (response.success) {
          this.users = response.data;
          this.messageService.add({
            severity: 'success',
            summary: 'Usuarios encontrados',
            detail: `Se encontraron ${this.users.length} usuarios.`,
          });
        } else {
          this.messageService.add({
            severity: 'error',
            summary: 'Error',
            detail: response.message || 'Error al filtrar usuarios',
          });
        }
      },
      error: (error) => {
        console.error(error);
        this.messageService.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Ocurrió un error en la solicitud',
        });
      },
    });
  }

  async generateExcelForUsers() {
    if (this.users.length === 0) {
      this.messageService.add({
        severity: 'warn',
        summary: 'Sin datos',
        detail: 'No hay usuarios para generar el Excel',
      });
      return;
    }
  
    for (const user of this.users) {
      console.log('Generando Excel para usuario:', user);
      const workbook = new Workbook();
      await fetch('/assets/FORMATO_DE_VINCULACION.xlsx')
        .then((response) => response.arrayBuffer())
        .then((data) => workbook.xlsx.load(data));
  
      const worksheet = workbook.getWorksheet(1);
  
      if (worksheet) {
        // Datos personales
        worksheet.getCell('C6').value = user.primerApellido || '';
        worksheet.getCell('L6').value = user.segundoApellido || '';
        worksheet.getCell('S6').value = `${user.primerNombre || ''} ${user.segundoNombre || ''}`.trim();
        worksheet.getCell('D7').value = user.numeroDocumento ? Number(user.numeroDocumento) : '';
        worksheet.getCell('L7').value = user.fechaExpedicionDoc || '';
        worksheet.getCell('Q7').value = user.nombreMpioExpDoc || '';
        worksheet.getCell('AA7').value = user.fechaNacimiento || '';
        worksheet.getCell('AH7').value = user.nombrePaisNacimiento || 'N/A';
        worksheet.getCell('A8').value = `GENERO: ${user.generoNombre} `|| 'N/A';
        worksheet.getCell('B10').value = user.estadoCivil || '';
        worksheet.getCell('L10').value = user.nombreNivelEducativo || '';
        worksheet.getCell('W10').value = user.tieneHijos || '';
        worksheet.getCell('AH10').value = user.numeroHijos || '';
        worksheet.getCell('B11').value = user.correoElectronico || '';
        worksheet.getCell('N11').value = Number(user.telefono) || '';
        worksheet.getCell('AA11').value = Number(user.celular) || '';
        worksheet.getCell('B12').value = user.nombrePaisNacimiento || '';
        worksheet.getCell('H12').value = user.profesion || '';
        worksheet.getCell('Y12').value = user.ocupacionOficio || '';
        // worksheet.getCell('B14').value = user.nombreTipoEmpresa || '';
        worksheet.getCell('R14').value = user.cargoOcupa || '';

        // Dirección y datos de vivienda
        worksheet.getCell('E8').value = user.direccionResidencia || '';
        worksheet.getCell('R8').value = user.nombreMpioResidencia || '';
        worksheet.getCell('AC8').value = user.nombreDptoResidencia || 'N/A';
        worksheet.getCell('B9').value = user.antiguedadVivienda || '';
        worksheet.getCell('H9').value = user.personasACargo || '';
        worksheet.getCell('K9').value = user.zonaGeografica || '';
        worksheet.getCell('Q9').value = user.tipoVivienda || '';
        worksheet.getCell('AH9').value = user.estrato || '';

        // Datos empresa
        worksheet.getCell('B13').value = user.actividadEconomicaEmpresa || '';
        worksheet.getCell('K13').value = user.ciiuEmpresa || '';
        worksheet.getCell('R13').value = user.nombreEmpresaLabor || '';
        worksheet.getCell('AG13').value = user.nitEmpresa || '';
        worksheet.getCell('W15').value = Number(user.telefonoEmpresa) || '';
        worksheet.getCell('B15').value = user.direccionEmpresa || '';
        worksheet.getCell('L15').value = user.municipioEmpresa || '';

        // Datos financieros
        worksheet.getCell('G34').value = Number(user.ingresosMensuales) || 0;
        worksheet.getCell('X34').value = Number(user.egresosMensuales) || 0;
        worksheet.getCell('G35').value = Number(user.otrosIngresosMensuales) || 0;
        worksheet.getCell('X35').value = Number(user.otrosEgresosMensuales) || 0;
        worksheet.getCell('G36').value = Number(user.totalIngresosMensuales) || 0;
        worksheet.getCell('X36').value = Number(user.totalEgresosMensuales) || 0;
        worksheet.getCell('G37').value = user.conceptoOtrosIngresos || '';
        worksheet.getCell('X37').value = Number(user.totalActivos) || 0;
        worksheet.getCell('X38').value = Number(user.totalPasivos) || 0;
        worksheet.getCell('X39').value = Number(user.totalPatrimonio) || 0;

        const familiares = user.familiares.slice(0, 4);
        const startRow = 42;
        familiares.forEach((familiar: any, index: number) => {
          const row = startRow + index;
          worksheet.getCell(`A${row}`).value = familiar.nombreCompleto || '';
          worksheet.getCell(`H${row}`).value = familiar.parentesco || '';
          worksheet.getCell(`K${row}`).value = familiar.numeroDocumento ? Number(familiar.numeroDocumento) : '';
          worksheet.getCell(`M${row}`).value = familiar.tipoDocumento || '';
          worksheet.getCell(`N${row}`).value = familiar.genero || '';
          worksheet.getCell(`O${row}`).value = familiar.fechaNacimiento || '';
          worksheet.getCell(`V${row}`).value = familiar.nivelEducativo || '';
          worksheet.getCell(`AB${row}`).value = familiar.trabaja ? 'Sí' : 'No';
          worksheet.getCell(`AE${row}`).value = familiar.celular ? Number(familiar.celular) : '';
        });

        const referencias = user.referencias.slice(0, 2);
        const startRowRef = 48;
        referencias.forEach((referencia: any, index: number) => {
          const row = startRowRef + index;
          worksheet.getCell(`A${row}`).value = referencia.nombreRazonSocial || '';
          worksheet.getCell(`K${row}`).value = referencia.abreviatura || '';
          worksheet.getCell(`L${row}`).value = referencia.direccion || '';
          worksheet.getCell(`V${row}`).value = referencia.ciudad || '';
          worksheet.getCell(`AD${row}`).value = referencia.telefono ? Number(referencia.telefono) : '';
        });

        worksheet.getCell('G51').value = user.transaccionesMonedaExtranjera || '';
        worksheet.getCell('L51').value = user.monedaTransaccion || '';
        worksheet.getCell('Y51').value = user.otrasOperaciones || '';
        worksheet.getCell('G52').value = user.cuentaExtranjera || '';
        worksheet.getCell('L52').value = user.bancoExtranjera || '';
        worksheet.getCell('P52').value = user.numeroCuentaExtranjera || '';
        worksheet.getCell('X52').value = user.monedaCuenta || '';
        worksheet.getCell('AD52').value = user.ciudadCuenta || '';
        worksheet.getCell('AH52').value = user.paisCuenta || '';

        worksheet.getCell('A55').value = user.asa || '';
      }
      const fileName = `Formato_Vinculacion_${user.numeroDocumento}.xlsx`;
      await workbook.xlsx.writeBuffer().then((buffer) => {
        const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        fs.saveAs(blob, fileName);
      });
    }
  
    this.messageService.add({
      severity: 'success',
      summary: 'Excels generados',
      detail: 'Los archivos Excel han sido generados con éxito.',
    });
  }  
}
