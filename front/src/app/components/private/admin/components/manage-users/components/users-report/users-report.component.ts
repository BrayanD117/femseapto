import { Component } from '@angular/core';
import { Workbook } from 'exceljs';
import * as fs from 'file-saver';
import { UserService } from '../../../../../../../services/user.service';

@Component({
  selector: 'app-users-report',
  standalone: true,
  imports: [],
  templateUrl: './users-report.component.html',
  styleUrls: ['./users-report.component.css'],
})
export class UsersReportComponent {
  constructor(private userService: UserService) {}

  downloadUserReport(): void {
    this.userService.getUserReport().subscribe(
      (response: any) => {
        const jsonResponse = JSON.parse(response);
        const users = jsonResponse.data;

        const workbook = new Workbook();
        const worksheet = workbook.addWorksheet('Reporte de Usuarios');

        worksheet.columns = [
          { header: 'Número de Documento', key: 'numeroDocumento', width: 30 },
          { header: 'Nombre', key: 'nombre', width: 30 },
          { header: 'Fecha de Actualización', key: 'fechaUltimaActualizacion', width: 20 },
        ];

        users.forEach((user: { numeroDocumento: string; nombre: string; fechaUltimaActualizacion: string; }) => {
          worksheet.addRow({
            numeroDocumento: Number(user.numeroDocumento),
            nombre: user.nombre,
            fechaUltimaActualizacion: user.fechaUltimaActualizacion || '',
          });
        });

        worksheet.autoFilter = {
          from: 'A1',
          to: 'C1',
        };

        const colombiaDate = new Date().toLocaleDateString('es-CO', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
        });
        const fileName = `Reporte_Usuarios_${colombiaDate}.xlsx`;

        workbook.xlsx.writeBuffer().then((buffer) => {
          const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
          fs.saveAs(blob, fileName);
        });
      },
      (error) => {
        console.error('Error al obtener el reporte de usuarios:', error);
        alert('Hubo un error al obtener el reporte de usuarios. Revisa la consola para más detalles.');
      }
    );
  }
}
