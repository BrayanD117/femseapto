import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CreditBalanceComponent } from './credit-balance.component';

describe('CreditBalanceComponent', () => {
  let component: CreditBalanceComponent;
  let fixture: ComponentFixture<CreditBalanceComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CreditBalanceComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(CreditBalanceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
