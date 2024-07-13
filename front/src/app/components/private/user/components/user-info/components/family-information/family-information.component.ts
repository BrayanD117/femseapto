import { Component, OnInit } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { TableModule } from 'primeng/table';
import { forkJoin, Observable, of } from 'rxjs';
import { MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';


import { Family, FamilyService } from '../../../../../../../services/family.service';
import { LoginService } from '../../../../../../../services/login.service';
import { DocumentType, DocumentTypeService } from '../../../../../../../services/document-type.service';
import { Relationship, RelationshipService } from '../../../../../../../services/relationship.service';
import { Gender, GenderService } from '../../../../../../../services/gender.service';
import { EducationLevel, EducationLevelService } from '../../../../../../../services/education-level.service';

@Component({
  selector: 'app-family-information',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, TableModule, ToastModule],
  providers: [MessageService],
  templateUrl: './family-information.component.html',
  styleUrl: './family-information.component.css'
})
export class FamilyInformationComponent implements OnInit{
  family: Family[] = [];
  familiarForm: FormGroup;
  editMode = false;
  selectedFamiliar: Family | null = null;
  userId: number | null = null;

  documentTypes: DocumentType[] = [];
  relationships: Relationship[] = [];
  genders: Gender[] = [];
  educationLevels: EducationLevel[] = [];

  constructor(private fb: FormBuilder, private familyService: FamilyService,
    private loginService: LoginService, private docTypeService: DocumentTypeService,
    private relationshipService: RelationshipService, private genderService: GenderService,
    private educationLevelService: EducationLevelService, private messageService: MessageService,
  ) {
    this.familiarForm = this.fb.group({
      id: [''],
      idUsuario: [{ value: '', disabled: true }, Validators.required],
      nombreCompleto: ['', Validators.required],
      idTipoDocumento: ['', Validators.required],
      numeroDocumento: ['', Validators.required],
      idMpioExpDoc: ['', Validators.required],
      idParentesco: ['', Validators.required],
      idGenero: ['', Validators.required],
      fechaNacimiento: ['', Validators.required],
      idNivelEducativo: ['', Validators.required],
      trabaja: ['', Validators.required],
      celular: ['', Validators.required]
    });
  }

  ngOnInit(): void {
    this.obtenerIdUsuarioDesdeToken();
    this.getAllDocTypes();
    this.getAllRelationships();
    this.getAllGenders();
    this.getAllEducationLevels();
    this.cargarFamilia();
  }

  getAllDocTypes(): void {
    this.docTypeService.getAll().subscribe(types => {
      this.documentTypes = types;
    });
  }

  getAllRelationships(): void {
    this.relationshipService.getAll().subscribe(types => {
      this.relationships = types;
    });
  }

  getAllGenders(): void {
    this.genderService.getAll().subscribe(types => {
      this.genders = types;
    });
  }

  getAllEducationLevels(): void {
    this.educationLevelService.getAll().subscribe(types => {
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
    console.log(id);
    console.log(this.documentTypes);
    const tipo = this.documentTypes.find(type => type.id === id);
    console.log(tipo?.nombre);
    return tipo ? tipo.nombre : '';
  }
  
  getRelationshipName(id: number): string {
    const parentesco = this.relationships.find(type => type.id === id);
    return parentesco ? parentesco.nombre : '';
  }
  
  getGenderName(id: number): string {
    const genero = this.genders.find(type => type.id === id);
    return genero ? genero.nombre : '';
  }
  
  getEducationLevelName(id: number): string {
    const nivel = this.educationLevels.find(type => type.id === id);
    return nivel ? nivel.nombre : '';
  }
  
  cargarFamilia(): void {
    if (this.userId) {
      this.familyService.getByUserId(this.userId).subscribe((data: Family[]) => {
        const requests: Observable<any>[] = data.map((familiar: Family) => {
          const parentescoNombre = this.getRelationshipName(familiar.idParentesco);
          return of({
            ...familiar,
            parentescoNombre: parentescoNombre,
            // Agregar más mapeos aquí según sea necesario
          });
        });

        forkJoin(requests).subscribe(results => {
          this.family = results as Family[];
        });
      });
    }
  }

  guardarFamiliar(): void {
    console.log(this.familiarForm.value);

    if (this.familiarForm.invalid  || !this.userId) {
      return;
    }

    const familiar: Family = this.familiarForm.getRawValue();
    familiar.idUsuario = this.userId;
    console.log("Info. con Id usuario", familiar);

    if (this.editMode) {
      if (this.selectedFamiliar && this.selectedFamiliar.id) {
        this.familyService.update(familiar).subscribe({
          next: () => {
            this.cargarFamilia();
            this.cancelarEdicion();
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Info. familiar actualizada exitosamente.' });
          },
          error: (err) => {
            console.error('Error actualizando la info. familiar', err);
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Ocurrió un error al actualizar la info. familiar. Vuelve a intentarlo.' });
          }
        });
      }
    } else {
      this.familyService.create(familiar).subscribe({
        next: () => {
          this.cargarFamilia();
          this.familiarForm.reset();
          this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Info. familiar creada exitosamente.' });
        },
        error: (err) => {
          console.error('Error creando la info. familiar', err);
          this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Ocurrió un error al crear la info. familiar. Vuelve a intentarlo.' });
        }
      });
    }
  }

  editarFamiliar(familiar: Family): void {
    this.editMode = true;
    this.selectedFamiliar = familiar;
    this.familiarForm.patchValue(familiar);
  }

  eliminarFamiliar(id: number): void {
    this.familyService.delete(id).subscribe({
      next: () => {
        this.cargarFamilia();
      },
      error: (err) => {
        console.error('Error eliminando la info. familiar', err);
        this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Ocurrió un error al eliminar la info. familiar. Vuelve a intentarlo.' });
      }
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