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
import { CreditBalanceComponent } from './components/private/user/credit-balance/credit-balance.component';
// Request Components
import { RequestCreditComponent } from './components/private/user/request/request-credit/request-credit.component';
import { RequestSavingComponent } from './components/private/user/request/request-saving/request-saving.component';
import { RequestSavingWithdrawalComponent } from './components/private/user/request/request-saving-withdrawal/request-saving-withdrawal.component';

// Admin components
import { AdminComponent } from './components/private/admin/admin.component';
import { AdminWelcomeComponent } from './components/private/admin/components/admin-welcome/admin-welcome.component';
import { CreditRequestsComponent } from './components/private/admin/components/credit-requests/credit-requests.component';
import { SavingRequestsComponent } from './components/private/admin/components/saving-requests/saving-requests.component';

// Guards
import { LoginGuard } from './guards/login.guard';
import { LoginRedirectGuard } from './guards/login-redirect.guard';
import { AdminGuard } from './guards/admin.guard';
import { UserGuard } from './guards/user.guard';

import { PublicComponent } from './components/public/public.component';
import { PrivateComponent } from './components/private/private.component';




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
            { path: 'user', canActivate: [UserGuard], 
                children: [
                    { path: '', component: WelcomeComponent },
                    { path: 'information', component: UserInfoComponent },
                    { path: 'savings', component: UserSavingComponent },
                    { path: 'credits',
                        children: [
                        { path: 'balance', component: CreditBalanceComponent },
                    ],
                    },
                    { path: 'request', 
                        children: [
                            { path: 'credit', component: RequestCreditComponent },
                            { path: 'saving', component: RequestSavingComponent },
                            { path: 'withdrawal', component: RequestSavingWithdrawalComponent }
                        ], 
                    },    
                ]
            },
            { path: 'admin', component: AdminComponent, canActivate: [AdminGuard],
                children: [
                    { path: '', component: AdminWelcomeComponent },
                    { path: 'credits', component: CreditRequestsComponent },
                    { path: 'savings', component: SavingRequestsComponent }
                ],
                
            }
        ]
    }
];
