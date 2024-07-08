import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { User, UserService } from '../../../../../../../services/user.service';
import { LoginService } from '../../../../../../../services/login.service';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { DocumentType, DocumentTypeService } from '../../../../../../../services/document-type.service';
import { MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';

@Component({
  selector: 'app-user',
  standalone: true,
  imports: [ CommonModule, ReactiveFormsModule, FormsModule, ToastModule ],
  providers: [MessageService],
  templateUrl: './user.component.html',
  styleUrl: './user.component.css'
})
export class UserComponent implements OnInit {
  userForm: FormGroup;
  user!: User;

  documentTypes: DocumentType[] = [];

  constructor(
    private fb: FormBuilder,
    private userService: UserService,
    private loginService: LoginService,
    private docTypeService: DocumentTypeService,
    private messageService: MessageService
  ) {
    this.userForm = this.fb.group({
      id: [''],
      primerNombre: ['', Validators.required],
      segundoNombre: [''],
      primerApellido: ['', Validators.required],
      segundoApellido: [''],
      idTipoDocumento: ['', Validators.required],
      numeroDocumento: ['', Validators.required],
    });
  }

  ngOnInit(): void {
    const token = this.loginService.getTokenClaims();

    if (token) {
      this.userService.getById(token.userId).subscribe(user => {
        this.user = user;
        this.userForm.patchValue(user);
      });
    }

    this.docTypeService.getAll().subscribe(types => {
      this.documentTypes = types;
    });
  }

  guardarUsuario(): void {
    if (this.userForm.valid) {
      const usuarioData = { ...this.user, ...this.userForm.value };
      if (usuarioData.id) {
        this.userService.update(usuarioData).subscribe({
          next: () => {
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Información básica actualizada correctamente' });
          },
          error: (err) => {
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo actualizar la información básica' });
          }
        });
      } else {
        this.userService.create(usuarioData).subscribe({
          next: () => {
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Información básica actualizada correctamente' });
          },
          error: (err) => {
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo actualizar la información básica' });
          }
        });
      }
    }
  }
}