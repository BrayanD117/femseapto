import { Routes } from '@angular/router';

// Components
import { HomeComponent } from './components/home/home.component';
import { AboutComponent } from './components/about/about.component';
import { AgreementsComponent } from './components/agreements/agreements.component';
import { CreditServiceComponent } from './components/credit-service/credit-service.component';
import { SavingServiceComponent } from './components/saving-service/saving-service.component';
import { LoginComponent } from './components/login/login.component';

export const routes: Routes = [
    { path: '', component: HomeComponent },
    { path: 'about', component: AboutComponent },
    { path: 'agreements', component: AgreementsComponent },
    { path: 'services',
        children: [
            { path: '', pathMatch: 'full', redirectTo: 'savings' },
            { path: 'savings', component: SavingServiceComponent },
            { path: 'credits', component: CreditServiceComponent }
        ]
    },
    { path: 'login', component: LoginComponent }
];
