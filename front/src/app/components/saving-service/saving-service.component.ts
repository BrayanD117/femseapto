import { Component, ElementRef, ViewChild  } from '@angular/core';
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

  @ViewChild('card') card: ElementRef | undefined;

  selectSavingLine(line: string) {
    this.selectedSavingLine = line;
    setTimeout(() => {
      if (this.card) {
        this.card.nativeElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }, 100); // Espera para asegurarte de que la vista se ha actualizado
  }
}
