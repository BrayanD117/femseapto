import { Component } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { TableModule } from 'primeng/table';
import { MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';

import { Recommendation, RecommendationService } from '../../../../../../../services/recommendation.service';
import { LoginService } from '../../../../../../../services/login.service';
import { RecommendationType, RecommendationTypeService } from '../../../../../../../services/recommendation-type.service';
import {
  Department,
  DepartmentsService,
} from '../../../../../../../services/departments.service';
import {
  City,
  CitiesService,
} from '../../../../../../../services/cities.service';

@Component({
  selector: 'app-recommendation',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, TableModule, ToastModule],
  providers: [MessageService],
  templateUrl: './recommendation.component.html',
  styleUrl: './recommendation.component.css'
})
export class RecommendationComponent {
  recommendations: Recommendation[] = [];
  recommendationForm: FormGroup;
  editMode = false;
  selectedRecommendation: Recommendation | null = null;
  userId: number | null = null;

  recommendationTypes: RecommendationType[] = [];
  departments: Department[] = [];
  cities: City[] = [];

  constructor(private fb: FormBuilder, private recommendationService: RecommendationService,
    private loginService: LoginService, private recommendationTypeService: RecommendationTypeService,
    private messageService: MessageService, private departmentsService: DepartmentsService,
    private citiesService: CitiesService
  ) {
    this.recommendationForm = this.fb.group({
      id: [''],
      idUsuario: [{ value: '', disabled: true }, Validators.required],
      nombreRazonSocial: ['', Validators.required],
      parentesco: ['', Validators.required],
      idTipoReferencia: ['', Validators.required],
      idDpto: ['', Validators.required],
      idMunicipio: ['', Validators.required],
      direccion: ['', Validators.required],
      telefono: ['', Validators.required],
      correoElectronico: ['', [Validators.required, Validators.email]]
    });
  }

  ngOnInit(): void {
    this.getUserIdFromToken();
    this.loadRecommendations();
    this.getAllRecommendationTypes();
    this.getAllDepartments();

    const idMpio = this.recommendationForm.get('idMunicipio')?.value;
    if (idMpio) {
      this.onDepartmentChange();
    }
  }

  getUserIdFromToken(): void {
    const token = this.loginService.getTokenClaims();
    if (token) {
      this.userId = token.userId;
    }
  }

  loadRecommendations(): void {
    if(this.userId) {
      this.recommendationService.getByUserId(this.userId).subscribe(data => {
        this.recommendations = data;
      });
    }
  }

  getAllRecommendationTypes(): void {
    this.recommendationTypeService.getAll().subscribe(types => {
      this.recommendationTypes = types;
    });
  }

  getAllDepartments(): void {
    this.departmentsService.getAll().subscribe((types) => {
      this.departments = types;
    });
  }

  submit(): void {
    console.log(this.recommendationForm.value);

    if (this.recommendationForm.invalid  || !this.userId) {
      return;
    }

    const recommendation: Recommendation = this.recommendationForm.getRawValue();
    recommendation.idUsuario = this.userId;
    console.log("Info. con Id usuario", recommendation);

    if (this.editMode) {
      if (this.selectedRecommendation && this.selectedRecommendation.id) {
        this.recommendationService.update(recommendation).subscribe({
          next: () => {
            this.loadRecommendations();
            this.cancelEdit();
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Referencia actualizada exitosamente.' });
          },
          error: (err) => {
            console.error('Error actualizando la referencia', err);
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Ocurrió un error al actualizar la referencia. Vuelve a intentarlo.' });
          }
        });
      }
    } else {
      this.recommendationService.create(recommendation).subscribe({
        next: () => {
          this.loadRecommendations();
          this.recommendationForm.reset();
          this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Referencia creada exitosamente.' });
        },
        error: (err) => {
          console.error('Error creando la referencia', err);
          this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Ocurrió un error al crear la referencia. Vuelve a intentarlo.' });
        }
      });
    }
  }

  onDepartmentChange(): void {
    const departmentId = this.recommendationForm.get('idDpto')?.value;
    if (departmentId) {
      this.citiesService.getByDepartmentId(departmentId).subscribe((data) => {
        this.cities = data;
      });
    }
  }

  editRecommendation(recommendation: Recommendation): void {
    this.editMode = true;
    this.selectedRecommendation = recommendation;

    // Limpiar las ciudades del select para evitar problemas de re-renderizado
    this.cities = [];

    this.citiesService.getById(recommendation.idMunicipio).subscribe((city) => {

      this.recommendationForm.get('idDpto')?.setValue(city.idDepartamento);

      this.citiesService
        .getByDepartmentId(city.idDepartamento)
        .subscribe((data) => {
          this.cities = data;

          this.recommendationForm
            .get('idMunicipio')
            ?.setValue(recommendation.idMunicipio);
        });
    });

    this.recommendationForm.patchValue(recommendation);
  }

  deleteRecommendation(id: number): void {
    this.recommendationService.delete(id).subscribe({
      next: () => {
        this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Referencia eliminada exitosamente.' });
        this.loadRecommendations();
      },
      error: (err) => {
        console.error('Error eliminando la referencia', err);
        this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Ocurrió un error al eliminar la referencia. Vuelve a intentarlo.' });
      }
    });
    this.recommendationForm.reset();
  }

  cancelEdit(): void {
    this.editMode = false;
    this.selectedRecommendation = null;
    this.recommendationForm.reset();
  }

  formReset() {
    this.recommendationForm.reset();
  }
}