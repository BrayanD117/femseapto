import { Component, OnInit } from '@angular/core';
import { TableModule } from 'primeng/table';
import { CommonModule } from '@angular/common';

import { SolicitudAhorroService } from '../../../../services/request-saving.service';
import { LoginService } from '../../../../services/login.service';

import { GenerateSavingRequestComponent } from '../../admin/components/generate-saving-request/generate-saving-request.component';

@Component({
  selector: 'app-saving-request-history',
  standalone: true,
  imports: [CommonModule, TableModule, GenerateSavingRequestComponent],
  templateUrl: './saving-request-history.component.html',
  styleUrl: './saving-request-history.component.css'
})
export class SavingRequestHistoryComponent implements OnInit {

  savingRequests: any[] = [];

  constructor(private savingRequestService: SolicitudAhorroService,
    private loginService: LoginService
  ) {}

  ngOnInit() {
    const token = this.loginService.getTokenClaims();
  
    this.savingRequestService.getByUserId(token.userId).subscribe((data: any[]) => {
      const requests = data.map((savingRequest: any) => ({
        ...savingRequest,
        montoTotalAhorrar: this.formatNumber(savingRequest.montoTotalAhorrar)
      }));
  
      this.savingRequests = requests;
      console.log(this.savingRequests);
    });
  }  

  formatNumber(value: string): string {
    const numericValue = parseFloat(value.replace(',', '.')); // Asegura que el formato de número sea válido
    return new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP',
      minimumFractionDigits: 2
    }).format(numericValue);
  }

}
