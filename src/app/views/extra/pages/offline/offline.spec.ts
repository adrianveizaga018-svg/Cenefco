import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Offline } from './offline';

describe('Offline', () => {
  let component: Offline;
  let fixture: ComponentFixture<Offline>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Offline]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Offline);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
