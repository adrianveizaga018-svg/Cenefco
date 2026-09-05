import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SellingProduct } from './selling-product';

describe('SellingProduct', () => {
  let component: SellingProduct;
  let fixture: ComponentFixture<SellingProduct>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SellingProduct]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SellingProduct);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
