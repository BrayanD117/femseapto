import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';



import { UserComponent } from './components/user/user.component';
import { NaturalPersonComponent } from './components/natural-person/natural-person.component';
import { FinancialInfoComponent } from './components/financial-info/financial-info.component';
import { FamilyInformationComponent } from './components/family-information/family-information.component';
import { RecommendationComponent } from './components/recommendation/recommendation.component';
import { InternationalTransactionsComponent } from './components/international-transactions/international-transactions.component';
import { PublicPersonComponent } from './components/public-person/public-person.component';
import { ContactComponent } from './components/contact/contact.component';

@Component({
  selector: 'app-user-info',
  standalone: true,
  imports: [CommonModule, UserComponent, NaturalPersonComponent, FinancialInfoComponent,
    FamilyInformationComponent, RecommendationComponent, InternationalTransactionsComponent,
    PublicPersonComponent, ContactComponent],
  templateUrl: './user-info.component.html',
  styleUrls: ['./user-info.component.css']
})
export class UserInfoComponent {

  constructor() { }
}
