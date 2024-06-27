import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';

@Component({
  selector: 'app-private',
  standalone: true,
  imports: [RouterOutlet],
  templateUrl: './private.component.html',
  styleUrl: './private.component.css'
})
export class PrivateComponent {

}
