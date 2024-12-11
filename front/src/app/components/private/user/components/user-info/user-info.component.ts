import { Component, ViewChild, AfterViewInit } from '@angular/core';
import { CommonModule } from '@angular/common';



import { UserComponent } from './components/user/user.component';
import { NaturalPersonComponent } from './components/natural-person/natural-person.component';
import { FinancialInfoComponent } from './components/financial-info/financial-info.component';
import { FamilyInformationComponent } from './components/family-information/family-information.component';
import { RecommendationComponent } from './components/recommendation/recommendation.component';
import { InternationalTransactionsComponent } from './components/international-transactions/international-transactions.component';
import { PublicPersonComponent } from './components/public-person/public-person.component';
import { ContactComponent } from './components/contact/contact.component';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';

@Component({
  selector: 'app-user-info',
  standalone: true,
  imports: [CommonModule, ToastModule, UserComponent, NaturalPersonComponent, FinancialInfoComponent,
    FamilyInformationComponent, RecommendationComponent, InternationalTransactionsComponent,
    PublicPersonComponent, ContactComponent],
    providers: [MessageService],
  templateUrl: './user-info.component.html',
  styleUrls: ['./user-info.component.css']
})
export class UserInfoComponent {
  @ViewChild(ContactComponent) contactComponent!: ContactComponent;

  constructor(private messageService: MessageService) { }

  showContactNotification(): void {
    this.messageService.add({
      closable: false,
      severity: 'warn',
      summary: 'Atención',
      detail: 'contact-update',
      sticky: true,
    });
  }
  
  scrollToContact(): void {
    const contactSection = document.getElementById('contact-section');
    if (contactSection) {
      const offset = -70;
      const topPosition = contactSection.getBoundingClientRect().top + window.scrollY + offset;

      window.scrollTo({
        top: topPosition,
        behavior: 'smooth',
      });
    }
  }
}
