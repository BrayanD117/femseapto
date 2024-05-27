import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';

import { AgreementsCarouselComponent } from './components/agreements-carousel/agreements-carousel.component';

@Component({
  selector: 'app-agreements',
  standalone: true,
  imports: [RouterLink, AgreementsCarouselComponent],
  templateUrl: './agreements.component.html',
  styleUrl: './agreements.component.css'
})
export class AgreementsComponent {

}
