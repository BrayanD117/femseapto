import { Component, OnInit, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup, Validators, FormArray, ReactiveFormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { SolicitudAhorroService } from '../../../../../services/request-saving.service';
import { LoginService } from '../../../../../services/login.service';
import { UserService } from '../../../../../services/user.service';
import { CommonModule } from '@angular/common';
import { ButtonModule } from 'primeng/button';
import { CurrencyFormatPipe } from '../../../../pipes/currency-format.pipe';
import { MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';
import { UserInfoValidationService } from '../../../../../services/user-info-validation.service';
import { InfoRequestSavingComponent } from './info-request-saving/info-request-saving.component';
import { FinancialInfoComponent } from '../../components/user-info/components/financial-info/financial-info.component';
import { FinancialInfoService } from '../../../../../services/financial-info.service';
import { NaturalpersonService } from '../../../../../services/naturalperson.service';
import { GenerateSavingRequestComponent } from '../../../admin/components/generate-saving-request/generate-saving-request.component';

@Component({
  selector: 'app-request-saving',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, ToastModule, ButtonModule, CurrencyFormatPipe, InfoRequestSavingComponent, FinancialInfoComponent, GenerateSavingRequestComponent],
  providers: [MessageService],
  templateUrl: './request-saving.component.html',
  styleUrls: ['./request-saving.component.css']
})
export class RequestSavingComponent implements OnInit {
  @ViewChild('generateSavingRequestComponent') generateSavingRequestComponent!: GenerateSavingRequestComponent;

  isLoading = false;

  savingsForm: FormGroup;
  maxSavingsAmount: number = 0;
  savingLines: any[] = [];
  toastDisplayed = false;
  previousTotalLinesAmount = 0;
  tipoAsociado: number = 0;

  displayMessageNatPerson: string = '';
  displayMessageFinancialInfo: string = '';
  isAdditionalDisabled: boolean = false;

  savingRequest = {
    idUsuario: 0,
    id: 0
  };

  

  constructor(
    private fb: FormBuilder,
    private savingsService: SolicitudAhorroService,
    private loginService: LoginService,
    private userService: UserService,
    private messageService: MessageService,
    private router: Router,
    //private userInfoValidationService: UserInfoValidationService,
    private financialInfoService: FinancialInfoService,
    private naturalpersonService: NaturalpersonService
  ) {
    this.savingsForm = this.fb.group({
      totalSavingsAmount: [0, [Validators.required, Validators.min(1)]],
      fortnight: ['', Validators.required],
      month: ['', Validators.required],
      lines: this.fb.array([])
    });
  }

  ngOnInit(): void {
    this.validateUserRecords();

    const token = this.loginService.getTokenClaims();

    if (token) {
      const userId = token.userId;

      this.savingRequest.idUsuario = userId;

      this.userService.getById(userId).subscribe(
        (user: any) => {
          this.tipoAsociado = user.id_tipo_asociado;
          this.addSavingLinesControls();
        },
        (error: any) => {
          console.error('Error fetching user information', error);
        }
      );

      //this.userInfoValidationService.validateUserRecords(userId, this.handleWarning.bind(this));

      this.savingsService.getFinancialInfo(userId).subscribe(
        (data: any) => {
          this.maxSavingsAmount = data.montoMaxAhorro;
          this.savingsForm.get('totalSavingsAmount')?.setValidators([Validators.required, Validators.min(1), Validators.max(this.maxSavingsAmount)]);
        },
        (error: any) => {
          console.error('Error fetching financial information', error);
        }
      );

      this.savingsService.getSavingLines().subscribe(
        (data: any) => {
          this.savingLines = data;
          this.addSavingLinesControls();
        },
        (error: any) => {
          console.error('Error fetching saving lines', error);
        }
      );
    } else {
      console.error('User ID not found');
    }
  }

  validateUserRecords(): void {
    const token = this.loginService.getTokenClaims();

    if (token) {
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
                this.displayMessageNatPerson = 'Por favor, registre la información personal';
                this.messageService.add({ severity: 'warn', summary: 'Aviso', detail: this.displayMessageNatPerson });
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

  addSavingLinesControls(): void {
    const linesArray = this.savingsForm.get('lines') as FormArray;

    if (linesArray.length > 0) {
      return;
    }

    const filteredSavingLines = this.tipoAsociado === 2
      ? this.savingLines.filter(line => line.nombre === 'Ahorro Extraordinario')
      : this.savingLines;
    console.log(this.savingLines);

    filteredSavingLines.forEach((line: any) => {
      linesArray.push(this.fb.group({
        id: [line.id],
        nombre: [line.nombre],
        selected: [false],
        montoAhorrar: [{ value: 0, disabled: true }, [Validators.required, Validators.min(1)]]
      }));
    });
  }

  onLineSelected(index: number): void {
    const linesArray = this.savingsForm.get('lines') as FormArray;
    const selectedControl = linesArray.at(index).get('selected');
    const montoControl = linesArray.at(index).get('montoAhorrar');

    if (selectedControl?.value) {
      montoControl?.enable();
    } else {
      montoControl?.disable();
      montoControl?.setValue(0);
    }

    this.validateTotalSavingsAmount();
  }

  onMontoAhorrarInput(event: Event, index: number): void {
    const inputElement = event.target as HTMLInputElement;
    const numericValue = inputElement.value.replace(/[^0-9]/g, '');
    const montoControl = this.lines.at(index).get('montoAhorrar');
    const value = numericValue ? parseInt(numericValue, 10) : 0;
    montoControl?.setValue(value);
    inputElement.value = `$ ${value.toLocaleString('es-ES')}`;

    this.validateTotalSavingsAmount();
  }

  validateTotalSavingsAmount(): void {
    const totalSavingsAmount = this.savingsForm.get('totalSavingsAmount')?.value;
    const totalLinesAmount = this.lines.controls
      .map(control => control.get('montoAhorrar')?.value || 0)
      .reduce((acc, value) => acc + value, 0);

    if (totalLinesAmount > totalSavingsAmount && totalLinesAmount !== this.previousTotalLinesAmount) {
      if (!this.toastDisplayed) {
        this.toastDisplayed = true;
        this.messageService.add({
          severity: 'warn',
          summary: 'Advertencia',
          detail: `La suma de las líneas de ahorro no puede exceder el monto total de ahorro.`
        });
        setTimeout(() => {
          this.toastDisplayed = false;
        }, 3000);
      }
      this.lines.controls.forEach(control => {
        if (control.get('selected')?.value) {
          control.get('montoAhorrar')?.setErrors({ max: true });
        }
      });
    } else {
      this.lines.controls.forEach(control => {
        control.get('montoAhorrar')?.setErrors(null);
      });
    }

    this.previousTotalLinesAmount = totalLinesAmount;
  }

  onTotalSavingsAmountInput(event: Event): void {
    const inputElement = event.target as HTMLInputElement;
    const numericValue = inputElement.value.replace(/[^0-9]/g, '');
    const value = numericValue ? parseInt(numericValue, 10) : 0;
    this.savingsForm.get('totalSavingsAmount')?.setValue(value);
    inputElement.value = `$ ${value.toLocaleString('es-ES')}`;

    this.validateTotalSavingsAmount();
  }

  resetForm(): void {
    this.savingsForm.reset({
      totalSavingsAmount: 0,
      fortnight: '',
      month: '',
    });

    const linesArray = this.savingsForm.get('lines') as FormArray;
    linesArray.clear();
    this.addSavingLinesControls();
  }

  onSubmit(): void {
    if (this.savingsForm.valid) {
      this.isLoading = true;

      const token = this.loginService.getTokenClaims();

      if (token) {
        const userId = token.userId;
        const selectedLines = this.savingsForm.value.lines
          .filter((line: any) => line.selected)
          .map((line: any) => ({
            idLineaAhorro: line.id,
            montoAhorrar: line.montoAhorrar
          }));

        const savingsData = {
          idUsuario: userId,
          montoTotalAhorrar: this.savingsForm.value.totalSavingsAmount,
          quincena: this.savingsForm.value.fortnight,
          mes: this.savingsForm.value.month,
          lineas: selectedLines
        };

        this.savingsService.createSavingsRequest(savingsData).subscribe({
          next: (response) => {
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Solicitud de ahorro creada exitosamente.' });
            this.resetForm();

            this.savingRequest.id = response.id;

            setTimeout(() => {
              this.generateSavingRequestComponent.generateExcel();
              this.isLoading = false;
              this.router.navigate(['/auth/user']);
            }, 5000);

          },
          error: (err) => {
            console.error('Error creando la solicitud de ahorro', err);
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Ocurrió un error al crear la solicitud de ahorro. Vuelve a intentarlo.' });
            this.isLoading = false;
          }
        });
      } else {
        this.isLoading = false;
        console.error('User ID not found');
        this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Vuelve a iniciar sesión e intentalo de nuevo.' });
      }
    }
  }

  get lines(): FormArray {
    return this.savingsForm.get('lines') as FormArray;
  }
}
