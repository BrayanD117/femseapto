import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DividerModule } from 'primeng/divider';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-saving-service',
  standalone: true,
  imports: [CommonModule, DividerModule, RouterLink],
  templateUrl: './saving-service.component.html',
  styleUrl: './saving-service.component.css'
})
export class SavingServiceComponent {
  selectedSavingLine: string = 'AHORRO VIVIENDA';

  selectSavingLine(line: string) {
    this.selectedSavingLine = line;
  }
}
