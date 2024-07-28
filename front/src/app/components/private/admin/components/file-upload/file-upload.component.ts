import { Component } from '@angular/core';
import { FileUploadService } from '../../../../../services/file-upload.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-file-upload',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './file-upload.component.html',
  styleUrl: './file-upload.component.css'
})
export class FileUploadComponent {
  selectedFile: File | null = null;
  responseMessage: string | null = null;

  constructor(private fileUploadService: FileUploadService) {}

  onFileChange(event: Event) {
    const input = event.target as HTMLInputElement;
    if (input.files) {
      this.selectedFile = input.files[0];
    }
  }

  onUpload() {
    if (this.selectedFile) {
      this.fileUploadService.uploadFile(this.selectedFile).subscribe({
        next: (response) => {
          this.responseMessage = response.message;
        },
        error: err => {
          console.error('Error al subir el archivo', err);
          this.responseMessage = 'Error al subir el archivo';
        }
      });
    } else {
      this.responseMessage = 'Por favor, selecciona un archivo';
    }
  }
}