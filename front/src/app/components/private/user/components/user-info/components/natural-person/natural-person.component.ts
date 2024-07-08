import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { LoginService } from '../../../../../../../services/login.service';
import { NaturalPerson, NaturalpersonService } from '../../../../../../../services/naturalperson.service';
import { Gender, GenderService } from '../../../../../../../services/gender.service';
import { CommonModule } from '@angular/common';
import { DepartmentsService } from '../../../../../../../services/departments.service';
import { CitiesService } from '../../../../../../../services/cities.service';
import { AutoCompleteModule } from 'primeng/autocomplete';

interface AutoCompleteCompleteEvent {
  originalEvent: Event;
  query: string;
}

interface Department {
  id: string;
  nombre: string;
}

interface City {
  id: string;
  nombre: string;
  id_departamento: string;
}

@Component({
  selector: 'app-natural-person',
  standalone: true,
  imports: [ CommonModule, ReactiveFormsModule, AutoCompleteModule ],
  templateUrl: './natural-person.component.html',
  styleUrl: './natural-person.component.css'
})
export class NaturalPersonComponent implements OnInit {
  natPersonForm: FormGroup;
  natPerson!: NaturalPerson;
  genders: Gender[] = [];

  departments: Department[] = [];
  selectedDepartment: Department | undefined;
  filteredDepartments: Department[] = [];

  cities: City[] = [];
  selectedCity: City | undefined;
  filteredCities: City[] = [];

  constructor(private fb: FormBuilder, private naturalPersonService: NaturalpersonService, private loginService: LoginService, private genderService: GenderService,
    private departmentsService: DepartmentsService, private citiesService: CitiesService
  ) { 
    this.natPersonForm = this.fb.group({
      id: [''],
      idUsuario: [''],
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
        this.loadDepartmentsAndCities();
      });
    }

    this.genderService.getAll().subscribe(types => {
      this.genders = types;
    });

    this.departmentsService.getDepartments().subscribe((departments) => {
      this.departments = departments;
    });
  }

  loadDepartmentsAndCities() {
    const cityId = this.natPerson.mpioExpDoc;
    console.log("CIUDAD", cityId);

    if (cityId) {
      this.citiesService.getCityById(cityId).subscribe(city => {
        this.selectedCity = city;
        console.log("CIUDAD new", this.selectedCity);
        if (this.selectedCity) {
          this.selectedDepartment = this.departments.find(dept => dept.id === this.selectedCity?.id_departamento);
        }
      });
    }
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

  filterDepartment(event: AutoCompleteCompleteEvent) {
    let filtered: Department[] = [];
    let query = event.query;

    for (let i = 0; i < this.departments.length; i++) {
        let dptm = this.departments[i];
        if (dptm.nombre.toLowerCase().indexOf(query.toLowerCase()) === 0) {
            filtered.push(dptm);
        }
    }

    this.filteredDepartments = filtered;
  }

  filterCity(event: AutoCompleteCompleteEvent) {
    let filtered: City[] = [];
    let query = event.query;

    for (let i = 0; i < this.cities.length; i++) {
        let city = this.cities[i];
        if (city.nombre.toLowerCase().indexOf(query.toLowerCase()) === 0) {
            filtered.push(city);
        }
    }

    this.filteredCities = filtered;
  }

  onDepartmentSelect() {
    if (this.selectedDepartment && this.selectedDepartment.id) {
      this.citiesService.getCitiesByDepartment(this.selectedDepartment.id.toString()).subscribe((cities) => {
        this.cities = cities;
        this.selectedCity = this.cities.find(muni => muni.id === this.natPerson.mpioExpDoc);
        this.filteredCities = []; // Clear filtered municipalities
      });
    }
  }
}