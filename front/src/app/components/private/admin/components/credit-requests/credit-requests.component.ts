import { Component, OnInit } from '@angular/core';
import { RequestCreditService } from '../../../../../services/request-credit.service';
import { AllCreditRequestDataService } from '../../../../../services/allCreditRequestData.service';
import { CommonModule } from '@angular/common';
import { TableModule } from 'primeng/table';
import { PaginatorModule } from 'primeng/paginator';
import { InputTextModule } from 'primeng/inputtext';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';

@Component({
  selector: 'app-credit-requests',
  standalone: true,
  imports: [CommonModule, TableModule, PaginatorModule, InputTextModule, FormsModule],
  templateUrl: './credit-requests.component.html',
  styleUrls: ['./credit-requests.component.css']
})
export class CreditRequestsComponent implements OnInit {
  creditRequests: any[] = [];
  totalRecords: number = 0;
  loading: boolean = true;
  searchQuery: string = '';

  constructor(
    private requestCreditService: RequestCreditService,
    private allCreditRequestDataService: AllCreditRequestDataService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.loadCreditRequests();
  }

  loadCreditRequests(page: number = 1, size: number = 10): void {
    this.loading = true;
    this.requestCreditService.getAll({ page, size, search: this.searchQuery }).subscribe({
      next: response => {
        this.creditRequests = response.data;
        this.totalRecords = parseInt(response.total, 10);
        this.loading = false;
        console.log('CreditRequestsComponent initialized', this.creditRequests);
      },
      error: err => {
        console.error('Error al cargar solicitudes de crédito', err);
        this.loading = false;
      }
    });
  }

  onSearch(): void {
    this.loadCreditRequests();
  }

  onPageChange(event: any): void {
    this.loadCreditRequests(event.page + 1, event.rows);
  }

  generateData(request: any): void {
    const userId = request.idUsuario;

    this.allCreditRequestDataService.getAllData(userId).subscribe(data => {
      console.log('Datos recolectados:', data);
    });
  }
}
