import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CreditServiceHomeComponent } from './credit-service-home.component';

describe('CreditServiceHomeComponent', () => {
  let component: CreditServiceHomeComponent;
  let fixture: ComponentFixture<CreditServiceHomeComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CreditServiceHomeComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(CreditServiceHomeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
