import { Component, OnInit } from '@angular/core';
import {
  FormBuilder,
  FormGroup,
  Validators,
  ReactiveFormsModule,
} from '@angular/forms';
import { CommonModule } from '@angular/common';
import { AutoCompleteModule } from 'primeng/autocomplete';
import { InputMaskModule } from 'primeng/inputmask';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';

// Services
import { LoginService } from '../../../../../../../services/login.service';
import {
  NaturalPerson,
  NaturalpersonService,
} from '../../../../../../../services/naturalperson.service';
import {
  Gender,
  GenderService,
} from '../../../../../../../services/gender.service';
import {
  Department,
  DepartmentsService,
} from '../../../../../../../services/departments.service';
import {
  City,
  CitiesService,
} from '../../../../../../../services/cities.service';
import { Zone, ZoneService } from '../../../../../../../services/zone.service';
import {
  HouseType,
  HouseTypeService,
} from '../../../../../../../services/house-type.service';
import {
  MaritalStatus,
  MaritalStatusService,
} from '../../../../../../../services/marital-status.service';
import {
  EducationLevel,
  EducationLevelService,
} from '../../../../../../../services/education-level.service';
import {
  Company,
  CompanyService,
} from '../../../../../../../services/company.service';
import {
  ContractType,
  ContractTypeService,
} from '../../../../../../../services/contract-type.service';
import {
  CountriesService,
  Country,
} from '../../../../../../../services/countries.service';

