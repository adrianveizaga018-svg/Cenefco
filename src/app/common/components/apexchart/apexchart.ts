
import { Component, Input, OnInit } from '@angular/core';
import { ApexOptions, NgApexchartsModule } from 'ng-apexcharts';

@Component({
  selector: 'app-apexchart',
  imports: [NgApexchartsModule],
  templateUrl: './apexchart.html',
  styles: ``
})
export class Apexchart implements OnInit {
 @Input() getOptions!: () => ApexOptions;
  options!: ApexOptions;

 ngOnInit(): void {
    this.options = this.getOptions();
  }
}
