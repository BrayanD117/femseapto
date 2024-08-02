import { Component } from '@angular/core';
import { CreditBalanceService } from '../../../../../services/credit-balance.service';
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
  selectedFile: File | null = null;
  message: string | null = null;

  constructor(private creditBalanceService: CreditBalanceService) {}

  onFileSelected(event: any) {
    const file: File = event.target.files[0];
    const validTypes = ['text/csv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    if (file && validTypes.includes(file.type)) {
      this.selectedFile = file;
      this.message = null;
    } else {
      this.message = 'Please select a valid CSV or XLSX file.';
      this.selectedFile = null;
    }
  }

  async onUpload() {
    if (this.selectedFile) {
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
              this.message = 'File uploaded successfully';
            },
            error => {
              console.error("Error: ", error);
              this.message = `Failed to upload file: ${error}`;
            }
          );
        } else {
          this.message = 'No valid worksheet found in the file.';
        }
      };

      fileReader.readAsArrayBuffer(this.selectedFile);
    }
  }
}
