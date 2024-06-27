import { Component, HostListener, OnInit, ViewEncapsulation } from '@angular/core';
import { ImageModule } from 'primeng/image';
import { GalleriaModule } from 'primeng/galleria';
import { CardModule } from 'primeng/card';
import { CarouselModule } from 'primeng/carousel';

@Component({
  selector: 'app-about',
  standalone: true,
  imports: [ImageModule, GalleriaModule, CardModule, CarouselModule],
  templateUrl: './about.component.html',
  styleUrls: ['./about.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class AboutComponent implements OnInit {

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

  principles: { name: string, description: string }[] = [
    {
      name: 'Asociación voluntaria',
      description: 'Los asociados aceptan voluntariamente las responsabilidades que conlleva asociarse, sin discriminaciones de raza, género, política o religión.'
    },
    {
      name: 'Democracia',
      description: 'Todos los asociados deben participar en la toma de decisiones y los miembros de los órganos de administración y control tendrán la responsabilidad de su gestión ante los asociados, teniendo en cuenta que el fondo pertenece a todos los asociados.'
    },
    {
      name: 'Autonomía e independencia',
      description: 'El control lo tienen los asociados y por lo tanto el fondo es autónomo en la toma de decisiones, que están siempre ajustadas a la ley y a las buenas costumbres.'
    },
    {
      name: 'Educación',
      description: 'FEMSEAPTO brinda educación a sus asociados, miembros de los órganos de administración, control y empleados, con el fin de contribuir eficazmente al desarrollo del fondo.'
    },
    {
      name: 'Responsabilidad social',
      description: 'FEMSEAPTO fomenta el desarrollo de la comunidad en aspectos económicos, sociales, ambientales y culturales.'
    },
    {
      name: 'Administración democrática y participativa',
      description: 'Al interior del Fondo de Empleados, se desarrollan procesos participativos que promueven la amplia participación de sus asociados en la cual ejercen su derecho a voz y voto, delegando de forma representativa la confianza para ser representado dentro de los intereses comunes y colectivos.'
    },
    {
      name: 'Participación y control económico y social por parte de los asociados',
      description: 'Los asociados del Fondo de Empleados participan de su proceso económico, desde la generación hasta el uso y apropiación de productos, programas y/o servicios beneficiándose de estos, a su vez ejercen un control económico, social y administrativo a través de la revisoría fiscal como control externo, y un control social a través del comité de control social.'
    },
    {
      name: 'Interés colectivo por los asociados, su grupo familiar y el medio ambiente',
      description: 'El Fondo de Empleados busca permanentemente la satisfacción de las necesidades de sus asociados y sus familiares forma colectiva. promoviendo con sus asociados y familias la implementación de prácticas solidarias para la protección del medio ambiente.'
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
    const maxVisible = Math.min(3, this.principles.length);
    const maxScroll = Math.min(3, this.principles.length);

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
