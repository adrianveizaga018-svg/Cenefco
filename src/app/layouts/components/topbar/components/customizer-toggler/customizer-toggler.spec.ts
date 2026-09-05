import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CustomizerToggler } from './customizer-toggler';

describe('CustomizerToggler', () => {
  let component: CustomizerToggler;
  let fixture: ComponentFixture<CustomizerToggler>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CustomizerToggler]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CustomizerToggler);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
