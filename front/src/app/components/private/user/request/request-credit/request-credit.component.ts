import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators, AbstractControl, ValidationErrors } from '@angular/forms';
import { ToastModule } from 'primeng/toast';
import { CurrencyFormatPipe } from '../../../../pipes/currency-format.pipe';
import { RequestCreditService } from '../../../../../services/request-credit.service';
import { LoginService } from '../../../../../services/login.service';
import { MessageService } from 'primeng/api';
import { Router } from '@angular/router';
import { LineasCreditoService } from '../../../../../services/lineas-credito.service';

import { FinancialInfoService } from '../../../../../services/financial-info.service';
import { FamilyService } from '../../../../../services/family.service';
import { InternationalTransactionsService } from '../../../../../services/international-transactions.service';
import { NaturalpersonService } from '../../../../../services/naturalperson.service';
import { PublicPersonService } from '../../../../../services/public-person.service';
import { RecommendationService } from '../../../../../services/recommendation.service';

@Component({
  selector: 'app-request-credit',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, ToastModule, CurrencyFormatPipe],
  providers: [MessageService],
  templateUrl: './request-credit.component.html',
  styleUrls: ['./request-credit.component.css']
})
export class RequestCreditComponent implements OnInit {
  creditForm: FormGroup;
  creditLines: any[] = [];
  selectedCreditLine: any = null;
  loanAmount = 0;
  creditConditions: string[] = [];

  constructor(
    private fb: FormBuilder,
    private creditsService: RequestCreditService,
    private loginService: LoginService,
    private messageService: MessageService,
    private router: Router,
    private lineasCreditoService: LineasCreditoService,
    private financialInfoService: FinancialInfoService,
    private familyService: FamilyService,
    private internationalTransactionsService: InternationalTransactionsService,
    private naturalpersonService: NaturalpersonService,
    private publicPersonService: PublicPersonService,
    private recommendationService: RecommendationService
  ) {
    this.creditForm = this.fb.group({
      montoSolicitado: [0, [Validators.required, Validators.min(1), this.maxLimitValidator.bind(this)]],
      plazoQuincenal: ['', [Validators.required, this.maxTermValidator.bind(this)]],
      valorCuotaQuincenal: [{ value: 0, disabled: false }, [Validators.required, Validators.min(1)]],
      idLineaCredito: ['', Validators.required],
      tasaInteres: [{ value: '', disabled: false }, Validators.required],
      valorMensual: [{ value: '', disabled: true }]
    });
  }

  ngOnInit(): void {
    this.validateUserRecords();

    this.lineasCreditoService.obtenerLineasCredito().subscribe(
      data => {
        this.creditLines = data;
      },
      error => {
        console.error('Error al obtener líneas de crédito:', error);
      }
    );
  }

  validateUserRecords(): void {
    const token = this.loginService.getTokenClaims();

    if(token) {
      this.financialInfoService.validate(token.userId).subscribe(response => {
        if (!response) {
          this.messageService.add({ severity: 'warn', summary: 'Aviso', detail: 'Por favor, registre la información financiera' });
          setTimeout(() => {
            this.router.navigate(['/auth/user/information']);
          }, 5000);
          return;
        }
      });

      this.familyService.validate(token.userId).subscribe(response => {
        if (!response) {
          this.messageService.add({ severity: 'warn', summary: 'Aviso', detail: 'Por favor, registre la información familiar' });
          setTimeout(() => {
            this.router.navigate(['/auth/user/information']);
          }, 5000);
          return;
        }
      });

      this.internationalTransactionsService.validate(token.userId).subscribe(response => {
        if (!response) {
          this.messageService.add({ severity: 'warn', summary: 'Aviso', detail: 'Por favor, registre la información de operaciones internacionales' });
          setTimeout(() => {
            this.router.navigate(['/auth/user/information']);
          }, 5000);
          return;
        }
      });

      this.naturalpersonService.validate(token.userId).subscribe(response => {
        if (!response) {
          this.messageService.add({ severity: 'warn', summary: 'Aviso', detail: 'Por favor, registre la información general' });
          setTimeout(() => {
            this.router.navigate(['/auth/user/information']);
          }, 5000);
          return;
        }
      });

      this.publicPersonService.validate(token.userId).subscribe(response => {
        if (!response) {
          this.messageService.add({ severity: 'warn', summary: 'Aviso', detail: 'Por favor, registre la información de personas públicamente expuestas' });
          setTimeout(() => {
            this.router.navigate(['/auth/user/information']);
          }, 5000);
          return;
        }
      });

      this.recommendationService.validatePersonal(token.userId).subscribe(response => {
        if (!response) {
          this.messageService.add({ severity: 'warn', summary: 'Aviso', detail: 'Por favor, registre al menos una referencia personal' });
          setTimeout(() => {
            this.router.navigate(['/auth/user/information']);
          }, 5000);
          return;
        }
      });

      this.recommendationService.validateFamiliar(token.userId).subscribe(response => {
        if (!response) {
          this.messageService.add({ severity: 'warn', summary: 'Aviso', detail: 'Por favor, registre al menos una referencia familiar' });
          setTimeout(() => {
            this.router.navigate(['/auth/user/information']);
          }, 5000);
          return;
        }
      });
    }   
  }

  onLoanAmountInput(event: Event): void {
    const inputElement = event.target as HTMLInputElement;
    const numericValue = inputElement.value.replace(/[^0-9]/g, '');
    this.loanAmount = parseInt(numericValue, 10) || 0;

    this.creditForm.get('montoSolicitado')?.setValue(this.loanAmount);
    inputElement.value = `$ ${this.loanAmount.toLocaleString('es-ES')}`;

    this.creditForm.get('montoSolicitado')?.updateValueAndValidity();
    this.calculateInterestRate();
    this.calculateCuotaQuincenal();
  }

