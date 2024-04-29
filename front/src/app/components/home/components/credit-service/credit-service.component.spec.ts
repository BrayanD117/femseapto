import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CreditServiceComponent } from './credit-service.component';

describe('CreditServiceComponent', () => {
  let component: CreditServiceComponent;
  let fixture: ComponentFixture<CreditServiceComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CreditServiceComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(CreditServiceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
