import { Component } from '@angular/core';
import { PageTitle } from '../../../../common/components/page-title/page-title';
import { Pricing1 } from './components/pricing1/pricing1';
import { Pricing2 } from './components/pricing2/pricing2';

@Component({
  selector: 'app-pricing',
  imports: [ PageTitle, Pricing1, Pricing2 ],
  templateUrl: './pricing.html',
  styles: ``
})
export class Pricing {
  
}
