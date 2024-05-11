import { Routes } from '@angular/router';

// Components
import { HomeComponent } from './components/home/home.component';
import { AboutComponent } from './components/about/about.component';
import { AgreementsComponent } from './components/agreements/agreements.component';
import { CreditServiceComponent } from './components/credit-service/credit-service.component';
import { SavingServiceComponent } from './components/saving-service/saving-service.component';
import { LoginComponent } from './components/login/login.component';
import { WelcomeComponent } from './components/welcome/welcome.component';
// Auth User Components
import { UserInfoComponent } from './components/user/components/user-info/user-info.component';
import { UserSavingComponent } from './components/user/components/user-saving/user-saving.component';
import { UserCreditsComponent } from './components/user/components/user-credits/user-credits.component';

// Guards
import { LoginGuard } from './guards/login.guard';
import { LoginRedirectGuard } from './guards/login-redirect.guard';

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
    { path: 'login', component: LoginComponent, canActivate: [LoginRedirectGuard] },
    { path: 'welcome', component: WelcomeComponent, canActivate: [LoginGuard]  },
    { path: 'user', component: AboutComponent, canActivate: [LoginGuard], 
        children: [
        { path: 'MyInformation', component: UserInfoComponent },
        { path: 'MySavings', component: UserSavingComponent },
        { path: 'MyCredits', component: UserCreditsComponent },

    ]}
];
