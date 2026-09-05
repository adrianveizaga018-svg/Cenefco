import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MonthlySale } from './monthly-sale';

describe('MonthlySale', () => {
  let component: MonthlySale;
  let fixture: ComponentFixture<MonthlySale>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [MonthlySale]
    })
    .compileComponents();

    fixture = TestBed.createComponent(MonthlySale);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
