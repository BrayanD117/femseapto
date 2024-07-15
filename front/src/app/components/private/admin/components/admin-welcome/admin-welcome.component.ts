import { Component } from '@angular/core';
import { GenerateCreditRequestComponent } from '../generate-credit-request/generate-credit-request.component';

@Component({
  selector: 'app-admin-welcome',
  standalone: true,
  imports: [GenerateCreditRequestComponent],
  templateUrl: './admin-welcome.component.html',
  styleUrl: './admin-welcome.component.css'
})
export class AdminWelcomeComponent {

}
