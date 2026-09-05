import { Component, Input } from '@angular/core';
import { NgIcon } from '@ng-icons/core';
import { DashboardResumen } from '../../../../domain/models/dashboard.model';

@Component({
  selector: 'app-monthly-sale',
  imports: [NgIcon],
  templateUrl: './monthly-sale.html',
  styles: ``
})
export class MonthlySale {
  @Input() resumen: DashboardResumen | null = null;
}
