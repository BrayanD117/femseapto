import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { LoginService } from '../../../../../../../services/login.service';
import { FinancialInfoService } from '../../../../../../../services/financial-info.service';
import { CommonModule } from '@angular/common';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';

@Component({
  selector: 'app-financial-info',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, ToastModule],
  providers: [MessageService],
  templateUrl: './financial-info.component.html',
  styleUrls: ['./financial-info.component.css']
})
export class FinancialInfoComponent implements OnInit {
  financialForm: FormGroup;

  constructor(
    private fb: FormBuilder,
    private financialInfoService: FinancialInfoService,
    private loginService: LoginService,
    private messageService: MessageService
  ) {
    this.financialForm = this.fb.group({
      nombreBanco: ['', Validators.required],
      idTipoCuentaBanc: ['', Validators.required],
      numeroCuentaBanc: ['', Validators.required],
      ingresosMensuales: ['', Validators.required],
      primaProductividad: [''],
      otrosIngresosMensuales: [''],
      conceptoOtrosIngresosMens: [''],
      totalIngresosMensuales: ['', Validators.required],
      egresosMensuales: ['', Validators.required],
      obligacionFinanciera: [''],
      otrosEgresosMensuales: [''],
      totalEgresosMensuales: ['', Validators.required],
      totalActivos: ['', Validators.required],
      totalPasivos: ['', Validators.required],
      totalPatrimonio: ['', Validators.required],
      montoMaxAhorro: ['', Validators.required]
    });
  }

  ngOnInit(): void {
    const token = this.loginService.getTokenClaims();

    if (token) {
      this.financialInfoService.getFinancialInfo(token.userId).subscribe(financialInfo => {
        this.financialForm.patchValue(financialInfo);
      });
    }
  }

  onSubmit(): void {
    if (this.financialForm.valid) {
      const token = this.loginService.getTokenClaims();
      this.financialInfoService.updateFinancialInfo(token.userId, this.financialForm.value).subscribe({
        next: () => {
          this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Información financiera actualizada correctamente' });
        },
        error: (err) => {
          this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo actualizar la información financiera' });
        }
      });
    }
  }
}
