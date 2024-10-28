import { Component } from '@angular/core';
import { RouterLink, RouterOutlet, RouterLinkActive } from '@angular/router';
import { UsersReportComponent } from './components/users-report/users-report.component';

@Component({
  selector: 'app-manage-users',
  standalone: true,
  imports: [RouterOutlet, RouterLink, RouterLinkActive, UsersReportComponent],
  templateUrl: './manage-users.component.html',
  styleUrl: './manage-users.component.css'
})
export class ManageUsersComponent {

}