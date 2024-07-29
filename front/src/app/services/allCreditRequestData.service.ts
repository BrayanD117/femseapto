import { Injectable } from '@angular/core';
import { forkJoin, Observable } from 'rxjs';
import { map, switchMap } from 'rxjs/operators';
import { BankAccountTypeService } from './bank-account-type.service';
import { CitiesService } from './cities.service';
import { CompanyService } from './company.service';
import { ContractTypeService } from './contract-type.service';
import { CountriesService } from './countries.service';
import { CreditBalanceService } from './credit-balance.service';
import { DepartmentsService } from './departments.service';
import { DocumentTypeService } from './document-type.service';
import { EducationLevelService } from './education-level.service';
import { FamilyService } from './family.service';
import { FinancialInfoService } from './financial-info.service';
import { GenderService } from './gender.service';
import { HouseTypeService } from './house-type.service';
import { InternationalTransactionsService } from './international-transactions.service';
import { LineasCreditoService } from './lineas-credito.service';
import { MaritalStatusService } from './marital-status.service';
import { NaturalpersonService } from './naturalperson.service';
import { PublicPersonService } from './public-person.service';
import { RecommendationService } from './recommendation.service';
import { RecommendationTypeService } from './recommendation-type.service';
import { RelationshipService } from './relationship.service';
import { RequestCreditService } from './request-credit.service';
import { UserInfoService } from './user-info.service';
import { UserService } from './user.service';
import { ZoneService } from './zone.service';

@Injectable({
  providedIn: 'root'
})
export class AllCreditRequestDataService {
  constructor(
    private bankAccountTypeService: BankAccountTypeService,
    private citiesService: CitiesService,
    private companyService: CompanyService,
    private contractTypeService: ContractTypeService,
    private countriesService: CountriesService,
    private creditBalanceService: CreditBalanceService,
    private departmentsService: DepartmentsService,
    private documentTypeService: DocumentTypeService,
    private educationLevelService: EducationLevelService,
    private familyService: FamilyService,
    private financialInfoService: FinancialInfoService,
    private genderService: GenderService,
    private houseTypeService: HouseTypeService,
    private internationalTransactionsService: InternationalTransactionsService,
    private lineasCreditoService: LineasCreditoService,
    private maritalStatusService: MaritalStatusService,
    private naturalpersonService: NaturalpersonService,
    private publicPersonService: PublicPersonService,
    private recommendationService: RecommendationService,
    private recommendationTypeService: RecommendationTypeService,
    private relationshipService: RelationshipService,
    private requestCreditService: RequestCreditService,
    private userInfoService: UserInfoService,
    private userService: UserService,
    private zoneService: ZoneService
  ) {}

  getAllData(userId: number): Observable<any> {
    return this.userService.getById(userId).pipe(
      switchMap(user => {
        const cityId = user.mpioResidencia || '';
        const companyId = user.idEmpresaLabor || 0;
        const contractTypeId = user.idTipoContrato || 0;
        const countryId = user.paisNacimiento || 0;

        return forkJoin({
          // creditBalances: this.creditBalanceService.getByUserId(userId),
          bankAccountTypes: this.bankAccountTypeService.getAll(),
          // city: this.citiesService.getById(cityId),
          // company: this.companyService.getById(companyId),
          // contractType: this.contractTypeService.getById(contractTypeId),
          country: this.countriesService.getById(countryId),
          departments: this.departmentsService.getAll(),
          documentTypes: this.documentTypeService.getAll(),
          educationLevels: this.educationLevelService.getAll(),
          family: this.familyService.getByUserId(userId),
          financialInfo: this.financialInfoService.getByUserId(userId),
          genders: this.genderService.getAll(),
          houseTypes: this.houseTypeService.getAll(),
          internationalTransactions: this.internationalTransactionsService.getByUserId(userId),
          lineasCredito: this.lineasCreditoService.obtenerLineasCredito(),
          maritalStatus: this.maritalStatusService.getAll(),
          naturalPerson: this.naturalpersonService.getByUserId(userId),
          publicPerson: this.publicPersonService.getByUserId(userId),
          recommendations: this.recommendationService.getByUserId(userId),
          recommendationTypes: this.recommendationTypeService.getAll(),
          relationships: this.relationshipService.getAll(),
          //requestCredits: this.requestCreditService.getAll({ userId }),
          userInfo: this.userInfoService.getUserInfo(),
          user: this.userService.getById(userId),
          zones: this.zoneService.getAll()
        }).pipe(
          map(response => ({
            ...response,
          }))
        );
      })
    );
  }
}
