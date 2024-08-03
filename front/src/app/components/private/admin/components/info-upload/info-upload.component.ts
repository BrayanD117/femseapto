import { Component } from '@angular/core';
import { CreditBalanceService } from '../../../../../services/credit-balance.service';
import { SavingBalanceService } from '../../../../../services/saving-balance.service';
import { FinancialInfoService } from '../../../../../services/financial-info.service';
import { CommonModule } from '@angular/common';
import * as ExcelJS from 'exceljs';

@Component({
  selector: 'app-info-upload',
  standalone: true,
  imports: [CommonModule],
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
              private financialInfoService: FinancialInfoService) {}

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
    switch (type) {
      case 'credit':
        return {
          numeroDocumento: row.getCell(1).value,
          idLineaCredito: row.getCell(2).value,
          cuotaActual: row.getCell(3).value,
          cuotasTotales: row.getCell(4).value,
          valorSolicitado: row.getCell(5).value,
          valorPagado: row.getCell(6).value,
          valorSaldo: row.getCell(7).value
        };
      case 'saving':
        return {
          numeroDocumento: row.getCell(1).value,
          idLineaAhorro: row.getCell(2).value,
          valorSaldo: row.getCell(3).value
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
      service.uploadData(data).subscribe(
        response => {
          console.log(response);
          this.messages[type] = 'File uploaded successfully';
        },
        error => {
          console.error("Error: ", error);
          this.messages[type] = `Failed to upload file: ${error}`;
        }
      );
    }
  }
}
