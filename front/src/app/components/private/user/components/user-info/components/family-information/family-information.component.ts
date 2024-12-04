import { Component, Input, OnInit } from '@angular/core';
import {
  ReactiveFormsModule,
  FormBuilder,
  FormGroup,
  Validators,
} from '@angular/forms';
import { CommonModule } from '@angular/common';
import { TableModule } from 'primeng/table';
import { forkJoin, Observable, of } from 'rxjs';
import { MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';
import {
  Family,
  FamilyService,
} from '../../../../../../../services/family.service';
import { LoginService } from '../../../../../../../services/login.service';
import {
  DocumentType,
  DocumentTypeService,
} from '../../../../../../../services/document-type.service';
import {
  Relationship,
  RelationshipService,
} from '../../../../../../../services/relationship.service';
import {
  Gender,
  GenderService,
} from '../../../../../../../services/gender.service';
import {
  EducationLevel,
  EducationLevelService,
} from '../../../../../../../services/education-level.service';
import {
  Department,
  DepartmentsService,
} from '../../../../../../../services/departments.service';
import {
  City,
  CitiesService,
} from '../../../../../../../services/cities.service';

@Component({
  selector: 'app-family-information',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, TableModule, ToastModule],
  providers: [MessageService],
  templateUrl: './family-information.component.html',
  styleUrl: './family-information.component.css',
})
export class FamilyInformationComponent implements OnInit {
  @Input() actualizarPerfilFecha: boolean = false;

  family: Family[] = [];
  familiarForm: FormGroup;
  editMode = false;
  selectedFamiliar: Family | null = null;
  userId: number | null = null;

  departments: Department[] = [];
  citiesExpDoc: City[] = [];
  documentTypes: DocumentType[] = [];
  relationships: Relationship[] = [];
  genders: Gender[] = [];
  educationLevels: EducationLevel[] = [];

  constructor(
    private fb: FormBuilder,
    private familyService: FamilyService,
    private loginService: LoginService,
    private docTypeService: DocumentTypeService,
    private relationshipService: RelationshipService,
    private genderService: GenderService,
    private educationLevelService: EducationLevelService,
    private messageService: MessageService,
    private departmentsService: DepartmentsService,
    private citiesService: CitiesService
  ) {
    this.familiarForm = this.fb.group({
      id: [''],
      idUsuario: [{ value: '', disabled: true }, Validators.required],
      nombreCompleto: ['', Validators.required],
      idTipoDocumento: [0, Validators.required],
      numeroDocumento: ['', Validators.required],
      idDptoExpDoc: ['', Validators.required],
      idMpioExpDoc: ['', Validators.required],
      idParentesco: [0, Validators.required],
      idGenero: ['', Validators.required],
      fechaNacimiento: ['', Validators.required],
      idNivelEducativo: ['', Validators.required],
      trabaja: ['', Validators.required],
      celular: ['', Validators.required],
      actualizarPerfilFecha: [false]
    });
  }

  ngOnInit(): void {
    this.obtenerIdUsuarioDesdeToken();
    this.getAllDepartments();
    this.getAllDocTypes();
    this.getAllRelationships();
    this.getAllGenders();
    this.getAllEducationLevels();
    this.cargarFamilia();

    const idMpioExpDoc = this.familiarForm.get('idMpioExpDoc')?.value;
    if (idMpioExpDoc) {
      this.onDepartmentChange();
    }

    this.familiarForm.patchValue({
      actualizarPerfilFecha: this.actualizarPerfilFecha
    });
  }

  getAllDepartments(): void {
    this.departmentsService.getAll().subscribe((types) => {
      this.departments = types;
    });
  }

  getAllDocTypes(): void {
    this.docTypeService.getAll().subscribe((types) => {
      /*this.documentTypes = types.map((type: DocumentType) => ({
        ...type,
        id: +type.id
      }))*/
      this.documentTypes = types;
    });
  }

  getAllRelationships(): void {
    this.relationshipService.getAll().subscribe((types: Relationship[]) => {
      this.relationships = types.map((type: Relationship) => ({
        ...type,
        id: +type.id,
      }));
    });
  }

  getAllGenders(): void {
    this.genderService.getAll().subscribe((types) => {
      this.genders = types;
    });
  }

  getAllEducationLevels(): void {
    this.educationLevelService.getAll().subscribe((types) => {
      this.educationLevels = types;
    });
  }

  obtenerIdUsuarioDesdeToken(): void {
    const token = this.loginService.getTokenClaims();
    if (token) {
      this.userId = token.userId;
    }
  }

