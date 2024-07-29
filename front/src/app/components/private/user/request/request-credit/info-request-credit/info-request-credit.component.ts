import { CommonModule } from '@angular/common';
import { Component, EventEmitter, OnInit, Output } from '@angular/core';
import {
  FormBuilder,
  FormGroup,
  ReactiveFormsModule,
  Validators,
} from '@angular/forms';
import { MessageService } from 'primeng/api';
import { AutoCompleteModule } from 'primeng/autocomplete';
import { InputMaskModule } from 'primeng/inputmask';
import { ToastModule } from 'primeng/toast';
import {
  Gender,
  GenderService,
} from '../../../../../../services/gender.service';
import { Zone, ZoneService } from '../../../../../../services/zone.service';
import {
  HouseType,
  HouseTypeService,
} from '../../../../../../services/house-type.service';
import {
  MaritalStatus,
  MaritalStatusService,
} from '../../../../../../services/marital-status.service';
import {
  EducationLevel,
  EducationLevelService,
} from '../../../../../../services/education-level.service';
import {
  Company,
  CompanyService,
} from '../../../../../../services/company.service';
import {
  ContractType,
  ContractTypeService,
} from '../../../../../../services/contract-type.service';
import {
  CountriesService,
  Country,
} from '../../../../../../services/countries.service';
import {
  Department,
  DepartmentsService,
} from '../../../../../../services/departments.service';
import { CitiesService, City } from '../../../../../../services/cities.service';
import {
  NaturalPerson,
  NaturalpersonService,
} from '../../../../../../services/naturalperson.service';
import { LoginService } from '../../../../../../services/login.service';
import { RequestCreditComponent } from '../request-credit.component';
import { FamilyInformationComponent } from '../../../components/user-info/components/family-information/family-information.component';
import { RecommendationComponent } from '../../../components/user-info/components/recommendation/recommendation.component';
import { FinancialInfoComponent } from '../../../components/user-info/components/financial-info/financial-info.component';

@Component({
  selector: 'app-info-request-credit',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    AutoCompleteModule,
    InputMaskModule,
    ToastModule,
    RequestCreditComponent,
    FamilyInformationComponent,
    RecommendationComponent,
    FinancialInfoComponent
  ],
  providers: [MessageService],
  templateUrl: './info-request-credit.component.html',
  styleUrl: './info-request-credit.component.css',
})
export class InfoRequestCreditComponent implements OnInit {
  infoForm: FormGroup;
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

  currentSection: number = 0;
  totalSections: number = 4;

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
    this.infoForm = this.fb.group({
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
      //idZonaResidencia: ['', Validators.required],
      idTipoVivienda: ['', Validators.required],
      estrato: ['', Validators.required],
      direccionResidencia: ['', Validators.required],
      aniosAntigVivienda: ['', Validators.required],
      idEstadoCivil: ['', Validators.required],
      cabezaFamilia: ['', Validators.required],
      personasACargo: [{ value: '', disabled: true }],
      //tieneHijos: ['', Validators.required],
      //numeroHijos: [{ value: '', disabled: true }],
      correoElectronico: ['', [Validators.required, Validators.email]],
      telefono: ['', Validators.required],
      celular: ['', Validators.required],
      telefonoOficina: ['', Validators.required],
      idNivelEducativo: ['', Validators.required],
      //profesion: ['', Validators.required],
      //ocupacionOficio: ['', Validators.required],
      //idEmpresaLabor: ['', Validators.required],
      idTipoContrato: ['', Validators.required],
      dependenciaEmpresa: ['', Validators.required],
      //cargoOcupa: ['', Validators.required],
      jefeInmediato: ['', Validators.required],
      aniosAntigEmpresa: ['', Validators.required],
      //mesesAntigEmpresa: ['', Validators.required],
      mesSaleVacaciones: ['', Validators.required],
      //nombreEmergencia: ['', Validators.required],
      //numeroCedulaEmergencia: ['', Validators.required],
      //numeroCelularEmergencia: ['', Validators.required],
    });

    /*this.infoForm.get('tieneHijos')?.valueChanges.subscribe((value) => {
      this.toggleFieldsHasChildren(value);
    });*/

