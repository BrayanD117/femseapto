import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { MessageService } from 'primeng/api';
import { UserService } from '../../../../../../services/user.service';
import { Workbook } from 'exceljs';
import * as fs from 'file-saver';
import { MessagesModule } from 'primeng/messages';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-users-search',
  standalone: true,
  imports: [CommonModule, FormsModule, MessagesModule],
  providers: [MessageService],
  templateUrl: './users-search.component.html',
  styleUrl: './users-search.component.css'
})
export class UsersSearchComponent {
  documentNumber: string = '';
  user: any = null;
  isLoading: boolean = false;
  showNotFoundMessage: boolean = false;

  constructor(
    private userService: UserService,
    private messageService: MessageService
  ) {}

  getUserInfo() {
    if(!this.documentNumber) {
      return;
    }

    this.user = null;
    this.showNotFoundMessage = false;

    this.isLoading = true;

    this.userService.getUserInfoByDocumentNumber(this.documentNumber).subscribe({
      next: (response: any) => {
        this.isLoading = false;
        if (response) {
          this.user = response;
        } else {
          this.showNotFoundMessage = true;
        }
      },
      error: (error) => {
        this.isLoading = false;
        this.showNotFoundMessage = true;
        console.error(error);
      },
    });
  }

  async generateExcelForUser() {
    if (!this.user) {
      this.messageService.add({
        severity: 'warn',
        summary: 'Sin datos',
        detail: 'No hay información del usuario para generar el Excel',
      });
      return;
    }

    this.isLoading = true;

    const workbook = new Workbook();
    await fetch('/assets/FORMATO_DE_VINCULACION.xlsx')
      .then((response) => response.arrayBuffer())
      .then((data) => workbook.xlsx.load(data));

    const worksheet = workbook.getWorksheet(1);

    if (worksheet) {
      const user = this.user;
      console.log("user info", this.user);

      // Datos personales
      worksheet.getCell('C6').value = user.primerApellido || '';
      worksheet.getCell('L6').value = user.segundoApellido || '';
      worksheet.getCell('S6').value = `${user.primerNombre || ''} ${user.segundoNombre || ''}`.trim();
      worksheet.getCell('D7').value = user.numeroDocumento ? Number(user.numeroDocumento) : '';
      worksheet.getCell('L7').value = user.fechaExpedicionDoc || '';
      worksheet.getCell('Q7').value = user.nombreMpioExpDoc || '';
      worksheet.getCell('AA7').value = user.fechaNacimiento || '';
      worksheet.getCell('AH7').value = user.nombrePaisNacimiento || 'N/A';
      worksheet.getCell('A8').value = `GENERO: ${user.generoNombre} `|| 'N/A';
      worksheet.getCell('B10').value = user.estadoCivil || '';
      worksheet.getCell('L10').value = user.nombreNivelEducativo || '';
      worksheet.getCell('W10').value = user.tieneHijos || '';
      worksheet.getCell('AH10').value = user.numeroHijos || 0;
      worksheet.getCell('B11').value = user.correoElectronico || '';
      worksheet.getCell('N11').value = Number(user.telefono) || '';
      worksheet.getCell('AA11').value = Number(user.celular) || '';
      worksheet.getCell('B12').value = user.nombrePaisNacimiento || '';
      worksheet.getCell('H12').value = user.profesion || '';
      worksheet.getCell('Y12').value = user.ocupacionOficio || '';
      worksheet.getCell('B14').value = user.tipoEmpresa || '';
      worksheet.getCell('R14').value = user.cargoOcupa || '';

      // Dirección y datos de vivienda
      worksheet.getCell('E8').value = user.direccionResidencia || '';
      worksheet.getCell('R8').value = user.nombreMpioResidencia || '';
      worksheet.getCell('AC8').value = user.nombreDptoResidencia || 'N/A';
      worksheet.getCell('B9').value = user.antiguedadVivienda || '';
      worksheet.getCell('H9').value = user.personasACargo || '';
      worksheet.getCell('K9').value = user.zonaGeografica || '';
      worksheet.getCell('Q9').value = user.tipoVivienda || '';
      worksheet.getCell('AH9').value = user.estrato || '';

      // Datos empresa
      worksheet.getCell('B13').value = user.actividadEconomicaEmpresa || '';
      worksheet.getCell('K13').value = user.ciiuEmpresa || '';
      worksheet.getCell('O13').value = user.nombreEmpresaLabor || '';
      worksheet.getCell('AG13').value = user.nitEmpresa || '';
      worksheet.getCell('W15').value = Number(user.telefonoEmpresa) || '';
      worksheet.getCell('B15').value = user.direccionEmpresa || '';
      worksheet.getCell('L15').value = user.municipioEmpresa || '';

      // Datos financieros
      worksheet.getCell('G34').value = (Number(user.ingresosMensuales) + Number(user.primaProductividad)) || 0;
      worksheet.getCell('X34').value = (Number(user.egresosMensuales) + Number(user.obligacionFinanciera)) || 0;
      worksheet.getCell('G35').value = Number(user.otrosIngresosMensuales) || 0;
      worksheet.getCell('X35').value = Number(user.otrosEgresosMensuales) || 0;
      worksheet.getCell('G36').value = Number(user.totalIngresosMensuales) || 0;
      worksheet.getCell('X36').value = Number(user.totalEgresosMensuales) || 0;
      worksheet.getCell('G37').value = user.conceptoOtrosIngresos || '';
      worksheet.getCell('X37').value = Number(user.totalActivos) || 0;
      worksheet.getCell('X38').value = Number(user.totalPasivos) || 0;
      worksheet.getCell('X39').value = Number(user.totalPatrimonio) || 0;

      const familiares = Array.isArray(user.familiares) ? user.familiares.slice(0, 4) : [];
      const startRow = 42;
      familiares.forEach((familiar: any, index: number) => {
        const row = startRow + index;
        worksheet.getCell(`A${row}`).value = familiar.nombreCompleto || '';
        worksheet.getCell(`H${row}`).value = familiar.parentesco || '';
        worksheet.getCell(`K${row}`).value = familiar.numeroDocumento ? Number(familiar.numeroDocumento) : '';
        worksheet.getCell(`M${row}`).value = familiar.tipoDocumento || '';
        worksheet.getCell(`N${row}`).value = familiar.genero || '';
        worksheet.getCell(`O${row}`).value = familiar.fechaNacimiento || '';
        worksheet.getCell(`V${row}`).value = familiar.nivelEducativo || '';
        worksheet.getCell(`AB${row}`).value = familiar.trabaja ? 'Sí' : 'No';
        worksheet.getCell(`AE${row}`).value = familiar.celular ? Number(familiar.celular) : '';
      });

      const referencias = Array.isArray(user.referencias) ? user.referencias.slice(0, 2) : [];
      const startRowRef = 48;
      referencias.forEach((referencia: any, index: number) => {
        const row = startRowRef + index;
        worksheet.getCell(`A${row}`).value = referencia.nombreRazonSocial || '';
        worksheet.getCell(`K${row}`).value = referencia.abreviatura || '';
        worksheet.getCell(`L${row}`).value = referencia.direccion || '';
        worksheet.getCell(`V${row}`).value = referencia.ciudad || '';
        worksheet.getCell(`AD${row}`).value = referencia.telefono ? Number(referencia.telefono) : '';
      });

      worksheet.getCell('G51').value = user.transaccionesMonedaExtranjera || 'N/A';
      worksheet.getCell('L51').value = user.monedaTransaccion || 'N/A';
      worksheet.getCell('Y51').value = user.otrasOperaciones || 'N/A';
      worksheet.getCell('G52').value = user.cuentaExtranjera || 'N/A';
      worksheet.getCell('L52').value = user.bancoExtranjera || 'N/A';
      worksheet.getCell('P52').value = user.numeroCuentaExtranjera || 'N/A';
      worksheet.getCell('X52').value = user.monedaCuenta || 'N/A';
      worksheet.getCell('AD52').value = user.ciudadCuenta || 'N/A';
      worksheet.getCell('AH52').value = user.paisCuenta || 'N/A';

      worksheet.getCell('A55').value = user.poderPublico || 'N/A';
      worksheet.getCell('E55').value = user.manejaRecursosPublicos || 'N/A';
      worksheet.getCell('K55').value = user.reconocimientoPublico || 'N/A';
      worksheet.getCell('R55').value = user.funcionesPublicas || 'N/A';
      worksheet.getCell('A57').value = user.funcionPublicoExtranjero || 'N/A';
      worksheet.getCell('K57').value = user.familiarFuncionPublico || 'N/A';
      worksheet.getCell('R57').value = user.socioFuncionPublico || 'N/A';
    }

    const buffer = await workbook.xlsx.writeBuffer();
    const fileName = `Formato_Vinculacion_${this.user.numeroDocumento}.xlsx`;
    fs.saveAs(new Blob([buffer]), fileName);

    this.isLoading = false;
    this.messageService.add({
      severity: 'success',
      summary: 'Excel generado',
      detail: 'El archivo Excel ha sido generado con éxito.',
    });
  }
}