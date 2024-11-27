import { Component } from '@angular/core';
import { UsersInfoFilterComponent } from './components/users-info-filter/users-info-filter.component';
import { UsersSearchComponent } from './components/users-search/users-search.component';

@Component({
  selector: 'app-users-report',
  standalone: true,
  imports: [UsersInfoFilterComponent, UsersSearchComponent],
  templateUrl: './users-report.component.html',
  styleUrl: './users-report.component.css'
})
export class UsersReportComponent {

}