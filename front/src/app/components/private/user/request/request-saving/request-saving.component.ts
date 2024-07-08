import { Component, OnInit } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { SolicitudAhorroService } from '../../../../../services/request-saving.service';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';

@Component({
  selector: 'app-request-saving',
  standalone: true,
  imports: [ReactiveFormsModule, CommonModule, HttpClientModule],
  templateUrl: './request-saving.component.html',
  styleUrls: ['./request-saving.component.css']
})
export class RequestSavingComponent implements OnInit {
  savingsForm: FormGroup;
  maxSavingsAmount: number = 0;

  constructor(
    private fb: FormBuilder,
    private savingsService: SolicitudAhorroService,
    private router: Router
  ) {
    this.savingsForm = this.fb.group({
      totalSavingsAmount: [0, [Validators.required, Validators.min(1)]],
      fortnight: ['', Validators.required],
      month: ['', Validators.required]
    });
  }

  ngOnInit(): void {
    const userId = 1; // Replace with logic to get user ID
    this.savingsService.getFinancialInfo(userId).subscribe(
      (data: any) => {
        this.maxSavingsAmount = data.montoMaxAhorro;
        this.savingsForm.get('totalSavingsAmount')?.setValidators([Validators.required, Validators.min(1), Validators.max(this.maxSavingsAmount)]);
      },
      (error: any) => {
        console.error('Error fetching financial information', error);
      }
    );
  }

  onSubmit(): void {
    if (this.savingsForm.valid) {
      const savingsData = {
        idUsuario: 1, // Replace with logic to get user ID
        ...this.savingsForm.value
      };

      this.savingsService.createSavingsRequest(savingsData).subscribe(
        (response: any) => {
          console.log('Savings request created', response);
          this.router.navigate(['/success']); // Redirect to a success page or similar
        },
        (error: any) => {
          console.error('Error creating savings request', error);
        }
      );
    }
  }
}
