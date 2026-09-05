import { Component } from '@angular/core';
import { NgIcon } from '@ng-icons/core';

type Plan = {
  name: string;
  price: number;
  period: string;
  color: string; 
  textColor: string;
  features: string[][];
};

@Component({
  selector: 'app-pricing2',
  imports: [ NgIcon ],
  templateUrl: './pricing2.html',
  styles: ``
})

export class Pricing2 {
  plans: Plan[] = [
    {
      name: 'Personal Plan',
      price: 29.99,
      period: 'Per Month',
      color: 'bg-primary/10',
      textColor: 'text-primary',
      features: [
        [
          '<b>3</b> Projects',
          '<b>299</b> Customers',
          'Scalable Bandwidth',
          '<b>0</b> No Team Account'
        ],
        [
          '<b>3</b> Projects',
          '<b>299</b> Customers'
        ]
      ]
    },
    {
      name: 'Enterprise Plan',
      price: 49.99,
      period: 'Per Month',
      color: 'bg-purple-500/20',
      textColor: 'text-purple-600',
      features: [
        [
          '<b>3</b> Projects',
          '<b>299</b> Customers',
          'Scalable Bandwidth',
          '<b>0</b> No Team Account'
        ],
        [
          '<b>3</b> Projects',
          '<b>299</b> Customers',
          'Scalable Bandwidth',
          '<b>0</b> No Team Account'
        ]
      ]
    }
  ];
}
