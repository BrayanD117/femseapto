import { Component, HostListener } from '@angular/core';

// Primeng Modules
import { CarouselModule, Carousel } from 'primeng/carousel';

@Component({
  selector: 'app-carousel',
  standalone: true,
  imports: [CarouselModule],
  templateUrl: './carousel.component.html',
  styleUrl: './carousel.component.css'
})
export class CarouselComponent {

  imagesSmall = [
    { previewImageSrc: "../../../../assets/images/home/slider/sl1.jpg" },
    { previewImageSrc: "../../../../assets/images/home/slider/sl2.jpg" },
  ];

  // Imágenes para pantallas grandes
  imagesLarge = [
    { previewImageSrc: "../../../../assets/images/home/slider/sl1.jpg" },
    { previewImageSrc: "../../../../assets/images/home/slider/sl2.jpg" },
  ];

  // Imágenes activas que se mostrarán en el carrusel
  activeImages = this.imagesLarge;

  constructor() {
    this.updateImageSet(window.innerWidth);
    Carousel.prototype.onTouchMove = () => {};
  }

  @HostListener('window:resize', ['$event'])
  onResize(event: { target: { innerWidth: number; }; }) {
    this.updateImageSet(event.target.innerWidth);
  }

  private updateImageSet(windowWidth: number) {
    if (windowWidth < 768) { // Considera pantallas menores a 768px como pequeñas
      this.activeImages = this.imagesSmall;
    } else {
      this.activeImages = this.imagesLarge;
    }
  }
}
