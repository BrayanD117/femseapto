import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { AutoCompleteModule } from 'primeng/autocomplete';
import { InputMaskModule } from 'primeng/inputmask';

// Services
import { LoginService } from '../../../../../../../services/login.service';
import { NaturalPerson, NaturalpersonService } from '../../../../../../../services/naturalperson.service';
import { Gender, GenderService } from '../../../../../../../services/gender.service';
import { Department, DepartmentsService } from '../../../../../../../services/departments.service';
import { City, CitiesService } from '../../../../../../../services/cities.service';
import { Zone, ZoneService } from '../../../../../../../services/zone.service';
import { HouseType, HouseTypeService } from '../../../../../../../services/house-type.service';
import { MaritalStatus, MaritalStatusService } from '../../../../../../../services/marital-status.service';
import { EducationLevel, EducationLevelService } from '../../../../../../../services/education-level.service';
import { Company, CompanyService } from '../../../../../../../services/company.service';
import { ContractType, ContractTypeService } from '../../../../../../../services/contract-type.service';
import { CountriesService, Country } from '../../../../../../../services/countries.service';

@Component({
  selector: 'app-natural-person',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, AutoCompleteModule, InputMaskModule],
  templateUrl: './natural-person.component.html',
  styleUrls: ['./natural-person.component.css']
})
export class NaturalPersonComponent implements OnInit {
  natPersonForm: FormGroup;
  userId: number | null = null;

  genders: Gender[] = [];
  zones: Zone[] = [];
  houseTypes: HouseType[] = [];
  maritalStatus: MaritalStatus[] = [];
  educationLevels: EducationLevel[] = [];
  companies: Company[] = [];
  contractTypes: ContractType[] = [];
  countries: Country[] = [];
  departments: Department[] = [];
  citiesExpDoc: City[] = [];
  citiesNac: City[] = [];
  citiesRes: City[] = [];


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
    private contractTypeService: ContractTypeService,
    private countriesService: CountriesService
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
    this.getUserIdFromToken();
    this.loadInitialData();

    this.genderService.getAll().subscribe(types => {
      this.genders = types;
    });

    this.departmentsService.getAll().subscribe((departments) => {
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

    this.countriesService.getAll().subscribe(types => {
      this.countries = types;
    });
  }

  getUserIdFromToken(): void {
    const token = this.loginService.getTokenClaims();
    if (token) {
      this.userId = token.userId;
    }
  }

  onDepartamentoChange(departmentType: string): void {
    const departamentoId = this.natPersonForm.get(departmentType)?.value;
    if (departamentoId) {
      this.citiesService.getByDepartmentId(departamentoId).subscribe(data => {
        switch (departmentType) {
          case 'departamentoExpDoc':
            this.citiesExpDoc = data;
            break;
          case 'departamentoNacimiento':
            this.citiesNac = data;
            break;
          case 'departamentoResidencia':
            this.citiesRes = data;
            break;
        }
      });
    }
  }

  loadInitialData(): void {
    if(this.userId) {
      this.naturalPersonService.getByUserId(this.userId).subscribe(natPerson => {
        this.natPersonForm.patchValue(natPerson);
  
        // Cargar municipios según los departamentos iniciales
        this.loadMunicipios('departamentoExpDoc', natPerson.departamentoExpDoc, 'mpioExpDoc');
        this.loadMunicipios('departamentoNacimiento', natPerson.departamentoNacimiento, 'mpioNacimiento');
        this.loadMunicipios('departamentoResidencia', natPerson.departamentoResidencia, 'mpioResidencia');
      });
    }    
  }

  loadMunicipios(departmentControlName: string, departamentoId: string, municipioControlName: string): void {
    this.citiesService.getByDepartmentId(departamentoId).subscribe(data => {
      switch (departmentControlName) {
        case 'departamentoExpDoc':
          this.citiesExpDoc = data;
          this.natPersonForm.get(municipioControlName)?.setValue(this.natPersonForm.get(municipioControlName)?.value);
          break;
        case 'departamentoNacimiento':
          this.citiesNac = data;
          this.natPersonForm.get(municipioControlName)?.setValue(this.natPersonForm.get(municipioControlName)?.value);
          break;
        case 'departamentoResidencia':
          this.citiesRes = data;
          this.natPersonForm.get(municipioControlName)?.setValue(this.natPersonForm.get(municipioControlName)?.value);
          break;
      }
    });
  }

  onSubmit(): void {
    if (this.natPersonForm.valid) {
      console.log(this.natPersonForm.value);
    } else {
      console.log('Formulario inválido');
    }
  }
}