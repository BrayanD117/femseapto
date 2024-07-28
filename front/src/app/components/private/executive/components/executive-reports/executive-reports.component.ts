import { Component, OnInit } from '@angular/core';
import { FileUploadService } from '../../../../../services/file-upload.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-executive-reports',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './executive-reports.component.html',
  styleUrls: ['./executive-reports.component.css']
})
export class ExecutiveReportsComponent implements OnInit {

  files: string[] = [];

  constructor(private fileUploadService: FileUploadService) { }

  ngOnInit() {
    this.getFiles();
  }

  getFiles() {
    this.fileUploadService.getFiles().subscribe(
      (data: string[]) => {
        this.files = data;
        console.log('Files:', this.files || []);
      },
      (error: any) => {
        console.error('Error getting files:', error);
      }
    );
  }

  getDownloadUrl(fileName: string): string {
    return `http://localhost/femseapto/uploads/${fileName}`;
  }
}
