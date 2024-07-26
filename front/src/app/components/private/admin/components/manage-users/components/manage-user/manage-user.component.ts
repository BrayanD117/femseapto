import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

import { UserService } from '../../../../../../../services/user.service';

@Component({
  selector: 'app-manage-user',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './manage-user.component.html',
  styleUrl: './manage-user.component.css'
})
export class ManageUserComponent {
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
    this.userService.getAll({ page, size}).subscribe({
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

  changeState(id: number): void {
    const user = this.users.find(user => user.id === id);
    console.log("id", id);
    if (user) {
      this.userService.changeState(id).subscribe({
        next: response => {
          // Actualiza el estado del usuario en la lista
          user.activo = !user.activo;
          console.log('Estado del usuario actualizado');
        },
        error: err => {
          console.error('Error al cambiar el estado del usuario', err);
        }
      });
    }
  }

  editUser(id: number): void {
    // Navegar al formulario de edición
    //this.router.navigate(['/usuarios/editar', id]);
    const user = this.users.find(user => user.id === id);
    if(user)
      console.log(user);
  }
}
