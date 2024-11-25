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
        worksheet.getCell('C6').value = user.primerApellido || '';
        worksheet.getCell('L6').value = user.segundoApellido || '';
        worksheet.getCell('S6').value = `${user.primerNombre || ''} ${user.segundoNombre || ''}`.trim();
        const numeroDocumentoCell = worksheet.getCell('D7');
        numeroDocumentoCell.value = user.numeroDocumento ? Number(user.numeroDocumento) : 0;
        numeroDocumentoCell.numFmt = '0';
        worksheet.getCell('L7').value = user.fechaExpedicionDoc || '';
        worksheet.getCell('Q7').value = user.mpioExpedicionDoc || '';
        worksheet.getCell('AA7').value = user.fechaNacimiento || '';
        if (user.paisNacimiento) {
          try {
            const country = await this.countriesService.getById(user.paisNacimiento).toPromise();
            console.log('Nombre del país:', country.nombre);
            worksheet.getCell('AH7').value = country.nombre || 'N/A';
          } catch (error) {
            console.error(`Error al obtener el nombre del país con ID ${user.paisNacimiento}:`, error);
            worksheet.getCell('AH7').value = 'N/A';
          }
        } else {
          worksheet.getCell('AH7').value = 'N/A';
        }

        if (user.idDptoResidencia) {
          try {
            const department = await this.departmentsService.getById(user.idDptoResidencia).toPromise();
            worksheet.getCell('AC8').value = department?.nombre || 'N/A';
          } catch {
            worksheet.getCell('AC8').value = 'N/A';
          }
        } else {
          worksheet.getCell('AC8').value = 'N/A';
        }

        worksheet.getCell('A8').value = `GENERO: ${user.idGenero || 'N/A'}`;
        worksheet.getCell('E8').value = user.direccionResidencia || '';
        worksheet.getCell('R8').value = user.mpioResidencia || '';
        worksheet.getCell('AC8').value = user.idDptoResidencia || '';
        const antiguedadCell = worksheet.getCell('B9');
        antiguedadCell.value = user.antiguedadVivienda ? user.antiguedadVivienda : 0;
        const personasACargoCell = worksheet.getCell('G9');
        personasACargoCell.value = user.personasACargo ? Number(user.personasACargo) : 0;
        personasACargoCell.numFmt = '0';
        worksheet.getCell('K9').value = user.idZonaResidencia || '';
        worksheet.getCell('Q9').value = user.idTipoVivienda || '';
        worksheet.getCell('AH9').value = user.estrato || '';
        worksheet.getCell('B10').value = user.idEstadoCivil || '';
        worksheet.getCell('L10').value = user.idNivelEducativo|| '';
        worksheet.getCell('W10').value = user.tieneHijos || '';
        worksheet.getCell('AH10').value = user.numeroHijos || '';
        worksheet.getCell('B11').value = user.correoElectronico || '';
        worksheet.getCell('N11').value = Number(user.telefono) || '';
        worksheet.getCell('AA11').value = Number(user.celular) || '';
        worksheet.getCell('B12').value = user.paisNacimiento || '';
        worksheet.getCell('H12').value = user.profesion || '';
        worksheet.getCell('Y12').value = user.ocupacionOficio || '';

        if (user.idEmpresaLabor) {
          try {
            const company = await this.companyService.getById(user.idEmpresaLabor).toPromise();
            console.log('Información de la empresa:', company);

            worksheet.getCell('B13').value = company.actividadEconomica || '';
            worksheet.getCell('K13').value = company.ciiu || '';
            worksheet.getCell('R13').value = company.nombre || '';
            worksheet.getCell('AG13').value = company.nit || '';
            worksheet.getCell('B14').value = company.idTipoEmpresa || '';
            worksheet.getCell('B15').value = company.direccion || '';
            worksheet.getCell('L15').value = company.idMunicipio || '';
            worksheet.getCell('W15').value = Number(company.telefono) || '';
          } catch (error) {
            console.error(`Error al obtener la información de la empresa con ID ${user.idEmpresaLabor}:`, error);
          }

          worksheet.getCell('R14').value = user.cargoOcupa || '';
        }
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
