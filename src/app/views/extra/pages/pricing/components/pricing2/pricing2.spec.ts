import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Pricing2 } from './pricing2';

describe('Pricing2', () => {
  let component: Pricing2;
  let fixture: ComponentFixture<Pricing2>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Pricing2]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Pricing2);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
