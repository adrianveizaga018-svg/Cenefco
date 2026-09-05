import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FaqBanner } from './faq-banner';

describe('FaqBanner', () => {
  let component: FaqBanner;
  let fixture: ComponentFixture<FaqBanner>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FaqBanner]
    })
    .compileComponents();

    fixture = TestBed.createComponent(FaqBanner);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
