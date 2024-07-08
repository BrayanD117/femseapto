import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, FormArray, ReactiveFormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { SolicitudAhorroService } from '../../../../../services/request-saving.service';
import { LoginService } from '../../../../../services/login.service';
import { CommonModule } from '@angular/common';
import { DialogModule } from 'primeng/dialog';
import { ButtonModule } from 'primeng/button';

@Component({
  selector: 'app-request-saving',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, DialogModule, ButtonModule],
  templateUrl: './request-saving.component.html',
  styleUrls: ['./request-saving.component.css']
})
export class RequestSavingComponent implements OnInit {
  savingsForm: FormGroup;
  maxSavingsAmount: number = 0;
  savingLines: any[] = [];
  showModal = false;
  modalMessage = '';

  constructor(
    private fb: FormBuilder,
    private savingsService: SolicitudAhorroService,
    private loginService: LoginService,
    private router: Router
  ) {
    this.savingsForm = this.fb.group({
      totalSavingsAmount: [0, [Validators.required, Validators.min(1)]],
      fortnight: ['', Validators.required],
      month: ['', Validators.required],
      lines: this.fb.array([])  // FormArray for saving lines
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
        selected: [false]
      }));
    });
  }

  onSubmit(): void {
    if (this.savingsForm.valid) {
      const token = this.loginService.getTokenClaims();

      if (token) {
        const userId = token.userId;
        const selectedLines = this.savingsForm.value.lines
          .filter((line: any) => line.selected)
          .map((line: any) => ({
            idLineaAhorro: line.id
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
            this.modalMessage = 'Solicitud de ahorro creada exitosamente.';
            this.showModal = true;
            setTimeout(() => {
              this.router.navigate(['/auth/user']);
            }, 3000);
          },
          (error: any) => {
            this.modalMessage = 'Error al crear la solicitud de ahorro.';
            this.showModal = true;
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

  closeModal(): void {
    this.showModal = false;
  }
}
