import { Component } from '@angular/core';
import { CreditBalanceService } from '../../../../../services/credit-balance.service';
import { SavingBalanceService } from '../../../../../services/saving-balance.service';
import { FinancialInfoService } from '../../../../../services/financial-info.service';
import { CommonModule } from '@angular/common';
import * as ExcelJS from 'exceljs';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { Router } from '@angular/router';

@Component({
  selector: 'app-info-upload',
  standalone: true,
  imports: [CommonModule, ToastModule],
  providers: [MessageService],
  templateUrl: './info-upload.component.html',
  styleUrls: ['./info-upload.component.css']
})
export class InfoUploadComponent {
  selectedFiles: { [key: string]: File | null } = {
    credit: null,
    saving: null,
    maxAmount: null
  };

  messages: { [key: string]: string | null } = {
    credit: null,
    saving: null,
    maxAmount: null
  };

  constructor(private creditBalanceService: CreditBalanceService,
    private savingBalanceService: SavingBalanceService,
    private financialInfoService: FinancialInfoService,
    private messageService: MessageService,
    private router: Router
  ) {}

  onFileSelected(event: any, type: string) {
    const file: File = event.target.files[0];
    const validTypes = ['text/csv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    if (file && validTypes.includes(file.type)) {
      this.selectedFiles[type] = file;
      this.messages[type] = null;
    } else {
      this.messages[type] = 'Please select a valid CSV or XLSX file.';
      this.selectedFiles[type] = null;
    }
  }

  async onFileUpload(type: string) {
    const file = this.selectedFiles[type];
    if (file) {
      const fileReader = new FileReader();

      fileReader.onload = async (e: any) => {
        const arrayBuffer = e.target.result;
        const workbook = new ExcelJS.Workbook();
        await workbook.xlsx.load(arrayBuffer);
        const worksheet = workbook.getWorksheet(1);

        if (worksheet) {
          const jsonData: any[] = [];
          worksheet.eachRow((row, rowNumber) => {
            if (rowNumber > 1) { // Assuming first row is the header
              const rowData = this.extractRowData(type, row);
              jsonData.push(rowData);
            }
          });

          this.uploadData(type, jsonData);
        } else {
          this.messages[type] = 'No valid worksheet found in the file.';
        }
      };

      fileReader.readAsArrayBuffer(file);
    }
  }

  extractRowData(type: string, row: any): any {
    const creditClosingDate = new Date(row.getCell(9).value).toISOString().split('T')[0];
    const savingClosingDate = new Date(row.getCell(5).value).toISOString().split('T')[0];
    switch (type) {
      case 'credit':
        return {
          numeroDocumento: row.getCell(1).value,
          idLineaCredito: row.getCell(2).value,
          cuotaActual: row.getCell(3).value,
          cuotasTotales: row.getCell(4).value,
          valorSolicitado: row.getCell(5).value,
          cuotaQuincenal: row.getCell(6).value,
          valorPagado: row.getCell(7).value,
          valorSaldo: row.getCell(8).value,
          fechaCorte: creditClosingDate,
        };
      case 'saving':
        return {
          numeroDocumento: row.getCell(1).value,
          idLineaAhorro: row.getCell(2).value,
          ahorroQuincenal: row.getCell(3).value,
          valorSaldo: row.getCell(4).value,
          fechaCorte: savingClosingDate
        };
      case 'maxAmount':
        return {
          numeroDocumento: row.getCell(1).value,
          montoMaxAhorro: row.getCell(2).value
        };
      default:
        return {};
    }
  }

  uploadData(type: string, data: any[]) {
    let service;

    switch (type) {
      case 'credit':
        service = this.creditBalanceService;
        break;
      case 'saving':
        service = this.savingBalanceService;
        break;
      case 'maxAmount':
        service = this.financialInfoService;
        break;
    }

    if (service) {
      service.uploadData(data).subscribe({
        next: (response) => {
          this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Archivo plano cargado correctamente.' }); 
          //console.log(response);
          this.messages[type] = 'File uploaded successfully';
        },
        error: (err) => {
          this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar el archivo plano. Por favor, intente otra vez' });
          console.error("Error: ", err);
          this.messages[type] = `Failed to upload file: ${err}`;
        }
      });
    }
  }
}