    this.infoForm.get('cabezaFamilia')?.valueChanges.subscribe((value) => {
      this.toggleFieldsPeople(value);
    });
  }

  ngOnInit(): void {
    this.getUserIdFromToken();
    this.loadInitialData();

    this.infoForm.patchValue({
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

  nextSection() {
    if (this.currentSection < this.totalSections - 1) {
      this.currentSection++;
    }
  }

  prevSection() {
    if (this.currentSection > 0) {
      this.currentSection--;
    }
  }

  isSectionVisible(sectionIndex: number): boolean {
    return this.currentSection === sectionIndex;
  }  

  onDepartamentoChange(departmentType: string): void {
    const departamentoId = this.infoForm.get(departmentType)?.value;
    if (departamentoId) {
      this.citiesService.getByDepartmentId(departamentoId).subscribe((data) => {
        switch (departmentType) {
          case 'idDeptoExpDoc':
            this.citiesExpDoc = data;
            this.infoForm
              .get('mpioExpDoc')
              ?.setValue(this.infoForm.get('mpioExpDoc')?.value);
            break;
          case 'idDeptoNacimiento':
            this.citiesNac = data;
            this.infoForm
              .get('mpioNacimiento')
              ?.setValue(this.infoForm.get('mpioNacimiento')?.value);
            break;
          case 'idDeptoResidencia':
            this.citiesRes = data;
            this.infoForm
              .get('mpioResidencia')
              ?.setValue(this.infoForm.get('mpioResidencia')?.value);
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
          this.infoForm.patchValue(natPerson);
          /*this.toggleFieldsHasChildren(
            this.infoForm.get('tieneHijos')?.value
          );*/
          this.toggleFieldsPeople(this.infoForm.get('cabezaFamilia')?.value);

          this.onDepartamentoChange('idDeptoExpDoc');
          this.onDepartamentoChange('idDeptoNacimiento');
          this.onDepartamentoChange('idDeptoResidencia');
        });
    }
  }

  onPaisNacimientoChange(): void {
    const paisSeleccionado = this.infoForm.get('paisNacimiento')?.value;
    if (paisSeleccionado === '170') {
      this.infoForm.get('idDeptoNacimiento')?.enable();
      this.infoForm.get('mpioNacimiento')?.enable();
      this.infoForm.get('otroLugarNacimiento')?.disable();
      this.infoForm.get('otroLugarNacimiento')?.setValue('');
    } else {
      this.infoForm.get('idDeptoNacimiento')?.disable();
      this.infoForm.get('mpioNacimiento')?.disable();
      this.infoForm.get('idDeptoNacimiento')?.setValue('');
      this.infoForm.get('mpioNacimiento')?.setValue('');
      this.infoForm.get('otroLugarNacimiento')?.enable();
    }
  }

  /*toggleFieldsHasChildren(value: string): void {
    const numChildrenControl = this.infoForm.get('numeroHijos');

    if (value === 'NO' || value === '') {
      numChildrenControl?.setValue(0);
      numChildrenControl?.disable();
    } else {
      numChildrenControl?.enable();
    }
  }*/

  toggleFieldsPeople(value: string): void {
    const numPeopleControl = this.infoForm.get('personasACargo');

    if (value === 'NO' || value === '') {
      numPeopleControl?.setValue(0);
      numPeopleControl?.disable();
    } else {
      numPeopleControl?.enable();
    }
  }

  onSubmit(): void {
    //console.log("ANTES", this.infoForm.value);

    if (this.infoForm.valid) {
      /*if (this.infoForm.get('tieneHijos')?.value === 'NO') {
        this.infoForm.get('numeroHijos')?.enable();
        this.infoForm.get('numeroHijos')?.setValue(0);
      }*/

      if (this.infoForm.get('cabezaFamilia')?.value === 'NO') {
        this.infoForm.get('personasACargo')?.enable();
        this.infoForm.get('personasACargo')?.setValue(0);
      }
      const data: NaturalPerson = this.infoForm.value;

      //console.log("ENTRA", this.infoForm.value);

      if (data.id) {
        this.naturalPersonService.update(data).subscribe({
          next: () => {
            this.messageService.add({
              severity: 'success',
              summary: 'Éxito',
              detail: 'Información actualizada correctamente',
            });
          },
          error: (err) => {
            console.error('Error al actualizar la información', err);
            this.messageService.add({
              severity: 'error',
              summary: 'Error',
              detail:
                'No se pudo actualizar la información. Vuelve a intentarlo.',
            });
          },
        });
      } else {
        this.naturalPersonService.create(data).subscribe({
          next: () => {
            this.messageService.add({
              severity: 'success',
              summary: 'Éxito',
              detail: 'Información creada correctamente',
            });
          },
          error: (err) => {
            console.error('Error al actualizar la información', err);
            this.messageService.add({
              severity: 'error',
              summary: 'Error',
              detail: 'No se pudo crear la información. Vuelve a intentarlo.',
            });
          },
        });
      }     
    } else {
      this.messageService.add({
        severity: 'error',
        summary: 'Error',
        detail: 'Algún dato te falta por registrar.',
      });
    }

    /*if (this.infoForm.get('tieneHijos')?.value === 'NO') {
      this.infoForm.get('numeroHijos')?.disable();
    }*/

    if (this.infoForm.get('cabezaFamilia')?.value === 'NO') {
      this.infoForm.get('personasACargo')?.disable();
    }
  }
}