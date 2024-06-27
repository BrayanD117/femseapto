import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { AccordionModule } from 'primeng/accordion';
import { UserInfoService } from '../../../../services/user-info.service';
import { DepartmentsService } from '../../../../services/departments.service';
import { CitiesService } from '../../../../services/cities.service';
import { AutoCompleteModule } from 'primeng/autocomplete';

interface AutoCompleteCompleteEvent {
    originalEvent: Event;
    query: string;
}

interface Department {
    id: number;
    nombre: string;
}

interface City {
    id: number;
    nombre: string;
    id_departamento: number;
}

@Component({
  selector: 'app-user-info',
  standalone: true,
  imports: [FormsModule, CommonModule, AutoCompleteModule, AccordionModule],
  templateUrl: './user-info.component.html',
  styleUrls: ['./user-info.component.css']
})
export class UserInfoComponent implements OnInit {
  userInfo: any = {};
  loading: boolean = true;
  error: string = '';
  originalUserInfo: any = {};
  isDirty: boolean = false;

  departments: Department[] = [];
  selectedDepartment: Department | undefined;
  filteredDepartments: Department[] = [];

  cities: City[] = [];
  selectedCity: City | undefined;
  filteredCities: City[] = [];

  constructor(private userInfoService: UserInfoService,
              private departmentsService: DepartmentsService,
              private citiesService: CitiesService) {}
  
  ngOnInit(): void {
    this.userInfoService.getUserInfo().subscribe({
      next: (data) => {
        if (data.success) {
          this.userInfo = data.data;
          this.originalUserInfo = { ...data.data };
          console.log("User Info: ", this.userInfo);
          this.loadDepartmentsAndCities();
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

    this.departmentsService.getDepartments().subscribe((departments) => {
      this.departments = departments;
    });
  }

  loadDepartmentsAndCities() {
    const cityId = this.userInfo.personaNatural.mpioExpDoc;

    if (cityId) {
      this.citiesService.getCityById(cityId).subscribe(city => {
        this.selectedCity = city;
        if (this.selectedCity) {
          this.selectedDepartment = this.departments.find(dept => dept.id === this.selectedCity?.id_departamento);
        }
      });
    }
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
        this.selectedCity = this.cities.find(muni => muni.id === this.userInfo.personaNatural.mpioExpDoc);
        this.filteredCities = []; // Clear filtered municipalities
      });
    }
  }
}
