import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SalesRevenueChart } from './sales-revenue-chart';

describe('SalesRevenueChart', () => {
  let component: SalesRevenueChart;
  let fixture: ComponentFixture<SalesRevenueChart>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SalesRevenueChart]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SalesRevenueChart);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
