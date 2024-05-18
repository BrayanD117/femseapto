import { Component } from '@angular/core';
import { ImageModule } from 'primeng/image';
import { GalleriaModule } from 'primeng/galleria';

@Component({
  selector: 'app-about',
  standalone: true,
  imports: [ImageModule, GalleriaModule],
  templateUrl: './about.component.html',
  styleUrl: './about.component.css'
})
export class AboutComponent {

  responsiveOptions: any[] = [
    {
        breakpoint: '1024px',
        numVisible: 5
    },
    {
        breakpoint: '768px',
        numVisible: 3
    },
    {
        breakpoint: '560px',
        numVisible: 1
    }
];

  images = [
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
    },
    // {
    //   itemImageSrc: '../../../assets/equidad.jpg',
    //   title: 'Equidad'
    // },
  ]
}