  getDocTypeName(id: number): string {
    const tipo = this.documentTypes.find((type) => type.id === id);
    return tipo ? tipo.nombre : '';
  }

  getRelationshipName(id: number): string {
    const parentesco = this.relationships.find((type) => type.id === id);
    return parentesco ? parentesco.nombre : '';
  }

  getGenderName(id: number): string {
    const genero = this.genders.find((type) => type.id === id);
    return genero ? genero.nombre : '';
  }

  getEducationLevelName(id: number): string {
    const nivel = this.educationLevels.find((type) => type.id === id);
    return nivel ? nivel.nombre : '';
  }

  cargarFamilia(): void {
    if (this.userId) {
      this.familyService
        .getByUserId(this.userId)
        .subscribe((data: Family[]) => {
          const requests: Observable<any>[] = data.map((familiar: Family) => {
            const relationshipName = this.getRelationshipName(
              familiar.idParentesco
            );
            const docTypeName = this.getDocTypeName(familiar.idTipoDocumento);
            return of({
              ...familiar,
              parentesco: relationshipName,
              tipoDoc: docTypeName,
            });
          });

          forkJoin(requests).subscribe((results) => {
            this.family = results as Family[];
          });
        });
    }
  }

  guardarFamiliar(): void {

    if (this.familiarForm.invalid || !this.userId) {
      return;
    }

    const familiar: Family = this.familiarForm.getRawValue();
    familiar.idUsuario = this.userId;

    if (this.editMode) {
      if (this.selectedFamiliar && this.selectedFamiliar.id) {
        this.familyService.update(familiar).subscribe({
          next: () => {
            this.cargarFamilia();
            this.cancelarEdicion();
            this.messageService.add({
              severity: 'success',
              summary: 'Éxito',
              detail: 'Info. familiar actualizada exitosamente.',
            });
          },
          error: (err) => {
            console.error('Error actualizando la info. familiar', err);
            this.messageService.add({
              severity: 'error',
              summary: 'Error',
              detail:
                'Ocurrió un error al actualizar la info. familiar. Vuelve a intentarlo.',
            });
          },
        });
      }
    } else {
      this.familyService.create(familiar).subscribe({
        next: () => {
          this.cargarFamilia();
          this.familiarForm.reset();
          this.messageService.add({
            severity: 'success',
            summary: 'Éxito',
            detail: 'Info. familiar creada exitosamente.',
          });
        },
        error: (err) => {
          console.error('Error creando la info. familiar', err);
          this.messageService.add({
            severity: 'error',
            summary: 'Error',
            detail:
              'Ocurrió un error al crear la info. familiar. Vuelve a intentarlo.',
          });
        },
      });
    }
  }

  onDepartmentChange(): void {
    const departmentId = this.familiarForm.get('idDptoExpDoc')?.value;
    if (departmentId) {
      this.citiesService.getByDepartmentId(departmentId).subscribe((data) => {
        this.citiesExpDoc = data;
      });
    }
  }

  editarFamiliar(familiar: Family): void {
    this.editMode = true;
    this.selectedFamiliar = familiar;

    // Limpiar las ciudades del select para evitar problemas de re-renderizado
    this.citiesExpDoc = [];

    this.citiesService.getById(familiar.idMpioExpDoc).subscribe((city) => {
      // Establecer el valor del departamento primero
      this.familiarForm.get('idDptoExpDoc')?.setValue(city.idDepartamento);

      // Cargar las ciudades correspondientes al departamento
      this.citiesService
        .getByDepartmentId(city.idDepartamento)
        .subscribe((data) => {
          this.citiesExpDoc = data;

          // Ahora establecer el valor del municipio
          this.familiarForm
            .get('idMpioExpDoc')
            ?.setValue(familiar.idMpioExpDoc);
        });
    });
    this.familiarForm.patchValue(familiar);
  }

  eliminarFamiliar(id: number): void {
    this.familyService.delete(id).subscribe({
      next: () => {
        this.messageService.add({
          severity: 'success',
          summary: 'Éxito',
          detail: 'Info. familiar eliminada exitosamente.',
        });
        this.cargarFamilia();
      },
      error: (err) => {
        console.error('Error eliminando la info. familiar', err);
        this.messageService.add({
          severity: 'error',
          summary: 'Error',
          detail:
            'Ocurrió un error al eliminar la info. familiar. Vuelve a intentarlo.',
        });
      },
    });
    this.familiarForm.reset();
  }

  cancelarEdicion(): void {
    this.editMode = false;
    this.selectedFamiliar = null;
    this.familiarForm.reset();
  }

  formReset() {
    this.familiarForm.reset();
  }
}