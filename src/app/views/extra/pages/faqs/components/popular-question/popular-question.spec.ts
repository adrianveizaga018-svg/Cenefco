import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PopularQuestion } from './popular-question';

describe('PopularQuestion', () => {
  let component: PopularQuestion;
  let fixture: ComponentFixture<PopularQuestion>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PopularQuestion]
    })
    .compileComponents();

    fixture = TestBed.createComponent(PopularQuestion);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
