import { Component, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  FormBuilder,
  FormControl,
  FormGroup,
  ReactiveFormsModule,
  Validators,
} from '@angular/forms';

import { User, UserService } from '../../../../../../../services/user.service';
import {
  DocumentType,
  DocumentTypeService,
} from '../../../../../../../services/document-type.service';
import { Role, RoleService } from '../../../../../../../services/role.service';
import {
  AssociateType,
  AssociateTypeService,
} from '../../../../../../../services/associate-type.service';

import { Table, TableModule } from 'primeng/table';
import { TagModule } from 'primeng/tag';
import { IconFieldModule } from 'primeng/iconfield';
import { InputIconModule } from 'primeng/inputicon';
import { HttpClientModule } from '@angular/common/http';
import { InputTextModule } from 'primeng/inputtext';
import { MultiSelectModule } from 'primeng/multiselect';
import { DropdownModule } from 'primeng/dropdown';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { debounceTime, distinctUntilChanged } from 'rxjs/operators';

@Component({
  selector: 'app-manage-admin',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    TableModule,
    TagModule,
    IconFieldModule,
    InputTextModule,
    InputIconModule,
    MultiSelectModule,
    DropdownModule,
    HttpClientModule,
    ToastModule
  ],
  providers: [MessageService],
  templateUrl: './manage-admin.component.html',
  styleUrl: './manage-admin.component.css',
})
export class ManageAdminComponent {
  @ViewChild('dt2') dt2!: Table;

  users: User[] = [];
  editUserForm: FormGroup;
  searchControl: FormControl;
  selectedUser: User | null = null;
  isEditMode: boolean = false;

  totalRecords: number = 0;
  loading: boolean = true;
  searchQuery: string = '';
  rows: number = 10;
  currentPage: number = 1;
  totalPages: number = 0;
  pages: number[] = [];

  documentTypes: DocumentType[] = [];
  roles: Role[] = [];
  associatesTypes: AssociateType[] = [];

  selectedUserId: number | null = null;

  constructor(
    private fb: FormBuilder,
    private userService: UserService,
    private docTypeService: DocumentTypeService,
    private roleService: RoleService,
    private associateTypeService: AssociateTypeService,
    private messageService: MessageService,
  ) {
    this.editUserForm = this.fb.group({
      id: [null],
      idTipoDocumento: [null],
      numeroDocumento: [''],
      primerApellido: [''],
      segundoApellido: [''],
      primerNombre: [''],
      segundoNombre: [''],
      usuario: ['', Validators.required],
      id_rol: [1],
      id_tipo_asociado: [null],
      contrasenia: ['', Validators.required],
      activo: [1, Validators.required],
    });

    this.searchControl = new FormControl('');
  }

  ngOnInit(): void {
    this.loadUsers();

    this.searchControl.valueChanges
    .pipe(
      debounceTime(500),
      distinctUntilChanged()
    )
    .subscribe(searchQuery => {
      this.loadUsers(this.currentPage, this.rows, 1, searchQuery);
    });

    this.getAllDocTypes();
    this.getAllRoles();
    this.getAllAssociateTypes();
  }

  clear(table: Table) {
    table.clear();
  }

  loadUsers(page: number = 1, size: number = 10, idRol: number = 1, search: string = ''): void {
    this.loading = true;
    this.userService.getAll({ page, size, idRol, search }).subscribe({
      next: (response) => {
        this.users = response.data;
        this.totalRecords = response.total;
        this.loading = false;
      },
      error: (err) => {
        console.error('Error al cargar los usuarios', err);
        this.loading = false;
      },
    });
  }

  getAllDocTypes(): void {
    this.docTypeService.getAll().subscribe((types) => {
      this.documentTypes = types;
    });
  }

  getAllRoles(): void {
    this.roleService.getAll().subscribe((types) => {
      this.roles = types;
    });
  }

  getAllAssociateTypes(): void {
    this.associateTypeService.getAll().subscribe((types) => {
      this.associatesTypes = types;
    });
  }

  onPageChange(page: number): void {
    this.currentPage = page;
    this.loadUsers(this.currentPage, this.rows, 1, this.searchControl.value);
  }

  changeState(id: number): void {
    const user = this.users.find((user) => user.id === id);
    //console.log('id', id);
    if (user) {
      this.userService.changeState(id).subscribe({
        next: (response) => {
          // Actualiza el estado del usuario en la lista
          user.activo = user.activo === 0 ? 1 : 0;
          //console.log('Estado del usuario actualizado');
        },
        error: (err) => {
          console.error('Error al cambiar el estado del usuario', err);
        },
      });
    }
  }

  editUser(id: number): void {
    const user = this.users.find((user) => user.id === id);
    if (user) {
      this.isEditMode = true;
      this.editUserForm.patchValue(user);
      this.editUserForm.get('contrasenia')?.disable();
    }
  }

  createUser(): void {
    this.isEditMode = false;
    this.formReset();
    this.editUserForm.get('contrasenia')?.enable();
    this.editUserForm.patchValue({ activo: 1, id_rol: 1 }); // Establecer el estado activo como true por defecto
  }

  openResetPasswordModal(userId: number) {
    this.selectedUserId = userId;
  }

  confirmResetPassword() {
    if (this.selectedUserId !== null) {
      this.userService.resetPassword(this.selectedUserId).subscribe({
        next: (response) => {
          this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Contraseña restablecida con éxito.' });
        },
        error: (error) => {
          this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Error al restablecer la contraseña.' });
        }
      });
    }
  }

  submit(): void {
    //console.log(this.editUserForm.value);
    if (this.editUserForm.valid) {

      //console.log(this.editUserForm.value);
      const userFormData = this.editUserForm.value;
      if (this.isEditMode) {
        //console.log('antes', userFormData);
        this.userService.update(userFormData).subscribe({
          next: () => {
            //console.log('después', userFormData);
            const index = this.users.findIndex(
              (user) => user.id === userFormData.id
            );
            if (index !== -1) {
              this.users[index] = userFormData;
            }
            //userFormData.activo = userFormData.activo === 0 ? 1 : 0;
            //console.log('Usuario actualizado');
            this.formReset();
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Usuario actualizado correctamente' });
          },
          error: (err) => {
            console.error('Error al actualizar el usuario', err);
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo actualizar el usuario. Por favor, intente otra vez' });
          },
        });
      } else {
        //console.log(userFormData);
        this.userService.create(userFormData).subscribe({
          next: () => {
            this.users.push(userFormData);
            //console.log('Usuario creado');
            this.formReset();
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Usuario creado correctamente' });
          },
          error: (err) => {
            console.error('Error al crear el usuario', err);
            //console.log('Error al crear el usuario', err.error.id.message);
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo crear el usuario. Por favor, intente otra vez' });
          },
        });
      }
    }
  }

  formReset() {
    this.editUserForm.reset();
  }
}