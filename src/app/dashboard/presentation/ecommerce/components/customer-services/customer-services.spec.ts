import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CustomerServices } from './customer-services';

describe('CustomerServices', () => {
  let component: CustomerServices;
  let fixture: ComponentFixture<CustomerServices>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CustomerServices]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CustomerServices);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
