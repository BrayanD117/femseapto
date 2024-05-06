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
  images = [
    { previewImageSrc: "../../../../assets/images/home/slider/sl1.jpg" },
    { previewImageSrc: "../../../../assets/images/home/slider/sl2.jpg" },
  ];
}
