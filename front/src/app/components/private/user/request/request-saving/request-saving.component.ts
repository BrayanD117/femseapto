import { Component, OnInit } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators, FormArray } from '@angular/forms';
import { Router } from '@angular/router';
import { SolicitudAhorroService } from '../../../../../services/request-saving.service';
import { LoginService } from '../../../../../services/login.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-request-saving',
  standalone: true,
  imports: [ReactiveFormsModule, CommonModule],
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
    this.savingLines.forEach(() => {
      linesArray.push(this.fb.control(false));
    });
  }

  onSubmit(): void {
    if (this.savingsForm.valid) {
      const token = this.loginService.getTokenClaims();

      if (token) {
        const userId = token.userId;
        const selectedLines = this.savingsForm.value.lines
          .map((checked: boolean, i: number) => checked ? this.savingLines[i].id : null)
          .filter((v: any) => v !== null);

        const savingsData = {
          idUsuario: userId,
          ...this.savingsForm.value,
          lines: selectedLines
        };

        this.savingsService.createSavingsRequest(savingsData).subscribe(
          (response: any) => {
            console.log('Savings request created', response);
          },
          (error: any) => {
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
