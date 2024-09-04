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

  // Progress trackers for each type of file upload
  progress: { [key: string]: number } = {
    credit: 0,
    saving: 0,
    maxAmount: 0
  };

  // Track completed requests
  completedRequests: { [key: string]: number } = {
    credit: 0,
    saving: 0,
    maxAmount: 0
  };

  constructor(
    private creditBalanceService: CreditBalanceService,
    private savingBalanceService: SavingBalanceService,
    private financialInfoService: FinancialInfoService,
    private messageService: MessageService,
    private router: Router
  ) { }

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
    this.progress[type] = 0;

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
            if (rowNumber > 1) {
              const rowData = this.extractRowData(type, row);
              if (rowData) {
                jsonData.push(rowData);
              }
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
    const hasData = row.getCell(1).value || row.getCell(2).value || row.getCell(3).value ||
      row.getCell(4).value || row.getCell(5).value || row.getCell(6).value ||
      row.getCell(7).value || row.getCell(8).value || row.getCell(9).value;

    if (!hasData) {
      return null;
    }

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
        };
      case 'saving':
        return {
          numeroDocumento: row.getCell(1).value,
          idLineaAhorro: row.getCell(2).value,
          ahorroQuincenal: row.getCell(3).value,
          valorSaldo: row.getCell(4).value
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

  async uploadData(type: string, data: any[]) {
    let service;
    const batchSize = 50;
    const totalBatches = Math.ceil(data.length / batchSize);

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

    this.completedRequests[type] = 0;

    if (service) {
      const batchPromises = [];

      for (let i = 0; i < totalBatches; i++) {
        const batch = data.slice(i * batchSize, (i + 1) * batchSize);
        batchPromises.push(this.uploadBatch(service, batch, totalBatches, type));
      }

      try {
        await Promise.all(batchPromises);
        this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Archivo cargado correctamente.' });
      } catch (err) {
        this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar el archivo. Inténtelo de nuevo.' });
        console.error("Error: ", err);
      }
    }
  }

  async uploadBatch(service: any, batch: any[], totalBatches: number, type: string): Promise<void> {
    return new Promise((resolve, reject) => {
      service.uploadData(batch).subscribe({
        next: () => {
          this.completedRequests[type] += 1;
          this.updateProgress(type, totalBatches);
          resolve();
        },
        error: (err: any) => {
          reject(err);
        }
      });
    });
  }

  updateProgress(type: string, totalBatches: number) {
    const progressValue = Math.round((this.completedRequests[type] / totalBatches) * 100);
    this.progress[type] = progressValue;
  }
}
