import { Component, HostListener, ViewEncapsulation } from '@angular/core';

import { CarouselModule, Carousel,  } from 'primeng/carousel';
import { CardModule } from 'primeng/card';

@Component({
  selector: 'app-agreements-carousel',
  standalone: true,
  imports: [CarouselModule, CardModule],
  templateUrl: './agreements-carousel.component.html',
  styleUrl: './agreements-carousel.component.css',
  encapsulation: ViewEncapsulation.None,
})
export class AgreementsCarouselComponent {
  numVisible: number = 3;
  numScroll: number = 3;

  responsiveOptions: { breakpoint: string, numVisible: number, numScroll: number }[] = [
    {
      breakpoint: '1024px',
      numVisible: 2,
      numScroll: 2
    },
    {
      breakpoint: '768px',
      numVisible: 1,
      numScroll: 1
    },
    {
      breakpoint: '560px',
      numVisible: 1,
      numScroll: 1
    }
  ];

  images: { itemImageSrc: string, title: string }[] = [
    {
      itemImageSrc: '../../../assets/ayuda_mutua.jpg',
      title: 'Ayuda mutua'
    },
    {
      itemImageSrc: '../../../assets/responsabilidad.jpg',
      title: 'Responsabilidad'
    },
    {
      itemImageSrc: '../../../assets/honestidad.jpg',
      title: 'Honestidad'
    },
    {
      itemImageSrc: '../../../assets/respeto.jpg',
      title: 'Respeto'
    },
    {
      itemImageSrc: '../../../assets/transparencia.jpg',
      title: 'Transparencia'
    },
    {
      itemImageSrc: '../../../assets/confianza.jpg',
      title: 'Confianza'
    }
  ];

  ngOnInit() {
    this.adjustCarouselSettings();
  }

  @HostListener('window:resize')
  onResize() {
    this.adjustCarouselSettings();
  }

  adjustCarouselSettings() {
    const maxVisible = Math.min(3, this.images.length);
    const maxScroll = Math.min(3, this.images.length);

    this.numVisible = maxVisible;
    this.numScroll = maxScroll;

    this.responsiveOptions = [
      {
        breakpoint: '1024px',
        numVisible: Math.min(2, maxVisible),
        numScroll: Math.min(2, maxScroll)
      },
      {
        breakpoint: '768px',
        numVisible: Math.min(1, maxVisible),
        numScroll: Math.min(1, maxScroll)
      },
      {
        breakpoint: '560px',
        numVisible: Math.min(1, maxVisible),
        numScroll: Math.min(1, maxScroll)
      }
    ];
  }
}
