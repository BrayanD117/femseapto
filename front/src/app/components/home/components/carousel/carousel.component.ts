import { Component } from '@angular/core';

// Primeng Modules
import { CarouselModule } from 'primeng/carousel';

@Component({
  selector: 'app-carousel',
  standalone: true,
  imports: [CarouselModule],
  templateUrl: './carousel.component.html',
  styleUrl: './carousel.component.css'
})
export class CarouselComponent {

  // Imágenes para pantallas grandes
  products = [
    { imageSrc: 'front/src/assets/images/home/slider/sl1.jpg' },
    { imageSrc: 'front/src/assets/images/home/slider/sl2.jpg' },
    { imageSrc: 'front/src/assets/images/home/slider/sl3.jpg' },
    { imageSrc: 'front/src/assets/images/home/slider/sl4.gif' },
  ];
}
