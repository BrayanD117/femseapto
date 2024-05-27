import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AgreementsCarouselComponent } from './agreements-carousel.component';

describe('AgreementsCarouselComponent', () => {
  let component: AgreementsCarouselComponent;
  let fixture: ComponentFixture<AgreementsCarouselComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AgreementsCarouselComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(AgreementsCarouselComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
