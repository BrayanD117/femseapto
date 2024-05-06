import { ApplicationConfig } from '@angular/core';
import { provideRouter } from '@angular/router';
import { provideClientHydration } from '@angular/platform-browser';
import { provideHttpClient, withFetch } from '@angular/common/http';
import { routes } from './app.routes';
import { JwtHelperService, JWT_OPTIONS  } from '@auth0/angular-jwt';

// Lottie Animation Module
import { provideLottieOptions } from 'ngx-lottie';
import player from 'lottie-web';

export const appConfig: ApplicationConfig = {
  providers: [
    provideRouter(routes),
    provideLottieOptions({
      player: () => player,
    }),
    provideHttpClient(withFetch()),
    provideClientHydration(),
    { provide: JWT_OPTIONS, useValue: JWT_OPTIONS },
        JwtHelperService
  ]
};
