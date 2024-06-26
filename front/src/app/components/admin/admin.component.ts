import { Component } from '@angular/core';

import { SidebarAdminComponent } from './components/sidebar-admin/sidebar-admin.component';

@Component({
  selector: 'app-admin',
  standalone: true,
  imports: [SidebarAdminComponent],
  templateUrl: './admin.component.html',
  styleUrl: './admin.component.css'
})
export class AdminComponent {

}
