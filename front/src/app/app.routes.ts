import { Routes } from '@angular/router';

// Components
import { HomeComponent } from './components/public/home/home.component';
import { AboutComponent } from './components/public/about/about.component';
import { AgreementsComponent } from './components/public/agreements/agreements.component';
import { CreditServiceComponent } from './components/public/credit-service/credit-service.component';
import { SavingServiceComponent } from './components/public/saving-service/saving-service.component';
import { LoginComponent } from './components/public/login/login.component';
import { WelcomeComponent } from './components/private/welcome/welcome.component';
// Auth User Components
import { UserInfoComponent } from './components/private/user/components/user-info/user-info.component';
import { UserSavingComponent } from './components/private/user/components/user-saving/user-saving.component';
import { UserCreditsComponent } from './components/private/user/components/user-credits/user-credits.component';
// Request Components
import { RequestCreditComponent } from './components/private/user/request/request-credit/request-credit.component';
import { RequestSavingComponent } from './components/private/user/request/request-saving/request-saving.component';

// Admin components
import { AdminComponent } from './components/private/admin/admin.component';

// Guards
import { LoginGuard } from './guards/login.guard';
import { LoginRedirectGuard } from './guards/login-redirect.guard';
import { PublicComponent } from './components/public/public.component';
import { PrivateComponent } from './components/private/private.component';
import { AdminWelcomeComponent } from './components/private/admin/components/admin-welcome/admin-welcome.component';

export const routes: Routes = [
    { path: '', component: PublicComponent,
        children: [
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
            { path: 'login', component: LoginComponent, canActivate: [LoginRedirectGuard] }
        ]
    },
    { path: 'auth', component: PrivateComponent, canActivate: [LoginGuard],
        children: [
            // Hacer componente para Not Found{ path: '', redirectTo: 'user/welcome', pathMatch: 'full' },
            { path: 'user', 
                children: [
                    { path: '', component: WelcomeComponent },
                    { path: 'information', component: UserInfoComponent },
                    { path: 'savings', component: UserSavingComponent },
                    { path: 'credits', component: UserCreditsComponent },
                    { path: 'request', 
                        children: [
                            { path: 'credit', component: RequestCreditComponent },
                            { path: 'saving', component: RequestSavingComponent },
                        ], 
                    },    
                ]
            },
            { path: 'admin', component: AdminComponent,
                children: [
                    { path: '', component: AdminWelcomeComponent },
                ]
            }
        ]
    }
];
