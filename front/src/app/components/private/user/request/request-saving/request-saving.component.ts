import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, FormArray, ReactiveFormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { SolicitudAhorroService } from '../../../../../services/request-saving.service';
import { LoginService } from '../../../../../services/login.service';
import { CommonModule } from '@angular/common';
import { ButtonModule } from 'primeng/button';
import { CurrencyFormatPipe } from '../../../../pipes/currency-format.pipe';
import { MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';

@Component({
  selector: 'app-request-saving',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, ToastModule, ButtonModule, CurrencyFormatPipe],
  providers: [MessageService],
  templateUrl: './request-saving.component.html',
  styleUrls: ['./request-saving.component.css']
})
export class RequestSavingComponent implements OnInit {
  savingsForm: FormGroup;
  maxSavingsAmount: number = 0;
  savingLines: any[] = [];

  constructor(
    private fb: FormBuilder,
    private savingsService: SolicitudAhorroService,
    private loginService: LoginService,
    private messageService: MessageService,
    private router: Router
  ) {
    this.savingsForm = this.fb.group({
      totalSavingsAmount: [0, [Validators.required, Validators.min(1)]],
      fortnight: ['', Validators.required],
      month: ['', Validators.required],
      lines: this.fb.array([])
    });
  }

  ngOnInit(): void {
    const token = this.loginService.getTokenClaims();

    if (token) {
      const userId = token.userId;
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

  addSavingLinesControls(): void {
    const linesArray = this.savingsForm.get('lines') as FormArray;
    this.savingLines.forEach((line: any) => {
      linesArray.push(this.fb.group({
        id: [line.id],
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
    montoControl?.setValue(numericValue ? parseInt(numericValue, 10) : 0);
    inputElement.value = `$ ${parseInt(numericValue, 10).toLocaleString('es-ES')}`;

    this.validateTotalSavingsAmount();
  }

  validateTotalSavingsAmount(): void {
    const totalSavingsAmount = this.savingsForm.get('totalSavingsAmount')?.value;
    const totalLinesAmount = this.lines.controls
      .map(control => control.get('montoAhorrar')?.value || 0)
      .reduce((acc, value) => acc + value, 0);

    if (totalLinesAmount > totalSavingsAmount) {
      this.messageService.add({ severity: 'warn', summary: 'Advertencia', detail: 'La suma de las líneas de ahorro no puede exceder el monto total de ahorro.' });
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
  }

  onTotalSavingsAmountInput(event: Event): void {
    const inputElement = event.target as HTMLInputElement;
    const numericValue = inputElement.value.replace(/[^0-9]/g, '');
    this.savingsForm.get('totalSavingsAmount')?.setValue(numericValue ? parseInt(numericValue, 10) : 0);
    inputElement.value = `$ ${parseInt(numericValue, 10).toLocaleString('es-ES')}`;

    this.validateTotalSavingsAmount();
  }

  onSubmit(): void {
    if (this.savingsForm.valid) {
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

        this.savingsService.createSavingsRequest(savingsData).subscribe(
          (response: any) => {
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Solicitud de ahorro creada exitosamente.' });
            setTimeout(() => {
              this.router.navigate(['/auth/user']);
            }, 3000);
          },
          (error: any) => {
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Error al crear la solicitud de ahorro.' });
            console.error('Error creating savings request', error);
          }
        );
      } else {
        console.error('User ID not found');
      }
    }
  }

  get lines(): FormArray {
    return this.savingsForm.get('lines') as FormArray;
  }
}
