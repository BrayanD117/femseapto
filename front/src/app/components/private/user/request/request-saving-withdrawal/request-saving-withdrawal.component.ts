import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';
import { LoginService } from '../../../../../services/login.service';
import { RequestSavingWithdrawal, RequestSavingWithdrawalService } from '../../../../../services/request-saving-withdrawal.service';
import { SavingLine, SavingLinesService } from '../../../../../services/saving-lines.service';
import { SavingBalance, SavingBalanceService } from '../../../../../services/saving-balance.service';
import { forkJoin } from 'rxjs';
import { FinancialInfoService } from '../../../../../services/financial-info.service';
import { InputNumberModule } from 'primeng/inputnumber';
import { Router } from '@angular/router';

import { NaturalpersonService } from '../../../../../services/naturalperson.service';
import { InfoRequestSavingComponent } from '../request-saving/info-request-saving/info-request-saving.component';

@Component({
  selector: 'app-request-saving-withdrawal',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, ToastModule, InputNumberModule, InfoRequestSavingComponent],
  providers: [MessageService],
  templateUrl: './request-saving-withdrawal.component.html',
  styleUrls: ['./request-saving-withdrawal.component.css']
})
export class RequestSavingWithdrawalComponent implements OnInit {
  savingWdRequestForm: FormGroup;
  userId: number | null = null;
  savingLines: SavingLine[] = [];
  savingBalances: SavingBalance[] = [];
  availableBalance: string | null = null;

  displayMessage: string = '';
  isAdditionalDisabled: boolean = false;

  constructor(private fb: FormBuilder,
              private loginService: LoginService,
              private savingWdRequestService: RequestSavingWithdrawalService,
              private savingLinesService: SavingLinesService,
              private savingBalanceService: SavingBalanceService,
              private financialInformationService: FinancialInfoService,
              private messageService: MessageService,
              private router: Router,
              private naturalpersonService: NaturalpersonService,
    ) {
    this.savingWdRequestForm = this.fb.group({
      id: [''],
      idUsuario: ['', Validators.required],
      idLineaAhorro: ['', Validators.required],
      montoRetirar: [, Validators.required],
      banco: [''],
      numeroCuenta: [''],
      devolucionCaja: ['NO'],
      observaciones: [''],
      continuarAhorro: ['', Validators.required]
    });
  }

  ngOnInit(): void {
    this.getUserIdFromToken();

    if(this.userId)
      this.validateUserRecords();
      //this.userInfoValidationService.validateUserRecords(this.userId, this.handleWarning.bind(this));
    
    this.getSavingBalances();

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

  validateUserRecords(): void {

    if(this.userId) {
      this.naturalpersonService.validate(this.userId).subscribe(response => {
        if (!response) {
          this.displayMessage = 'Por favor, registre la información personal';
          this.isAdditionalDisabled = true;
          this.messageService.add({ severity: 'warn', summary: 'Aviso', detail: this.displayMessage });
        } else {
          this.isAdditionalDisabled = false;
        }
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
          //console.log(this.savingLines);
        });
      });
    }
  }

  /*private handleWarning(detail: string): void {
    this.messageService.add({ severity: 'warn', summary: 'Aviso', detail });
    setTimeout(() => {
      this.router.navigate(['/auth/user/information']);
    }, 5000);
  }*/


  checkWithdrawalAmount(): void {
    const selectedLineId = this.savingWdRequestForm.get('idLineaAhorro')?.value;
    const withdrawalAmount = this.savingWdRequestForm.get('montoRetirar')?.value;

    const selectedLine = this.savingBalances.find(balance => balance.idLineaAhorro === selectedLineId);
    //console.log("linea sele", selectedLine)
    if (selectedLine) {
      const balanceAmount = selectedLine.valorSaldo;
      this.availableBalance = this.formatNumber(balanceAmount.toString());
      //console.log("balance total", balanceAmount)

      if (withdrawalAmount > balanceAmount) {
        //console.log("es mayor")
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
      //console.log(data);

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

  formatNumber(value: string): string {
    const numericValue = parseFloat(value.replace(',', '.')); // Asegura que el formato de número sea válido
    return new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP',
      minimumFractionDigits: 0
    }).format(numericValue);
  }
}
