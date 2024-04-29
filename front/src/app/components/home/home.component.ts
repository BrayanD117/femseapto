import { Component } from '@angular/core';

// Components
import { CarouselComponent } from '../home/components/carousel/carousel.component';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CarouselComponent],
  templateUrl: './home.component.html',
  styleUrl: './home.component.css'
})
export class HomeComponent {

}
