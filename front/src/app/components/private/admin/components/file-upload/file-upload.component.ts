import { Component } from '@angular/core';
import { FileUploadService } from '../../../../../services/file-upload.service';
import { CommonModule } from '@angular/common';
import { FileUploadModule } from 'primeng/fileupload';
import { MessageModule } from 'primeng/message';
import { MessagesModule } from 'primeng/messages';
import { MessageService, PrimeNGConfig } from 'primeng/api';
import { HttpClientModule } from '@angular/common/http';
import { ToastModule } from 'primeng/toast';

interface FileUploadHandlerEvent {
    files: File[];
}

@Component({
  selector: 'app-file-upload',
  standalone: true,
  imports: [CommonModule, FileUploadModule, MessagesModule, MessageModule, HttpClientModule, ToastModule],
  providers: [MessageService],
  templateUrl: './file-upload.component.html',
  styleUrls: ['./file-upload.component.css']
})
export class FileUploadComponent {
  uploadedFiles: any[] = [];

  constructor(private fileUploadService: FileUploadService, private messageService: MessageService, private config: PrimeNGConfig) {}

  onFileSelect(event: FileUploadHandlerEvent) {
    for(let file of event.files) {
      if (file.type !== 'application/pdf') {
        this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Solo se permiten archivos PDF.' });
        continue;
      }

      if (file.size > 5242880) {
        this.messageService.add({ severity: 'error', summary: 'Error', detail: 'El archivo no debe superar los 5MB.' });
        continue;
      }

      this.uploadFile(file);
    }
  }

  uploadFile(file: File) {
    this.fileUploadService.uploadFile(file).subscribe({
      next: (response) => {
        this.messageService.add({ severity: 'success', summary: 'Success', detail: 'Archivo subido con éxito' });
        this.uploadedFiles.push(file);
      },
      error: err => {
        console.error('Error al subir el archivo', err);
        this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Error al subir el archivo' });
      }
    });
  }
}
