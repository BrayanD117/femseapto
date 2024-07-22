import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';
import { LoginService } from '../../../../../services/login.service';
import { RequestSavingWithdrawal, RequestSavingWithdrawalService } from '../../../../../services/request-saving-withdrawal.service';
import { SavingLine, SavingLinesService } from '../../../../../services/saving-lines.service';
import { CountriesService, Country } from '../../../../../services/countries.service';
import { SavingBalance, SavingBalanceService } from '../../../../../services/saving-balance.service';
import { forkJoin } from 'rxjs';
import { FinancialInformation, FinancialInfoService } from '../../../../../services/financial-info.service';
import { InputNumberModule } from 'primeng/inputnumber';
import { Router } from '@angular/router';
import { FamilyService } from '../../../../../services/family.service';
import { InternationalTransactionsService } from '../../../../../services/international-transactions.service';
import { NaturalpersonService } from '../../../../../services/naturalperson.service';
import { PublicPersonService } from '../../../../../services/public-person.service';
import { RecommendationService } from '../../../../../services/recommendation.service';
import { UserInfoValidationService } from '../../../../../services/user-info-validation.service';

@Component({
  selector: 'app-request-saving-withdrawal',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, ToastModule, InputNumberModule],
  providers: [MessageService],
  templateUrl: './request-saving-withdrawal.component.html',
  styleUrls: ['./request-saving-withdrawal.component.css']
})
export class RequestSavingWithdrawalComponent implements OnInit {
  savingWdRequestForm: FormGroup;
  userId: number | null = null;
  savingLines: SavingLine[] = [];
  savingBalances: SavingBalance[] = [];

  constructor(private fb: FormBuilder,
              private loginService: LoginService,
              private savingWdRequestService: RequestSavingWithdrawalService,
              private savingLinesService: SavingLinesService,
              private savingBalanceService: SavingBalanceService,
              private financialInformationService: FinancialInfoService,
              private messageService: MessageService,
              private router: Router,
            
              private userInfoValidationService: UserInfoValidationService) {
    this.savingWdRequestForm = this.fb.group({
      id: [''],
      idUsuario: ['', Validators.required],
      idLineaAhorro: ['', Validators.required],
      montoRetirar: [, Validators.required],
      banco: [''],
      numeroCuenta: [''],
      devolucionCaja: ['', Validators.required],
      observaciones: [''],
      continuarAhorro: ['', Validators.required]
    });
  }

  ngOnInit(): void {
    this.getUserIdFromToken();

    if(this.userId)
      this.userInfoValidationService.validateUserRecords(this.userId, this.handleWarning.bind(this));
    
    this.getSavingBalances();
    this.getFinancialInformation();

    // Subscribe to form value changes
    this.savingWdRequestForm.get('idLineaAhorro')?.valueChanges.subscribe(() => {
      this.checkWithdrawalAmount();
    });

    this.savingWdRequestForm.get('montoRetirar')?.valueChanges.subscribe(() => {
      this.checkWithdrawalAmount();
    });

    // Subscribe to devolucionCaja value changes
    this.savingWdRequestForm.get('devolucionCaja')?.valueChanges.subscribe(value => {
      this.handleDevolucionCajaChange(value);
    });

    // Subscribe to banco and numeroCuenta value changes
    this.savingWdRequestForm.get('banco')?.valueChanges.subscribe(() => {
      this.updateDevolucionCajaBasedOnInputs();
    });

    this.savingWdRequestForm.get('numeroCuenta')?.valueChanges.subscribe(() => {
      this.updateDevolucionCajaBasedOnInputs();
    });
  }

  getUserIdFromToken(): void {
    const token = this.loginService.getTokenClaims();
    if (token) {
      this.userId = token.userId;

      this.savingWdRequestForm.patchValue({
        idUsuario: this.userId
      });
    }
  }

  getFinancialInformation(): void {
    if(this.userId) {
      this.financialInformationService.getByUserId(this.userId).subscribe((info: FinancialInformation) => {
        this.savingWdRequestForm.patchValue({
          banco: info.nombreBanco,
          numeroCuenta: info.numeroCuentaBanc
        });
      });
    } 
  }

  getSavingBalances(): void {
    if(this.userId) {
      this.savingBalanceService.getByUserId(this.userId).subscribe((balances: SavingBalance[]) => {
        this.savingBalances = balances;

        const validBalances = balances.filter(balance => balance.valorSaldo > 0);

        const savingLineIds = Array.from(new Set(validBalances.map(balance => balance.idLineaAhorro)));
      
        const savingLineObservables = savingLineIds.map(id => this.savingLinesService.getById(id));
      
        forkJoin(savingLineObservables).subscribe((savingLines: SavingLine[]) => {
          this.savingLines = savingLines;
        });
      });
    }
  }

  private handleWarning(detail: string): void {
    this.messageService.add({ severity: 'warn', summary: 'Aviso', detail });
    setTimeout(() => {
      this.router.navigate(['/auth/user/information']);
    }, 5000);
  }

  checkWithdrawalAmount(): void {
    const selectedLineId = this.savingWdRequestForm.get('idLineaAhorro')?.value;
    const withdrawalAmount = this.savingWdRequestForm.get('montoRetirar')?.value;

    const selectedLine = this.savingBalances.find(balance => balance.idLineaAhorro === selectedLineId);
    console.log("linea sele", selectedLine)
    if (selectedLine) {
      const balanceAmount = selectedLine.valorSaldo;
      console.log("balance total", balanceAmount)

      if (withdrawalAmount > balanceAmount) {
        console.log("es mayor")
        this.savingWdRequestForm.get('montoRetirar')?.setErrors({ insufficientFunds: true });
        this.messageService.add({ severity: 'error', summary: 'Error', detail: 'El monto a retirar no puede superar el saldo disponible.' });
      } else {
        this.savingWdRequestForm.get('montoRetirar')?.setErrors(null);
      }
    }
  }

  handleDevolucionCajaChange(value: string): void {
    const bancoControl = this.savingWdRequestForm.get('banco');
    const numeroCuentaControl = this.savingWdRequestForm.get('numeroCuenta');

    if (value === 'SI') {
      bancoControl?.reset();
      numeroCuentaControl?.reset();
    }
  }

  updateDevolucionCajaBasedOnInputs(): void {
    const bancoValue = this.savingWdRequestForm.get('banco')?.value;
    const numeroCuentaValue = this.savingWdRequestForm.get('numeroCuenta')?.value;
    const devolucionCajaControl = this.savingWdRequestForm.get('devolucionCaja');

    if (bancoValue && numeroCuentaValue) {
      devolucionCajaControl?.setValue('NO');
    }
  }

  submit(): void {
    if (this.savingWdRequestForm.valid) {
      const data: RequestSavingWithdrawal = this.savingWdRequestForm.value;
      console.log(data);

      this.savingWdRequestService.create(data).subscribe({
        next: () => {
          this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Solicitud de retiro de ahorro creada correctamente' });
          setTimeout(() => {
            this.router.navigate(['/auth/user']);
          }, 2000);
        },
        error: (err) => {
          console.error('Error', err);
          this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo crear la solicitud de retiro de ahorro. Por favor, intente otra vez' });
        }
      });
    }
  }
}
