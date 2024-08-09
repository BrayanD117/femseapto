import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';
import { NaturalPerson, NaturalpersonService } from '../../../../../../services/naturalperson.service';
import { Department, DepartmentsService } from '../../../../../../services/departments.service';
import { CitiesService, City } from '../../../../../../services/cities.service';
import { Company, CompanyService } from '../../../../../../services/company.service';
import { LoginService } from '../../../../../../services/login.service';

@Component({
  selector: 'app-info-request-saving',
  standalone: true,
  imports: [CommonModule,
    ReactiveFormsModule,
    ToastModule
  ],
  providers: [MessageService],
  templateUrl: './info-request-saving.component.html',
  styleUrl: './info-request-saving.component.css'
})
export class InfoRequestSavingComponent implements OnInit {
  infoForm: FormGroup;
  userId: number | null = null;

  departments: Department[] = [];
  citiesExpDoc: City[] = [];
  citiesRes: City[] = [];
  companies: Company[] = [];

  isSubmitting: boolean = false;

  constructor(
    private fb: FormBuilder,
    private naturalPersonService: NaturalpersonService,
    private loginService: LoginService,
    private departmentsService: DepartmentsService,
    private citiesService: CitiesService,
    private companyService: CompanyService,
    private messageService: MessageService
  ) {
    this.infoForm = this.fb.group({
      id: [null],
      idUsuario: ['', Validators.required],
      idGenero: [null],
      fechaExpDoc: [null],
      idDeptoExpDoc: ['', Validators.required],
      mpioExpDoc: ['', Validators.required],
      fechaNacimiento: [null],
      paisNacimiento: [null],
      idDeptoNacimiento: [null],
      mpioNacimiento: [null],
      otroLugarNacimiento: [null],
      idDeptoResidencia: ['', Validators.required],
      mpioResidencia: ['', Validators.required],
      idZonaResidencia: [null],
      idTipoVivienda: [null],
      estrato: [null],
      direccionResidencia: [null],
      aniosAntigVivienda: [null],
      idEstadoCivil: [null],
      cabezaFamilia: [null],
      personasACargo: [null],
      tieneHijos: [null],
      numeroHijos: [null],
      correoElectronico: [null],
      telefono: ['', Validators.required],
      celular: ['', Validators.required],
      telefonoOficina: [null],
      idNivelEducativo: [null],
      profesion: [null],
      ocupacionOficio: [null],
      idEmpresaLabor: ['', Validators.required],
      idTipoContrato: [null],
      dependenciaEmpresa: [null],
      cargoOcupa: [null],
      jefeInmediato: [null],
      aniosAntigEmpresa: [null],
      //mesesAntigEmpresa: ['', Validators.required],
      mesSaleVacaciones: [null],
      nombreEmergencia: [null],
      numeroCedulaEmergencia: [null],
      numeroCelularEmergencia: [null],
    });
  }

  ngOnInit(): void {
    this.getUserIdFromToken();
    this.loadInitialData();
    this.loadDepartments();
    this.loadCompanies();
  }

  getUserIdFromToken(): void {
    const token = this.loginService.getTokenClaims();
    if (token) {
      this.userId = token.userId;
      this.infoForm.patchValue({
        idUsuario: this.userId,
      });
    }
  }

  loadDepartments(): void {
    this.departmentsService.getAll().subscribe((departments) => {
      this.departments = departments;
    });
  }

  loadCompanies(): void {
    this.companyService.getAll().subscribe((types) => {
      this.companies = types;
    });
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

  loadInitialData(): void {
    if (this.userId) {
      this.naturalPersonService
        .getByUserId(this.userId)
        .subscribe((natPerson) => {
          this.infoForm.patchValue(natPerson);

          this.onDepartamentoChange('idDeptoExpDoc');
          this.onDepartamentoChange('idDeptoResidencia');
        });
    }
  }

  onSubmit(): void {
    if (this.isSubmitting) {
      return;
    }

    this.isSubmitting = true;

    console.log("ANTES", this.infoForm.value);

    if (this.infoForm.valid) {
      const data: NaturalPerson = this.infoForm.value;

      console.log("ENTRA", this.infoForm.value);

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
            console.log(response);
            this.infoForm.patchValue({ id: response.id });
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
            console.error('Error al crear la información', err);
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
  }
}
