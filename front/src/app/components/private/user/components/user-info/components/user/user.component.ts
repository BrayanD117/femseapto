import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { User, UserService } from '../../../../../../../services/user.service';
import { LoginService } from '../../../../../../../services/login.service';
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule } from '@angular/forms';


@Component({
  selector: 'app-user',
  standalone: true,
  imports: [ CommonModule, ReactiveFormsModule ],
  templateUrl: './user.component.html',
  styleUrl: './user.component.css'
})
export class UserComponent implements OnInit {
  userForm: FormGroup;
  user!: User;

  constructor(
    private fb: FormBuilder,
    private userService: UserService,
    private loginService: LoginService
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
  }

  guardarUsuario(): void {
    if (this.userForm.valid) {
      const usuarioData = { ...this.user, ...this.userForm.value };
      if (usuarioData.id) {
        this.userService.update(usuarioData).subscribe(() => {
          console.log('Usuario actualizado', usuarioData);
        });
      } else {
        this.userService.create(usuarioData).subscribe(() => {
          console.log('Usuario creado', usuarioData);
        });
      }
    }
  }
}