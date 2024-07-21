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
              private messageService: MessageService) {
    this.savingWdRequestForm = this.fb.group({
      id: [''],
      idUsuario: ['', Validators.required],
      idLineaAhorro: ['', Validators.required],
      montoRetirar: [, Validators.required],
      banco: ['', Validators.required],
      numeroCuenta: ['', Validators.required],
      devolucionCaja: ['', Validators.required],
      observaciones: ['', Validators.required],
      continuarAhorro: ['', Validators.required]
    });
  }

  ngOnInit(): void {
    this.getUserIdFromToken();
    //this.getAllSavingLines();
    //this.getCountries();
    this.getSavingBalances();
    this.getFinancialInformation();

    // Subscribe to form value changes
    this.savingWdRequestForm.get('idLineaAhorro')?.valueChanges.subscribe(() => {
      this.checkWithdrawalAmount();
    });

    this.savingWdRequestForm.get('montoRetirar')?.valueChanges.subscribe(() => {
      this.checkWithdrawalAmount();
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

        const savingLineIds = Array.from(new Set(balances.map(balance => balance.idLineaAhorro)));
      
        const savingLineObservables = savingLineIds.map(id => this.savingLinesService.getById(id));
      
        forkJoin(savingLineObservables).subscribe((savingLines: SavingLine[]) => {
          this.savingLines = savingLines;
        });
      });
    }
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

  submit(): void {
    if (this.savingWdRequestForm.valid) {
      const data: RequestSavingWithdrawal = this.savingWdRequestForm.value;
      console.log(data);
    }
  }
}
