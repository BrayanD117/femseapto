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
  selectedCreditFile: File | null = null;
  creditMessage: string | null = null;

  selectedSavingFile: File | null = null;
  savingMessage: string | null = null;

  selectedMaxAmountFile: File | null = null;
  maxAmountMessage: string | null = null;

  constructor(private creditBalanceService: CreditBalanceService,
    private savingBalanceService: SavingBalanceService, private financialInfoService: FinancialInfoService,
  ) {}

  onCreditFileSelected(event: any) {
    const file: File = event.target.files[0];
    const validTypes = ['text/csv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    if (file && validTypes.includes(file.type)) {
      this.selectedCreditFile = file;
      this.creditMessage = null;
    } else {
      this.creditMessage = 'Please select a valid CSV or XLSX file.';
      this.selectedCreditFile = null;
    }
  }

  async onCreditFileUpload() {
    if (this.selectedCreditFile) {
      const fileReader = new FileReader();

      fileReader.onload = async (e: any) => {
        const arrayBuffer = e.target.result;
        const workbook = new ExcelJS.Workbook();
        await workbook.xlsx.load(arrayBuffer);
        const worksheet = workbook.getWorksheet(1);

        if (worksheet) {
          const jsonData: any[] = [];
          worksheet.eachRow((row, rowNumber) => {
            if (rowNumber > 0) {
              const rowData = {
                numeroDocumento: row.getCell(1).value,
                idLineaCredito: row.getCell(2).value,
                cuotaActual: row.getCell(3).value,
                cuotasTotales: row.getCell(4).value,
                valorSolicitado: row.getCell(5).value,
                valorPagado: row.getCell(6).value,
                valorSaldo: row.getCell(7).value
              };
              jsonData.push(rowData);
              console.log("JSON DATA: ", jsonData);
            }
          });

          this.creditBalanceService.uploadData(jsonData).subscribe(
            response => {
              console.log(response);
              this.creditMessage = 'File uploaded successfully';
            },
            error => {
              console.error("Error: ", error);
              this.creditMessage = `Failed to upload file: ${error}`;
            }
          );
        } else {
          this.creditMessage = 'No valid worksheet found in the file.';
        }
      };

      fileReader.readAsArrayBuffer(this.selectedCreditFile);
    }
  }

  onSavingFileSelected(event: any) {
    const file: File = event.target.files[0];
    const validTypes = ['text/csv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    if (file && validTypes.includes(file.type)) {
      this.selectedSavingFile = file;
      this.savingMessage = null;
    } else {
      this.savingMessage = 'Please select a valid CSV or XLSX file.';
      this.selectedSavingFile = null;
    }
  }

  async onSavingFileUpload() {
    if (this.selectedSavingFile) {
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
              const rowData = {
                numeroDocumento: row.getCell(1).value,
                idLineaAhorro: row.getCell(2).value,
                valorSaldo: row.getCell(3).value
              };
              jsonData.push(rowData);
              console.log("JSON DATA: ", jsonData);
            }
          });

          this.savingBalanceService.uploadData(jsonData).subscribe(
            response => {
              console.log(response);
              this.savingMessage = 'File uploaded successfully';
            },
            error => {
              console.error("Error: ", error);
              this.creditMessage = `Failed to upload file: ${error}`;
            }
          );
        } else {
          this.savingMessage = 'No valid worksheet found in the file.';
        }
      };

      fileReader.readAsArrayBuffer(this.selectedSavingFile);
    }
  }

  onMaxAmountFileSelected(event: any) {
    const file: File = event.target.files[0];
    const validTypes = ['text/csv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    if (file && validTypes.includes(file.type)) {
      this.selectedMaxAmountFile = file;
      this.maxAmountMessage = null;
    } else {
      this.maxAmountMessage = 'Please select a valid CSV or XLSX file.';
      this.selectedMaxAmountFile = null;
    }
  }

  async onMaxAmountFileUpload() {
    if (this.selectedMaxAmountFile) {
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
              const rowData = {
                numeroDocumento: row.getCell(1).value,
                montoMaxAhorro: row.getCell(2).value
              };
              jsonData.push(rowData);
              console.log("JSON DATA: ", jsonData);
            }
          });

          this.financialInfoService.uploadData(jsonData).subscribe(
            response => {
              this.maxAmountMessage = 'File uploaded successfully';
            },
            error => {
              this.maxAmountMessage = `Failed to upload file: ${error}`;
            }
          );
        } else {
          this.maxAmountMessage = 'No valid worksheet found in the file.';
        }
      };

      fileReader.readAsArrayBuffer(this.selectedMaxAmountFile);
    }
  }
}
