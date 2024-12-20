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
import { InfoRequestCreditComponent } from './info-request-credit/info-request-credit.component';

//import { UserInfoValidationService } from '../../../../../services/user-info-validation.service';
import { FinancialInfoService } from '../../../../../services/financial-info.service';
import { RecommendationService } from '../../../../../services/recommendation.service';
import { FamilyService } from '../../../../../services/family.service';
import { NaturalpersonService } from '../../../../../services/naturalperson.service';


@Component({
  selector: 'app-request-credit',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, ToastModule],
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

  displayMessageNatPerson: string = '';
  displayMessageFinancialInfo: string = '';
  displayMessagePersonalRecommend: string = '';
  displayMessageFamRecommend: string = '';
  isAdditionalDisabled: boolean = false;

  pdfFile: File | null = null;
  isSubmitting: boolean = false;

  constructor(
    private fb: FormBuilder,
    private creditsService: RequestCreditService,
    private loginService: LoginService,
    private messageService: MessageService,
    private router: Router,
    private lineasCreditoService: LineasCreditoService,
    //private userInfoValidationService: UserInfoValidationService,
    private financialInfoService: FinancialInfoService,
    private familyService: FamilyService,
    private recommendationService: RecommendationService,
    private naturalpersonService: NaturalpersonService  
  ) {
    this.creditForm = this.fb.group({
      montoSolicitado: [0, [Validators.required, Validators.min(1), this.maxLimitValidator.bind(this)]],
      plazoQuincenal: ['', [Validators.required, this.maxTermValidator.bind(this)]],
      valorCuotaQuincenal: [{ value: 0, disabled: false }, [Validators.required, Validators.min(1)]],
      idLineaCredito: ['', Validators.required],
      tasaInteres: [{ value: '', disabled: false }, Validators.required],
      valorMensual: [{ value: '', disabled: true }],
      rutaDocumento: [null, Validators.required]
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

    if (token) {
        // Inicialmente, deshabilitamos la solicitud de crédito hasta que todas las validaciones pasen
        let allValid = true;

        this.financialInfoService.validate(token.userId).subscribe(response => {
            if (!response) {
                this.displayMessageFinancialInfo = 'Por favor, registre la información financiera';
                this.messageService.add({ severity: 'warn', summary: 'Aviso', detail: this.displayMessageFinancialInfo });
                allValid = false;
            }
            this.checkValidationComplete(allValid);
        });

        this.naturalpersonService.validate(token.userId).subscribe(response => {
            if (!response) {
                this.displayMessageNatPerson = 'Por favor, registre la información general';
                this.messageService.add({ severity: 'warn', summary: 'Aviso', detail: this.displayMessageNatPerson });
                allValid = false;
            }
            this.checkValidationComplete(allValid);
        });

        this.recommendationService.validatePersonal(token.userId).subscribe(response => {
            if (!response) {
                this.displayMessagePersonalRecommend = 'Por favor, registre al menos una referencia personal';
                this.messageService.add({ severity: 'warn', summary: 'Aviso', detail: this.displayMessagePersonalRecommend });
                allValid = false;
            }
            this.checkValidationComplete(allValid);
        });

        this.recommendationService.validateFamiliar(token.userId).subscribe(response => {
            if (!response) {
                this.displayMessageFamRecommend = 'Por favor, registre al menos una referencia familiar';
                this.messageService.add({ severity: 'warn', summary: 'Aviso', detail: this.displayMessageFamRecommend });
                allValid = false;
            }
            this.checkValidationComplete(allValid);
        });
    }   
}

    checkValidationComplete(allValid: boolean): void {
        this.isAdditionalDisabled = !allValid;
    }

  /*private handleWarning(detail: string): void {
    this.messageService.add({ severity: 'warn', summary: 'Aviso', detail });
    setTimeout(() => {
      this.router.navigate(['/auth/user/information']);
    }, 5000);
  }*/

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
    }
  }

  calculateValorMensual(valorCuotaQuincenal: number): void {
    const valorMensual = valorCuotaQuincenal * 2;
    let formattedMensual = `$ ${valorMensual.toLocaleString('es-CO')}`;
    this.creditForm.get('valorMensual')?.setValue(formattedMensual);
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
    this.creditConditions = textoCondiciones.split('.').map(sentence => sentence.trim()).filter(sentence => sentence.length > 0);
  }

  onFileChange(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      const file = input.files[0];
      if (file.type === 'application/pdf') {
        this.pdfFile = file;
        this.creditForm.patchValue({ rutaDocumento: this.pdfFile });
        this.creditForm.get('rutaDocumento')?.updateValueAndValidity();
      } else {
        this.messageService.add({ 
          severity: 'error', 
          summary: 'Archivo no válido', 
          detail: 'Solo se permiten archivos en formato PDF' 
        });
        input.value = '';
      }
    }
  }
  

  onSubmit(): void {
    if (this.creditForm.valid) {
      this.isSubmitting = true;
      const token = this.loginService.getTokenClaims();

      if(token) {
        const userId = token.userId;
        const formData = new FormData();

        //this.creditForm.value.valorCuotaQuincenal = this.creditForm.value.valorCuotaQuincenal.replace(/[$]/g, ''); // Elimina el símbolo de moneda
        //this.creditForm.value.valorCuotaQuincenal = this.creditForm.value.valorCuotaQuincenal.replace(/\./g, ''); // Elimina los separadores de miles
        //this.creditForm.value.valorCuotaQuincenal = this.creditForm.value.valorCuotaQuincenal.replace(/,/g, '.'); // Reemplaza la coma decimal por punto
        
        //const numeroFloat = parseFloat(this.creditForm.value.valorCuotaQuincenal);

        let valorCuotaQuincenal = this.creditForm.value.valorCuotaQuincenal
        .replace(/[$]/g, '') // Elimina el símbolo de moneda
        .replace(/\./g, '') // Elimina los separadores de miles
        .replace(/,/g, '.'); // Reemplaza la coma decimal por punto
        const valorCuotaFloat = parseFloat(valorCuotaQuincenal);

        formData.append('idUsuario', userId);
        formData.append('montoSolicitado', this.creditForm.value.montoSolicitado);
        formData.append('plazoQuincenal', this.creditForm.value.plazoQuincenal);
        formData.append('valorCuotaQuincenal', valorCuotaFloat.toString());
        formData.append('idLineaCredito', this.creditForm.value.idLineaCredito);
        formData.append('tasaInteres', this.creditForm.value.tasaInteres);

        if (this.pdfFile) {
          formData.append('rutaDocumento', this.pdfFile, this.pdfFile.name);
        } else {
          this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Debe subir la copia del documento' });
          this.isSubmitting = false;
          return;
        }

        this.creditsService.create(formData).subscribe({
          next: () => {
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Solicitud de crédito creada correctamente' });
            setTimeout(() => {
              this.isSubmitting = false;
              this.router.navigate(['/auth/user']);
            }, 2000);
          },
          error: (err) => {
            this.isSubmitting = false;
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo crear la solicitud de crédito. Por favor, intente otra vez' });
          }
        });
      }
    }
  }
}