import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-saving-service',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './saving-service.component.html',
  styleUrl: './saving-service.component.css'
})
export class SavingServiceComponent {
  selectedSavingLine: string = 'AHORRO VIVIENDA';

  selectSavingLine(line: string) {
    this.selectedSavingLine = line;
  }
}
