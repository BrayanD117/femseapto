import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';

import { LoginService } from '../../../../../services/login.service';
import { RequestSavingWithdrawal, RequestSavingWithdrawalService } from '../../../../../services/request-saving-withdrawal.service';
import { SavingLine, SavingLinesService } from '../../../../../services/saving-lines.service';


@Component({
  selector: 'app-request-saving-withdrawal',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, ToastModule,],
  providers: [MessageService],
  templateUrl: './request-saving-withdrawal.component.html',
  styleUrl: './request-saving-withdrawal.component.css'
})
export class RequestSavingWithdrawalComponent implements OnInit {
  savingWdRequestForm: FormGroup;
  userId: number | null = null;
  savingLines: SavingLine[] = [];

  constructor(private fb: FormBuilder, private loginService: LoginService,
    private savingWdRequestService: RequestSavingWithdrawalService, private savingLinesService: SavingLinesService,
    private messageService: MessageService
  ) {
    this.savingWdRequestForm = this.fb.group({
      id: [''],
      idUsuario: ['', Validators.required],
      idLineaAhorro: ['', Validators.required],
      montoRetirar: [0, Validators.required],
      banco: ['', Validators.required],
      numeroCuenta: ['', Validators.required],
      devolucionCaja: ['', Validators.required],
      observaciones: ['', Validators.required],
      continuarAhorro: ['', Validators.required]
    });
  }

  ngOnInit(): void {
    this.getUserIdFromToken();
    this.getAllSavingLines();
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

  getAllSavingLines(): void {
    this.savingLinesService.getAll().subscribe((types: SavingLine[]) => {
      this.savingLines = types;
    });
  }

  submit(): void {
    if (this.savingWdRequestForm.valid) {
      const data: RequestSavingWithdrawal = this.savingWdRequestForm.value;
    }
  }
}