@Component({
  selector: 'app-natural-person',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    AutoCompleteModule,
    InputMaskModule,
    ToastModule,
  ],
  providers: [MessageService],
  templateUrl: './natural-person.component.html',
  styleUrls: ['./natural-person.component.css'],
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

  isSubmitting: boolean = false;

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
    private countriesService: CountriesService,
    private messageService: MessageService
  ) {
    this.natPersonForm = this.fb.group({
      id: [''],
      idUsuario: ['', Validators.required],
      idGenero: ['', Validators.required],
      fechaExpDoc: ['', Validators.required],
      idDeptoExpDoc: ['', Validators.required],
      mpioExpDoc: ['', Validators.required],
      fechaNacimiento: ['', Validators.required],
      paisNacimiento: ['', Validators.required],
      idDeptoNacimiento: [''],
      mpioNacimiento: [''],
      otroLugarNacimiento: [''],
      idDeptoResidencia: ['', Validators.required],
      mpioResidencia: ['', Validators.required],
      idZonaResidencia: ['', Validators.required],
      idTipoVivienda: ['', Validators.required],
      estrato: ['', Validators.required],
      direccionResidencia: ['', Validators.required],
      antigVivienda: ['', Validators.required],
      duracionAntigVivienda: ['', Validators.required],
      periodoAntigVivienda: ['', Validators.required],
      idEstadoCivil: ['', Validators.required],
      cabezaFamilia: ['', Validators.required],
      personasACargo: [{ value: '', disabled: true }],
      tieneHijos: ['', Validators.required],
      numeroHijos: [{ value: '', disabled: true }],
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
      jefeInmediato: ['', Validators.required],
      antigEmpresa: ['', Validators.required],
      duracionAntigEmpresa: ['', Validators.required],
      periodoAntigEmpresa: ['', Validators.required],
      //mesesAntigEmpresa: ['', Validators.required],
      mesSaleVacaciones: ['', Validators.required],
      nombreEmergencia: ['', Validators.required],
      numeroCedulaEmergencia: ['', Validators.required],
      numeroCelularEmergencia: ['', Validators.required],
    });

    this.natPersonForm.get('tieneHijos')?.valueChanges.subscribe((value) => {
      this.toggleFieldsHasChildren(value);
    });

    this.natPersonForm.get('cabezaFamilia')?.valueChanges.subscribe((value) => {
      this.toggleFieldsPeople(value);
    });
  }

  ngOnInit(): void {
    this.getUserIdFromToken();
    this.loadInitialData();

    this.natPersonForm.patchValue({
      idUsuario: this.userId,
    });

    this.genderService.getAll().subscribe((types) => {
      this.genders = types;
    });

    this.departmentsService.getAll().subscribe((departments) => {
      this.departments = departments;
    });

    this.zoneService.getAll().subscribe((types) => {
      this.zones = types;
    });

    this.houseTypeService.getAll().subscribe((types) => {
      this.houseTypes = types;
    });

    this.maritalStatusService.getAll().subscribe((types) => {
      this.maritalStatus = types;
    });

    this.educationLevelService.getAll().subscribe((types) => {
      this.educationLevels = types;
    });

    this.companyService.getAll().subscribe((types) => {
      this.companies = types;
    });

    this.contractTypeService.getAll().subscribe((types) => {
      this.contractTypes = types;
    });

    this.countriesService.getAll().subscribe((types) => {
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
      this.citiesService.getByDepartmentId(departamentoId).subscribe((data) => {
        switch (departmentType) {
          case 'idDeptoExpDoc':
            this.citiesExpDoc = data;
            this.natPersonForm
              .get('mpioExpDoc')
              ?.setValue(this.natPersonForm.get('mpioExpDoc')?.value);
            break;
          case 'idDeptoNacimiento':
            this.citiesNac = data;
            this.natPersonForm
              .get('mpioNacimiento')
              ?.setValue(this.natPersonForm.get('mpioNacimiento')?.value);
            break;
          case 'idDeptoResidencia':
            this.citiesRes = data;
            this.natPersonForm
              .get('mpioResidencia')
              ?.setValue(this.natPersonForm.get('mpioResidencia')?.value);
            break;
        }
      });
    }
  }

  onSelectChange(event: Event): void {
    const selectedValue = (event.target as HTMLSelectElement).value;
  }

  loadInitialData(): void {
    if (this.userId) {
      this.naturalPersonService
        .getByUserId(this.userId)
        .subscribe((natPerson) => {
          let durationHome: string = '';
          let periodHome: string = '';
          let durationCompany: string = '';
          let periodCompany: string = '';
  
          if (natPerson.antigVivienda) {
            const [extractedDuration, extractedPeriod] = natPerson.antigVivienda.split(' ');
            durationHome = extractedDuration;
            periodHome = extractedPeriod;
          }

          if (natPerson.antigEmpresa) {
            const [extractedDuration, extractedPeriod] = natPerson.antigEmpresa.split(' ');
            durationCompany = extractedDuration;
            periodCompany = extractedPeriod;
          }

          this.natPersonForm.patchValue({
            duracionAntigVivienda: durationHome,
            periodoAntigVivienda: periodHome,
            duracionAntigEmpresa: durationCompany,
            periodoAntigEmpresa: periodCompany,
            ...natPerson
          });

          this.toggleFieldsHasChildren(
            this.natPersonForm.get('tieneHijos')?.value
          );
          this.toggleFieldsPeople(
            this.natPersonForm.get('cabezaFamilia')?.value
          );

          this.onDepartamentoChange('idDeptoExpDoc');
          this.onDepartamentoChange('idDeptoNacimiento');
          this.onDepartamentoChange('idDeptoResidencia');
        });
    }
  }

  onPaisNacimientoChange(): void {
    const paisSeleccionado = this.natPersonForm.get('paisNacimiento')?.value;
    if (paisSeleccionado === '170') {
      this.natPersonForm.get('idDeptoNacimiento')?.enable();
      this.natPersonForm.get('mpioNacimiento')?.enable();
      this.natPersonForm.get('otroLugarNacimiento')?.disable();
      this.natPersonForm.get('otroLugarNacimiento')?.setValue('');
    } else {
      this.natPersonForm.get('idDeptoNacimiento')?.disable();
      this.natPersonForm.get('mpioNacimiento')?.disable();
      this.natPersonForm.get('idDeptoNacimiento')?.setValue('');
      this.natPersonForm.get('mpioNacimiento')?.setValue('');
      this.natPersonForm.get('otroLugarNacimiento')?.enable();
    }
  }

  toggleFieldsHasChildren(value: string): void {
    const numChildrenControl = this.natPersonForm.get('numeroHijos');

    if (value === 'NO' || value === '') {
      numChildrenControl?.setValue(0);
      numChildrenControl?.disable();
    } else {
      numChildrenControl?.enable();
    }
  }

  toggleFieldsPeople(value: string): void {
    const numPeopleControl = this.natPersonForm.get('personasACargo');

    if (value === 'NO' || value === '') {
      numPeopleControl?.setValue(0);
      numPeopleControl?.disable();
    } else {
      numPeopleControl?.enable();
    }
  }

  onSubmit(): void {

    //console.log("ANTES", this.natPersonForm.value);
    if (this.isSubmitting) {
      return;
    }

    this.isSubmitting = true;


    if (this.natPersonForm.valid) {
      const durationHome = this.natPersonForm.get('duracionAntigVivienda')?.value;
      const periodHome = this.natPersonForm.get('periodoAntigVivienda')?.value;
      const durationCompany = this.natPersonForm.get('duracionAntigEmpresa')?.value;
      const periodCompany = this.natPersonForm.get('periodoAntigEmpresa')?.value;

      // Combina ambos valores en una cadena para enviar al backend
      const antiguedadVivienda = `${durationHome} ${periodHome}`;
      const antiguedadEmpresa = `${durationCompany} ${periodCompany}`;
      this.natPersonForm.patchValue({
        antigVivienda: antiguedadVivienda,
        antigEmpresa: antiguedadEmpresa
      });

      if (this.natPersonForm.get('tieneHijos')?.value === 'NO') {
        this.natPersonForm.get('numeroHijos')?.enable();
        this.natPersonForm.get('numeroHijos')?.setValue(0);
      }

      if (this.natPersonForm.get('cabezaFamilia')?.value === 'NO') {
        this.natPersonForm.get('personasACargo')?.enable();
        this.natPersonForm.get('personasACargo')?.setValue(0);
      }
      const data: NaturalPerson = this.natPersonForm.value;

      //console.log("ENTRA", this.natPersonForm.value);

      if (data.id) {
        this.naturalPersonService.update(data).subscribe({
          next: () => {
            this.messageService.add({
              severity: 'success',
              summary: 'Éxito',
              detail: 'Información actualizada correctamente',
            });
            setTimeout(() => {
              this.isSubmitting = false;
            }, 500);
          },
          error: (err) => {
            console.error('Error al actualizar la información', err);
            this.messageService.add({
              severity: 'error',
              summary: 'Error',
              detail:
                'No se pudo actualizar la información. Vuelve a intentarlo.',
            });
            setTimeout(() => {
              this.isSubmitting = false;
            }, 500);
          },
        });
      } else {
        this.naturalPersonService.create(data).subscribe({
          next: (response) => {
            //console.log(response);
            this.natPersonForm.patchValue({ id: response.id });
            this.messageService.add({
              severity: 'success',
              summary: 'Éxito',
              detail: 'Información creada correctamente',
            });
            setTimeout(() => {
              this.isSubmitting = false;
            }, 500);
          },
          error: (err) => {
            console.error('Error al actualizar la información', err);
            this.messageService.add({
              severity: 'error',
              summary: 'Error',
              detail: 'No se pudo crear la información. Vuelve a intentarlo.',
            });
            setTimeout(() => {
              this.isSubmitting = false;
            }, 500);
          },
        });
      }
    } else {
      this.messageService.add({
        severity: 'error',
        summary: 'Error',
        detail: 'Algún dato te falta por registrar.',
      });
      setTimeout(() => {
        this.isSubmitting = false;
      }, 500);
    }

    if (this.natPersonForm.get('tieneHijos')?.value === 'NO') {
      this.natPersonForm.get('numeroHijos')?.disable();
    }

    if (this.natPersonForm.get('cabezaFamilia')?.value === 'NO') {
      this.natPersonForm.get('personasACargo')?.disable();
    }
  }
}