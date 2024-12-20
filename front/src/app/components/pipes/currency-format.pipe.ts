import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'currencyFormat',
  standalone: true
})
export class CurrencyFormatPipe implements PipeTransform {
  transform(value: number): string {
    if (value == null) {
      return '';
    }
    const formattedValue = value.toLocaleString('es-ES', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    return `$ ${formattedValue}`;
  }
}
