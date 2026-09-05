import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Pricing1 } from './pricing1';

describe('Pricing1', () => {
  let component: Pricing1;
  let fixture: ComponentFixture<Pricing1>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Pricing1]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Pricing1);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
