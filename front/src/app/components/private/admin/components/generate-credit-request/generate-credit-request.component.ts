import { Component, Input } from '@angular/core';
import * as ExcelJS from 'exceljs';
import { saveAs } from 'file-saver';
import { HttpClient } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';

@Component({
  selector: 'app-generate-credit-request',
  standalone: true,
  templateUrl: './generate-credit-request.component.html',
  styleUrls: ['./generate-credit-request.component.css']
})
export class GenerateCreditRequestComponent {
  @Input() montoSolicitado: number | string = 0;

  constructor(private http: HttpClient) {}

  async generateExcel() {
    const workbook = new ExcelJS.Workbook();
    try {
      await this.loadTemplate(workbook);
      const worksheet = workbook.getWorksheet(1);
  
      // Agregar datos dinámicos
      if (worksheet) {
        worksheet.getCell('G5').value = Number(this.montoSolicitado);
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
