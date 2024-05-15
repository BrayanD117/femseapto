import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { MenuItem } from 'primeng/api';
import { SpeedDialModule } from 'primeng/speeddial';

@Component({
  selector: 'app-whatsapp-button',
  standalone: true,
  imports: [SpeedDialModule],
  templateUrl: './whatsapp-button.component.html',
//     `:host ::ng-deep {
//         .speeddial-circle-demo {
//             .p-speeddial-quarter-circle {
//                 &.p-speeddial-direction-up-left {
//                     right: 0;
//                     bottom: 0;
//                 }
    
//                 &.p-speeddial-direction-up-right {
//                     left: 0;
//                     bottom: 0;
//                 }
    
//                 &.p-speeddial-direction-down-left {
//                     right: 0;
//                     top: 0;
//                 }
    
//                 &.p-speeddial-direction-down-right {
//                     left: 0;
//                     top: 0;
//                 }
//             }
//         }
//     }`
// ],
  styleUrls: ['./whatsapp-button.component.css', ],
  encapsulation: ViewEncapsulation.None,
})
export class WhatsappButtonComponent implements OnInit {
  items: MenuItem[] = [];
  iconPath = '../../../assets/WhatsApp.svg.webp';

  ngOnInit() {
    this.items = [
      {
          icon: "pi pi-whatsapp",
          routerLink: ['/fileupload']
      },
      {
          icon: "pi pi-whatsapp",
          target: '_blank',
          url: 'https://api.whatsapp.com/send/?phone=573212289646'
      }
  ];
}
}
