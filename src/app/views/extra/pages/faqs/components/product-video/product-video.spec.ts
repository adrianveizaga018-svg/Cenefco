import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ProductVideo } from './product-video';

describe('ProductVideo', () => {
  let component: ProductVideo;
  let fixture: ComponentFixture<ProductVideo>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ProductVideo]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ProductVideo);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
