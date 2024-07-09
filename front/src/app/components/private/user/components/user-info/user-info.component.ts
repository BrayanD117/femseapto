import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, Validators } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { AccordionModule } from 'primeng/accordion';
import { AutoCompleteModule } from 'primeng/autocomplete';


import { UserComponent } from './components/user/user.component';
import { NaturalPersonComponent } from './components/natural-person/natural-person.component';
import { FinancialInfoComponent } from './components/financial-info/financial-info.component';

interface AutoCompleteCompleteEvent {
    originalEvent: Event;
    query: string;
}

interface Department {
    id: number;
    nombre: string;
}

interface City {
    id: number;
    nombre: string;
    id_departamento: number;
}

@Component({
  selector: 'app-user-info',
  standalone: true,
  imports: [FormsModule, CommonModule, AutoCompleteModule, AccordionModule, UserComponent, NaturalPersonComponent, FinancialInfoComponent],
  templateUrl: './user-info.component.html',
  styleUrls: ['./user-info.component.css']
})
export class UserInfoComponent implements OnInit {

  userForm: FormGroup;
  //personaNaturalForm: FormGroup;
  //infoFinancieraForm: FormGroup;

  constructor(private fb: FormBuilder) {
    this.userForm = this.fb.group({
      // Define aquí los campos del formulario y sus validaciones si es necesario
      nombreRazonSocial: ['', Validators.required],
      // ... otros campos
    });
  }

  ngOnInit(): void {
  }

  guardarUsuario(): void {
    if (this.userForm.valid) {
      // Lógica para guardar los datos del Usuario
      console.log('Usuario guardado', this.userForm.value);
    }
  }
}
