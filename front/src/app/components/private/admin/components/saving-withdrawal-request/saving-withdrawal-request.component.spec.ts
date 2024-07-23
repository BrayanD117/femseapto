import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SavingWithdrawalRequestComponent } from './saving-withdrawal-request.component';

describe('SavingWithdrawalRequestComponent', () => {
  let component: SavingWithdrawalRequestComponent;
  let fixture: ComponentFixture<SavingWithdrawalRequestComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SavingWithdrawalRequestComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(SavingWithdrawalRequestComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
