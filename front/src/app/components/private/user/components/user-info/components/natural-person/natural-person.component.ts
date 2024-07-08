import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { LoginService } from '../../../../../../../services/login.service';
import { NaturalPerson, NaturalpersonService } from '../../../../../../../services/naturalperson.service';
import { Gender, GenderService } from '../../../../../../../services/gender.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-natural-person',
  standalone: true,
  imports: [ CommonModule, ReactiveFormsModule ],
  templateUrl: './natural-person.component.html',
  styleUrl: './natural-person.component.css'
})
export class NaturalPersonComponent implements OnInit {
  natPersonForm: FormGroup;
  natPerson!: NaturalPerson;
  genders: Gender[] = [];

  constructor(private fb: FormBuilder, private naturalPersonService: NaturalpersonService, private loginService: LoginService, private genderService: GenderService) { 
    this.natPersonForm = this.fb.group({
      id: [''],
      idUsuario: ['', Validators.required],
      idGenero: ['', Validators.required],
      fechaExpDoc: ['', Validators.required],
      mpioExpDoc: ['', Validators.required],
      fechaNacimiento: ['', Validators.required],
      paisNacimiento: ['', Validators.required],
      mpioNacimiento: ['', Validators.required],
      otroLugarNacimiento: [''],
      mpioResidencia: ['', Validators.required],
      idZonaResidencia: ['', Validators.required],
      idTipoVivienda: ['', Validators.required],
      estrato: ['', Validators.required],
      direccionResidencia: ['', Validators.required],
      aniosAntigVivienda: ['', Validators.required],
      idEstadoCivil: ['', Validators.required],
      cabezaFamilia: ['', Validators.required],
      personasACargo: ['', Validators.required],
      tieneHijos: ['', Validators.required],
      numeroHijos: ['', Validators.required],
      correoElectronico: ['', [Validators.required, Validators.email]],
      telefono: ['', Validators.required],
      celular: ['', Validators.required],
      telefonoOficina: ['', Validators.required],
      idNivelEducativo: ['', Validators.required],
      profesion: ['', Validators.required],
      ocupacionOficio: ['', Validators.required],
      idEmpresaLabor: ['', Validators.required],
      idTipoContrato: ['', Validators.required],
      dependenciaEmpresa: ['', Validators.required],
      cargoOcupa: ['', Validators.required],
      aniosAntigEmpresa: ['', Validators.required],
      mesesAntigEmpresa: ['', Validators.required],
      mesSaleVacaciones: ['', Validators.required],
      nombreEmergencia: ['', Validators.required],
      numeroCedulaEmergencia: ['', Validators.required],
      numeroCelularEmergencia: ['', Validators.required],
    });
  }

  ngOnInit(): void {
    const token = this.loginService.getTokenClaims();

    if (token) {
      this.naturalPersonService.getByUserId(token.userId).subscribe(natPerson => {
        this.natPerson = natPerson;
        this.natPersonForm.patchValue(natPerson);
      });
    }

    this.genderService.getAll().subscribe(types => {
      this.genders = types;
    });
  }

  onSubmit(): void {
    if (this.natPersonForm.valid) {
      // Aquí es donde manejarás la lógica para enviar los datos al backend
      console.log(this.natPersonForm.value);
    } else {
      // Manejo de formulario inválido
      console.log('Formulario inválido');
    }
  }
}
