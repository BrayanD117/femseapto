import { Injectable } from '@angular/core';
import { FamilyService } from './family.service';
import { FinancialInfoService } from './financial-info.service';
import { InternationalTransactionsService } from './international-transactions.service';
import { NaturalpersonService } from './naturalperson.service';
import { PublicPersonService } from './public-person.service';
import { RecommendationService } from './recommendation.service';
import { MessageService } from 'primeng/api';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root',
})
export class UserInfoValidationService {

  constructor(
    private financialInformationService: FinancialInfoService,
    private familyService: FamilyService,
    private internationalTransactionsService: InternationalTransactionsService,
    private naturalpersonService: NaturalpersonService,
    private publicPersonService: PublicPersonService,
    private recommendationService: RecommendationService,
    private messageService: MessageService,
    private router: Router
  ) {}

  validateUserRecords(userId: number, showWarning: (detail: string) => void): void {
    if(userId) {
      this.financialInformationService.validate(userId).subscribe(response => {
        if (!response) {
          showWarning('Por favor, registre la información financiera');
        }
      });

      this.familyService.validate(userId).subscribe(response => {
        if (!response) {
          showWarning('Por favor, registre la información familiar');
        }
      });

      this.internationalTransactionsService.validate(userId).subscribe(response => {
        if (!response) {
          showWarning('Por favor, registre la información de operaciones internacionales');
        }
      });

      this.naturalpersonService.validate(userId).subscribe(response => {
        if (!response) {
          showWarning('Por favor, registre la información general');
        }
      });

      this.publicPersonService.validate(userId).subscribe(response => {
        if (!response) {
          showWarning('Por favor, registre la información de personas públicamente expuestas');
        }
      });

      this.recommendationService.validatePersonal(userId).subscribe(response => {
        if (!response) {
          showWarning('Por favor, registre al menos una referencia personal');
        }
      });

      this.recommendationService.validateFamiliar(userId).subscribe(response => {
        if (!response) {
          showWarning('Por favor, registre al menos una referencia familiar');
        }
      });
    }
  }
}