  onCreditLineChange(): void {
    const selectedId = this.creditForm.get('idLineaCredito')?.value;
    this.selectedCreditLine = this.creditLines.find(line => line.id === selectedId);
    if (this.selectedCreditLine) {
      this.procesarCondiciones(this.selectedCreditLine.condiciones);
      this.creditForm.get('montoSolicitado')?.updateValueAndValidity();
      this.creditForm.get('plazoQuincenal')?.updateValueAndValidity();
      this.calculateInterestRate();
      this.calculateCuotaQuincenal();
    }
  }

  onPlazoChange(): void {
    this.creditForm.get('plazoQuincenal')?.updateValueAndValidity();
    this.calculateInterestRate();
    this.calculateCuotaQuincenal();
  }

  calculateInterestRate(): void {
    if (this.selectedCreditLine) {
      const plazo = this.creditForm.get('plazoQuincenal')?.value;
      const loanType = this.selectedCreditLine.nombre.toLowerCase();
      let tasaInteres = this.selectedCreditLine.tasaInteres1;

      if (loanType.includes('compra o mejoras de vivienda')) {
        if (plazo > 120 && plazo <= 240)
          tasaInteres = this.selectedCreditLine.tasaInteres2;
      } else if (loanType.includes('libre inversion')) {
        if (plazo > 96 && plazo <= 144)
          tasaInteres = this.selectedCreditLine.tasaInteres2;
      } else if (loanType.includes('credito ordinario')) {
        if (plazo > 96 && plazo <= 168)
          tasaInteres = this.selectedCreditLine.tasaInteres2;
      }

      this.creditForm.get('tasaInteres')?.setValue(tasaInteres);
      // console.log('Tasa de Interés:', tasaInteres);  // Verificar la tasa de interés
    }
  }

  calculateCuotaQuincenal(): void {
    const montoSolicitado = this.loanAmount;
    const plazoQuincenal = this.creditForm.get('plazoQuincenal')?.value;
    const tasaInteres = parseFloat(this.creditForm.get('tasaInteres')?.value);

    if (montoSolicitado && plazoQuincenal && tasaInteres) {
      const tasaInteresQuincenal = tasaInteres / 100 / 2;
      const valorCuota = (montoSolicitado * tasaInteresQuincenal) / (1 - Math.pow(1 + tasaInteresQuincenal, -plazoQuincenal));
      
      let roundedCuota = Math.round(valorCuota);
      let formattedCuota = `$ ${roundedCuota.toLocaleString('es-CO')}`;

      this.creditForm.get('valorCuotaQuincenal')?.setValue(formattedCuota);
      this.calculateValorMensual(roundedCuota);
      // console.log('Valor Cuota Quincenal:', formattedCuota);  // Verificar el valor de la cuota
    }
  }

  calculateValorMensual(valorCuotaQuincenal: number): void {
    const valorMensual = valorCuotaQuincenal * 2;
    let formattedMensual = `$ ${valorMensual.toLocaleString('es-CO')}`;
    this.creditForm.get('valorMensual')?.setValue(formattedMensual);
    // console.log('Valor Mensual:', formattedMensual);  // Verificar el valor mensual
  }

  maxLimitValidator(control: AbstractControl): ValidationErrors | null {
    const value = parseFloat(control.value);
    if (this.selectedCreditLine && this.selectedCreditLine.monto > 0 && value > this.selectedCreditLine.monto) {
      return { maxLimitExceeded: true };
    }
    return null;
  }

  maxTermValidator(control: AbstractControl): ValidationErrors | null {
    if (this.selectedCreditLine && control.value > this.selectedCreditLine.plazo) {
      return { maxTermExceeded: true };
    }
    return null;
  }

  procesarCondiciones(textoCondiciones: string): void {
    // Reemplaza los puntos con saltos de línea
    this.creditConditions = textoCondiciones.split('.').map(sentence => sentence.trim()).filter(sentence => sentence.length > 0);
  }

  onSubmit(): void {
    if (this.creditForm.valid) {
      const token = this.loginService.getTokenClaims();

      if(token) {
        const userId = token.userId;

        this.creditForm.value.valorCuotaQuincenal = this.creditForm.value.valorCuotaQuincenal.replace(/[$]/g, ''); // Elimina el símbolo de moneda
        this.creditForm.value.valorCuotaQuincenal = this.creditForm.value.valorCuotaQuincenal.replace(/\./g, ''); // Elimina los separadores de miles
        this.creditForm.value.valorCuotaQuincenal = this.creditForm.value.valorCuotaQuincenal.replace(/,/g, '.'); // Reemplaza la coma decimal por punto
        
        const numeroFloat = parseFloat(this.creditForm.value.valorCuotaQuincenal);

        const creditData = {
          idUsuario: userId,
          montoSolicitado: this.creditForm.value.montoSolicitado,
          plazoQuincenal: this.creditForm.value.plazoQuincenal,
          valorCuotaQuincenal: numeroFloat,
          idLineaCredito: parseFloat(this.creditForm.value.idLineaCredito),
          tasaInteres: parseFloat(this.creditForm.value.tasaInteres)
        };
        console.log('Cuota:', numeroFloat);
        console.log('Formulario enviado:', creditData);
        this.creditsService.create(creditData).subscribe({
          next: () => {
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Solicitud de crédito creada correctamente' });
            setTimeout(() => {
              this.router.navigate(['/auth/user']);
            }, 2000);
          },
          error: (err) => {
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo crear la solicitud de crédito. Por favor, intente otra vez' });
          }
        });
      }
    } else {
      console.log('Formulario inválido');
    }
  }
}