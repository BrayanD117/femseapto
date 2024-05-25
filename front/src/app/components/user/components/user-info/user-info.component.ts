import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

// Components
import { UserInfoService } from '../../../../services/user-info.service';
import { DepartmentsService } from '../../../../services/departments.service';
import { CitiesService } from '../../../../services/cities.service';

import { AutoCompleteModule } from 'primeng/autocomplete';

interface AutoCompleteCompleteEvent {
    originalEvent: Event;
    query: string;
}

@Component({
  selector: 'app-user-info',
  standalone: true,
  imports: [FormsModule, CommonModule, AutoCompleteModule],
  templateUrl: './user-info.component.html',
  styleUrls: ['./user-info.component.css']
})
export class UserInfoComponent implements OnInit {
  userInfo: any = {};
  loading: boolean = true;
  error: string = '';
  originalUserInfo: any = {};
  isDirty: boolean = false;

  departments: any[] = [];
  selectedDepartment: any;
  filteredDepartments: any[] = [];

  cities: any[] = [];
  selectedCities: any;
  filteredCities: any[] = [];

  constructor(private userInfoService: UserInfoService,
    private departmentsService: DepartmentsService,
    private citiesService: CitiesService) {}
  
  ngOnInit(): void {
    this.userInfoService.getUserInfo().subscribe({
      next: (data) => {
        if (data.success) {
          this.userInfo = data.data;
          this.originalUserInfo = { ...data.data };
        } else {
          this.error = data.message;
        }
        this.loading = false;
      },
      error: (err) => {
        this.error = 'Error al obtener la información del usuario.';
        this.loading = false;
      }
    });

    this.departmentsService.getDepartments().subscribe((departaments) => {
      this.departments = departaments;
    });
  }

  onInputChange(): void {
    this.isDirty = Object.keys(this.userInfo).some(
      key => this.userInfo[key] !== this.originalUserInfo[key]
    );
  }

  onSubmit(): void {
    if (this.isDirty) {
      console.log('Updating user info:', this.userInfo);
    }
  }

  filterDepartment(event: AutoCompleteCompleteEvent) {
    let filtered: any[] = [];
    let query = event.query;

    for (let i = 0; i < (this.departments as any[]).length; i++) {
        let dptm = (this.departments as any[])[i];
        if (dptm.nombre.toLowerCase().indexOf(query.toLowerCase()) == 0) {
            filtered.push(dptm);
        }
    }

    this.filteredDepartments = filtered;
  }

  filterCity(event: AutoCompleteCompleteEvent) {
    let filtered: any[] = [];
    let query = event.query;

    for (let i = 0; i < this.cities.length; i++) {
        let city = this.cities[i];
        if (city.nombre.toLowerCase().indexOf(query.toLowerCase()) == 0) {
            filtered.push(city);
        }
    }

    this.filteredCities = filtered;
  }

  onDepartmentSelect(event: any) {
    this.citiesService.getCitiesByDepartment(this.selectedDepartment.id).subscribe((cities) => {
      this.cities = cities;
      this.selectedCities = null; // Clear selected municipality when department changes
      this.filteredCities = []; // Clear filtered municipalities
    });
  }
}