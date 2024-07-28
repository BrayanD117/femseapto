import { Component, OnInit } from '@angular/core';
import { FileService } from '../../../../../services/file.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-file-list',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './file-list.component.html',
  styleUrl: './file-list.component.css'
})
export class FileListComponent implements OnInit {
  files: string[] = [];

  constructor(private fileService: FileService) {}

  ngOnInit() {
    this.loadFiles();
  }

  loadFiles() {
    this.fileService.listFiles().subscribe({
      next: (files: string[]) => {
        console.log(files)
        this.files = files;
      },
      error: error => {
        console.error('Error al cargar archivos', error);
      }
    }
      
    );
  }

  downloadFile(fileName: string) {
    this.fileService.downloadFile(fileName).subscribe({
      next: (blob) => {
        const link = document.createElement('a');
        const url = window.URL.createObjectURL(blob);
        link.href = url;
        link.download = fileName;
        link.click();
        window.URL.revokeObjectURL(url);
      },
      error: error => {
        console.error('Error al descargar el archivo', error);
      }
    }
      
    );
  }
}