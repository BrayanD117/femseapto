import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { MenuItem } from 'primeng/api';
import { SpeedDialModule } from 'primeng/speeddial';
import { TooltipModule } from 'primeng/tooltip';

@Component({
  selector: 'app-whatsapp-button',
  standalone: true,
  imports: [SpeedDialModule, TooltipModule],
  templateUrl: './whatsapp-button.component.html',
  styleUrls: ['./whatsapp-button.component.css'],
  encapsulation: ViewEncapsulation.None,
})
export class WhatsappButtonComponent implements OnInit {
  items: any[] = [];
  iconPath = '../../../assets/WhatsApp.svg.webp';

  ngOnInit() {
    this.items = [
      {
        icon: "pi pi-phone",
        url: 'https://api.whatsapp.com/send/?phone=573212289646',
        tooltip: 'Álvaro Patiño'
      },
      {
        icon: "pi pi-phone",
        url: 'https://api.whatsapp.com/send/?phone=573212289646',
        tooltip: 'Álvaro Patiño'
      }
    ];
  }

  navigate(url: string) {
    window.open(url, '_blank');
  }
}
