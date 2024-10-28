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
        console.log("response", response);
        console.log("response.data", response.data);
        const jsonResponse = JSON.parse(response);
        const users = jsonResponse.data;
        console.log('Reporte de usuarios:', users);

        const workbook = new Workbook();
        const worksheet = workbook.addWorksheet('Reporte de Usuarios');

        worksheet.columns = [
          { header: 'Número de Documento', key: 'numeroDocumento', width: 20 },
          { header: 'Nombre', key: 'nombre', width: 30 },
          { header: 'Fecha de Actualización', key: 'fechaUltimaActualizacion', width: 20 },
        ];

        users.forEach((user: { numeroDocumento: string; nombre: string; fechaUltimaActualizacion: string; }) => {
          worksheet.addRow({
            numeroDocumento: user.numeroDocumento,
            nombre: user.nombre,
            fechaUltimaActualizacion: user.fechaUltimaActualizacion || '',
          });
        });

        workbook.xlsx.writeBuffer().then((buffer) => {
          const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
          fs.saveAs(blob, 'Reporte_Usuarios.xlsx');
        });
      },
      (error) => {
        console.error('Error al obtener el reporte de usuarios:', error);
        alert('Hubo un error al obtener el reporte de usuarios. Revisa la consola para más detalles.');
      }
    );
  }
}
