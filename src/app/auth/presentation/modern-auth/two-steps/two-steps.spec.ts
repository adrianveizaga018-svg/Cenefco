import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TwoSteps } from './two-steps';

describe('TwoSteps', () => {
  let component: TwoSteps;
  let fixture: ComponentFixture<TwoSteps>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [TwoSteps]
    })
    .compileComponents();

    fixture = TestBed.createComponent(TwoSteps);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
