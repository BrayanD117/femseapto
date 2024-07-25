import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { UserService } from '../../../../../services/user.service';
import { forkJoin } from 'rxjs';

@Component({
  selector: 'app-manage-users',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './manage-users.component.html',
  styleUrl: './manage-users.component.css'
})
export class ManageUsersComponent {
  users: any[] = [];
  totalRecords: number = 0;
  loading: boolean = true;
  searchQuery: string = '';
  rows: number = 10;
  currentPage: number = 1;
  totalPages: number = 0;
  pages: number[] = [];

  constructor(
    private userService: UserService,
  ) {}

  ngOnInit(): void {
    this.loadUsers();
  }

  loadUsers(page: number = 1, size: number = 10): void {
    this.loading = true;
    this.userService.getAll({ page, size, search: this.searchQuery }).subscribe({
      next: response => {
        const requests = response.data;
        this.users = requests;

        this.totalRecords = response.total;
        this.totalPages = Math.ceil(this.totalRecords / this.rows);
        this.pages = Array(this.totalPages).fill(0).map((x, i) => i + 1);
        this.loading = false;
        
      },
      error: err => {
        console.error('Error al cargar solicitudes de crédito', err);
        this.loading = false;
      }
    });
  }

  onSearch(): void {
    this.loadUsers();
  }

  onPageChange(page: number): void {
    this.currentPage = page;
    this.loadUsers(this.currentPage, this.rows);
  }
}
