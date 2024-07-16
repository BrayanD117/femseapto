import { Component, OnInit } from '@angular/core';
import { RequestCreditService } from '../../../../../services/request-credit.service';
import { UserService, User } from '../../../../../services/user.service';
import { AllCreditRequestDataService } from '../../../../../services/allCreditRequestData.service';
import { CommonModule } from '@angular/common';
import { TableModule } from 'primeng/table';
import { PaginatorModule } from 'primeng/paginator';
import { InputTextModule } from 'primeng/inputtext';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { GenerateCreditRequestComponent } from '../generate-credit-request/generate-credit-request.component';
import { forkJoin } from 'rxjs';

@Component({
  selector: 'app-credit-requests',
  standalone: true,
  imports: [CommonModule, TableModule, PaginatorModule, InputTextModule, FormsModule, GenerateCreditRequestComponent],
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
    private userService: UserService,
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
        const requests = response.data;
        const userObservables = requests.map((request: any) => this.userService.getById(request.idUsuario));

        forkJoin(userObservables).subscribe((users) => {
          const userArray = users as User[];
          this.creditRequests = requests.map((request: any, index: number) => {
            const user = userArray[index];
            return {
              ...request,
              numeroDocumento: user?.numeroDocumento || '',
              nombreAsociado: `${request.primerNombre || ''} ${request.segundoNombre || ''} ${request.primerApellido || ''} ${request.segundoApellido || ''}`.trim()
            };
          });

          this.totalRecords = response.total;
          this.loading = false;
        });
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
