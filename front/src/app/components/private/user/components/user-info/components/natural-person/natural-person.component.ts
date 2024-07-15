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
import { CountriesService, Country } from '../../../../../../../services/countries.service';

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
  countries: Country[] = [];

  departmentsExp: Department[] = [];
  selectedDepartmentExp: Department | undefined;
  filteredDepartmentsExp: Department[] = [];

  departmentsNac: Department[] = [];
  selectedDepartmentNac: Department | undefined;
  filteredDepartmentsNac: Department[] = [];

  departmentsRes: Department[] = [];
  selectedDepartmentRes: Department | undefined;
  filteredDepartmentsRes: Department[] = [];

  citiesExp: City[] = [];
  selectedCityExp: City | undefined;
  filteredCitiesExp: City[] = [];

  citiesNac: City[] = [];
  selectedCityNac: City | undefined;
  filteredCitiesNac: City[] = [];

  citiesRes: City[] = [];
  selectedCityRes: City | undefined;
  filteredCitiesRes: City[] = [];

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
      this.departmentsExp = departments;
      this.departmentsNac = departments;
      this.departmentsRes = departments;
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

  loadDepartmentsAndCities() {
    this.loadDepartmentAndCity('departamentoExpDoc', 'mpioExpDoc', this.natPerson.mpioExpDoc, this.departmentsExp, this.citiesExp, this.selectedCityExp, this.selectedDepartmentExp);
    this.loadDepartmentAndCity('departamentoNacimiento', 'mpioNacimiento', this.natPerson.mpioNacimiento, this.departmentsNac, this.citiesNac, this.selectedCityNac, this.selectedDepartmentNac);
    this.loadDepartmentAndCity('departamentoResidencia', 'mpioResidencia', this.natPerson.mpioResidencia, this.departmentsRes, this.citiesRes, this.selectedCityRes, this.selectedDepartmentRes);
  }

  /*loadDepartmentAndCity(departmentField: string, cityField: string, cityId: string, departments: Department[], selectedCity: any, selectedDepartment: any) {
    if (cityId) {
      this.citiesService.getCityById(cityId).subscribe(city => {
        selectedCity = city;
        if (selectedCity) {
          selectedDepartment = departments.find(dept => dept.id === selectedCity?.id_departamento);
          this.natPersonForm.patchValue({
            [departmentField]: selectedDepartment ? selectedDepartment.nombre : '',
            [cityField]: selectedCity ? selectedCity.nombre : ''
          });
        }
      });
    }
  }*/
  
  loadDepartmentAndCity(departmentField: string, cityField: string, cityId: string, departments: Department[], cities: City[], selectedCity: any, selectedDepartment: any) {
    if (cityId) {
      // Obtener la ciudad por su ID
      this.citiesService.getCityById(cityId).subscribe(city => {
        selectedCity = city;
        if (selectedCity) {
          // Encontrar el departamento correspondiente
          selectedDepartment = departments.find(dept => dept.id === selectedCity.id_departamento);
          if (selectedDepartment) {
            // Cargar todas las ciudades del departamento
            this.citiesService.getCitiesByDepartment(selectedDepartment.id).subscribe(data => {
              cities = data; // Guardar las ciudades en una variable (por ejemplo, this.cities)
              // Parchar los valores en el formulario
              this.natPersonForm.patchValue({
                [departmentField]: selectedDepartment.nombre,
                [cityField]: selectedCity.nombre
              });
            });
          }
        }
      });
    }
  }
  

  onSubmit(): void {
    if (this.natPersonForm.valid) {
      console.log(this.natPersonForm.value);
    } else {
      console.log('Formulario inválido');
    }
  }

  filterDepartmentExp(event: AutoCompleteCompleteEvent) {
    let filtered: Department[] = [];
    let query = event.query;

    for (let i = 0; i < this.departmentsExp.length; i++) {
      let dptm = this.departmentsExp[i];
      if (dptm.nombre.toLowerCase().indexOf(query.toLowerCase()) === 0) {
        filtered.push(dptm);
      }
    }

    this.filteredDepartmentsExp = filtered;
  }

  filterDepartmentNac(event: AutoCompleteCompleteEvent) {
    let filtered: Department[] = [];
    let query = event.query;

    for (let i = 0; i < this.departmentsNac.length; i++) {
      let dptm = this.departmentsNac[i];
      if (dptm.nombre.toLowerCase().indexOf(query.toLowerCase()) === 0) {
        filtered.push(dptm);
      }
    }

    this.filteredDepartmentsNac = filtered;
  }

  filterDepartmentRes(event: AutoCompleteCompleteEvent) {
    let filtered: Department[] = [];
    let query = event.query;

    for (let i = 0; i < this.departmentsRes.length; i++) {
      let dptm = this.departmentsRes[i];
      if (dptm.nombre.toLowerCase().indexOf(query.toLowerCase()) === 0) {
        filtered.push(dptm);
      }
    }

    this.filteredDepartmentsRes = filtered;
  }

  filterCityExp(event: AutoCompleteCompleteEvent) {
    let filtered: City[] = [];
    let query = event.query;

    for (let i = 0; i < this.citiesExp.length; i++) {
      let city = this.citiesExp[i];
      if (city.nombre.toLowerCase().indexOf(query.toLowerCase()) === 0) {
        filtered.push(city);
      }
    }

    this.filteredCitiesExp = filtered;
  }
  
  filterCityNac(event: AutoCompleteCompleteEvent) {
    let filtered: City[] = [];
    let query = event.query;

    for (let i = 0; i < this.citiesNac.length; i++) {
      let city = this.citiesNac[i];
      if (city.nombre.toLowerCase().indexOf(query.toLowerCase()) === 0) {
        filtered.push(city);
      }
    }

    this.filteredCitiesNac = filtered;
  }

  filterCityRes(event: AutoCompleteCompleteEvent) {
    let filtered: City[] = [];
    let query = event.query;

    for (let i = 0; i < this.citiesRes.length; i++) {
      let city = this.citiesRes[i];
      if (city.nombre.toLowerCase().indexOf(query.toLowerCase()) === 0) {
        filtered.push(city);
      }
    }

    this.filteredCitiesRes = filtered;
  }

  onDepartmentExpSelect(departmentField: string, cityField: string) {
    const selectedDepartmentId = this.natPersonForm.get(departmentField)?.value;
    this.selectedDepartmentExp = this.departmentsExp.find(dept => dept.id === selectedDepartmentId.id);

    if (this.selectedDepartmentExp) {
      this.citiesService.getCitiesByDepartment(this.selectedDepartmentExp.id).subscribe((cities) => {
        this.citiesExp = cities;
        this.filteredCitiesExp = [];
        this.natPersonForm.get(cityField)?.setValue('');
      });
    }
  }

  onDepartmentNacSelect(departmentField: string, cityField: string) {
    const selectedDepartmentId = this.natPersonForm.get(departmentField)?.value;
    this.selectedDepartmentNac = this.departmentsNac.find(dept => dept.id === selectedDepartmentId.id);

    if (this.selectedDepartmentNac) {
      this.citiesService.getCitiesByDepartment(this.selectedDepartmentNac.id).subscribe((cities) => {
        this.citiesNac = cities;
        this.filteredCitiesNac = [];
        this.natPersonForm.get(cityField)?.setValue('');
      });
    }
  }

  onDepartmentResSelect(departmentField: string, cityField: string) {
    const selectedDepartmentId = this.natPersonForm.get(departmentField)?.value;
    this.selectedDepartmentRes = this.departmentsRes.find(dept => dept.id === selectedDepartmentId.id);

    if (this.selectedDepartmentRes) {
      this.citiesService.getCitiesByDepartment(this.selectedDepartmentRes.id).subscribe((cities) => {
        this.citiesRes = cities;
        this.filteredCitiesRes = [];
        this.natPersonForm.get(cityField)?.setValue('');
      });
    }
  }

  onCountrySelect() {
    const selectedCountryId = this.natPersonForm.get('paisNacimiento')?.value;
    const selectedCountry = this.countries.find(country => country.id === selectedCountryId);

    if (selectedCountry?.nombre !== 'COLOMBIA') {
      this.natPersonForm.get('departamentoNacimiento')?.disable();
      this.natPersonForm.get('mpioNacimiento')?.disable();
      this.natPersonForm.get('otroLugarNacimiento')?.enable();
    } else {
      this.natPersonForm.get('departamentoNacimiento')?.enable();
      this.natPersonForm.get('mpioNacimiento')?.enable();
      this.natPersonForm.get('otroLugarNacimiento')?.disable();
    }
  }
}