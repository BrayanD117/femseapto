import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { AutoCompleteModule } from 'primeng/autocomplete';
import { InputMaskModule } from 'primeng/inputmask';

// Services
import { LoginService } from '../../../../../../../services/login.service';
import { NaturalPerson, NaturalpersonService } from '../../../../../../../services/naturalperson.service';
import { Gender, GenderService } from '../../../../../../../services/gender.service';
import { DepartmentsService } from '../../../../../../../services/departments.service';
import { CitiesService } from '../../../../../../../services/cities.service';
import { Zone, ZoneService } from '../../../../../../../services/zone.service';
import { HouseType, HouseTypeService } from '../../../../../../../services/house-type.service';
import { MaritalStatus, MaritalStatusService } from '../../../../../../../services/marital-status.service';
import { EducationLevel, EducationLevelService } from '../../../../../../../services/education-level.service';
import { Company, CompanyService } from '../../../../../../../services/company.service';
import { ContractType, ContractTypeService } from '../../../../../../../services/contract-type.service';

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
  imports: [CommonModule, ReactiveFormsModule, AutoCompleteModule, InputMaskModule],
  templateUrl: './natural-person.component.html',
  styleUrls: ['./natural-person.component.css']
})
export class NaturalPersonComponent implements OnInit {
  natPersonForm: FormGroup;
  natPerson!: NaturalPerson;
  genders: Gender[] = [];
  zones: Zone[] = [];
  houseTypes: HouseType[] = [];
  maritalStatus: MaritalStatus[] = [];
  educationLevels: EducationLevel[] = [];
  companies: Company[] = [];
  contractTypes: ContractType[] = [];

  departments: Department[] = [];
  selectedDepartment: Department | undefined;
  filteredDepartments: Department[] = [];

  cities: City[] = [];
  selectedCity: City | undefined;
  filteredCities: City[] = [];

  constructor(
    private fb: FormBuilder,
    private naturalPersonService: NaturalpersonService,
    private loginService: LoginService,
    private genderService: GenderService,
    private departmentsService: DepartmentsService,
    private citiesService: CitiesService,
    private zoneService: ZoneService,
    private houseTypeService: HouseTypeService,
    private maritalStatusService: MaritalStatusService,
    private educationLevelService: EducationLevelService,
    private companyService: CompanyService,
    private contractTypeService: ContractTypeService
  ) {
    this.natPersonForm = this.fb.group({
      id: [''],
      idGenero: ['', Validators.required],
      fechaExpDoc: ['', Validators.required],
      departamentoExpDoc: ['', Validators.required],
      mpioExpDoc: ['', Validators.required],
      fechaNacimiento: ['', Validators.required],
      paisNacimiento: ['', Validators.required],
      departamentoNacimiento: ['', Validators.required],
      mpioNacimiento: ['', Validators.required],
      otroLugarNacimiento: [''],
      departamentoResidencia: ['', Validators.required],
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
      numeroHijos: [''],
      correoElectronico: ['', [Validators.required, Validators.email]],
      telefono: [''],
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

    this.zoneService.getAll().subscribe(types => {
      this.zones = types;
    });

    this.houseTypeService.getAll().subscribe(types => {
      this.houseTypes = types;
    });

    this.maritalStatusService.getAll().subscribe(types => {
      this.maritalStatus = types;
    });

    this.educationLevelService.getAll().subscribe(types => {
      this.educationLevels = types;
    });

    this.companyService.getAll().subscribe(types => {
      this.companies = types;
    });

    this.contractTypeService.getAll().subscribe(types => {
      this.contractTypes = types;
    });
  }

  loadDepartmentsAndCities() {
    const cityId = this.natPerson.mpioExpDoc;
    if (cityId) {
      this.citiesService.getCityById(cityId).subscribe(city => {
        this.selectedCity = city;
        if (this.selectedCity) {
          this.selectedDepartment = this.departments.find(dept => dept.id === this.selectedCity?.id_departamento);
          this.natPersonForm.patchValue({
            departamentoExpDoc: this.selectedDepartment ? this.selectedDepartment.nombre : '',
            mpioExpDoc: this.selectedCity ? this.selectedCity.nombre : ''
          });
        }
      });
    }
  }

  onSubmit(): void {
    console.log(this.natPerson.celular);
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