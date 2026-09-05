import { Component } from '@angular/core';
import { NgIcon } from '@ng-icons/core';

type PricingPlan = {
  name: string;
  icon: string;
  color: string;
  description: string;
  price: number;
  period: string;
  badge?: string; 
  features: {
    text: string;
    available: boolean;
  }[];
};

@Component({
  selector: 'app-pricing1',
  imports: [NgIcon],
  templateUrl: './pricing1.html',
  styles: ``
})

export class Pricing1 {
  plans: PricingPlan[] = [
    {
      name: 'Lite',
      icon: 'lucideGoal',
      color: 'text-success',
      description: 'Perfect plan for Lite',
      price: 9.99,
      period: 'Monthly',
      features: [
        { text: '<b>3</b> Projects', available: true },
        { text: '<b>299</b> Customers', available: true },
        { text: 'Scalable Bandwidth', available: true },
        { text: '<b>0</b> No Team Account', available: true },
        { text: 'In app messaging', available: false },
        { text: '5 FTP Login', available: false },
        { text: 'Detailed Analytics', available: false },
        { text: 'Custom Domain', available: false },
        { text: 'Smart Workflow', available: false },
        { text: 'IQ test', available: false }
      ]
    },
    {
      name: 'Professional',
      icon: 'lucideGraduationCap',
      color: 'text-secondary',
      description: 'For users who want to do more.',
      price: 29.99,
      period: 'Monthly',
      features: [
        { text: '<b>3</b> Projects', available: true },
        { text: '<b>299</b> Customers', available: true },
        { text: 'Scalable Bandwidth', available: true },
        { text: '<b>3</b> No Team Account', available: true },
        { text: 'In app messaging', available: true },
        { text: '5 FTP Login', available: false },
        { text: 'Detailed Analytics', available: false },
        { text: 'Custom Domain', available: false },
        { text: 'Smart Workflow', available: false },
        { text: 'IQ test', available: false }
      ]
    },
    {
      name: 'Enterprise',
      icon: 'lucideCrown',
      color: 'text-primary',
      description: 'Run your company on your teams',
      price: 39.99,
      period: 'Monthly',
      badge: '25% Sale',
      features: [
        { text: '<b>3</b> Projects', available: true },
        { text: '<b>299</b> Customers', available: true },
        { text: 'Scalable Bandwidth', available: true },
        { text: '<b>3</b> No Team Account', available: true },
        { text: 'In app messaging', available: true },
        { text: '5 FTP Login', available: true },
        { text: 'Detailed Analytics', available: true },
        { text: 'Custom Domain', available: false },
        { text: 'Smart Workflow', available: false },
        { text: 'IQ test', available: false }
      ]
    },
    {
      name: 'Unlimited',
      icon: 'lucideLuggage',
      color: 'text-danger',
      description: 'Your entire team in one place',
      price: 49.99,
      period: 'Monthly',
      features: [
        { text: '<b>3</b> Projects', available: true },
        { text: '<b>299</b> Customers', available: true },
        { text: 'Scalable Bandwidth', available: true },
        { text: '<b>3</b> No Team Account', available: true },
        { text: 'In app messaging', available: true },
        { text: '5 FTP Login', available: true },
        { text: 'Detailed Analytics', available: true },
        { text: 'Custom Domain', available: true },
        { text: 'Smart Workflow', available: true },
        { text: 'IQ test', available: true }
      ]
    }
  ];
}
