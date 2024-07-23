import { Component, OnInit } from '@angular/core';
import { SolicitudAhorroService } from '../../../../../services/request-saving.service';
import { UserService, User } from '../../../../../services/user.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { GenerateSavingRequestComponent } from '../generate-saving-request/generate-saving-request.component';
import { forkJoin } from 'rxjs';

@Component({
  selector: 'app-saving-requests',
  standalone: true,
  imports: [CommonModule, FormsModule, GenerateSavingRequestComponent],
  templateUrl: './saving-requests.component.html',
  styleUrls: ['./saving-requests.component.css']
})
export class SavingRequestsComponent implements OnInit {
  savingRequests: any[] = [];
  totalRecords: number = 0;
  loading: boolean = true;
  searchQuery: string = '';
  rows: number = 10;
  currentPage: number = 1;
  totalPages: number = 0;
  pages: number[] = [];

  constructor(
    private solicitudAhorroService: SolicitudAhorroService,
    private userService: UserService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.loadSavingRequests();
  }

  loadSavingRequests(page: number = 1, size: number = 10): void {
    this.loading = true;
    this.solicitudAhorroService.getAll({ page, size, search: this.searchQuery }).subscribe({
      next: response => {
        const requests = response.data;
        const userObservables = requests.map((request: any) => this.userService.getById(request.idUsuario));

        forkJoin(userObservables).subscribe((users) => {
          const userArray = users as User[];
          this.savingRequests = requests.map((request: any, index: number) => {
            const user = userArray[index];
            return {
              ...request,
              numeroDocumento: user?.numeroDocumento || '',
              nombreAsociado: `${request.primerNombre || ''} ${request.segundoNombre || ''} ${request.primerApellido || ''} ${request.segundoApellido || ''}`.trim()
            };
          });

          this.totalRecords = response.total;
          this.totalPages = Math.ceil(this.totalRecords / this.rows);
          this.pages = Array(this.totalPages).fill(0).map((x, i) => i + 1);
          this.loading = false;
        });
      },
      error: err => {
        console.error('Error al cargar solicitudes de ahorro', err);
        this.loading = false;
      }
    });
  }

  onSearch(): void {
    this.loadSavingRequests();
  }

  onPageChange(page: number): void {
    this.currentPage = page;
    this.loadSavingRequests(this.currentPage, this.rows);
  }
}
