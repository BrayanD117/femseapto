import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RequestSavingWithdrawalComponent } from './request-saving-withdrawal.component';

describe('RequestSavingWithdrawalComponent', () => {
  let component: RequestSavingWithdrawalComponent;
  let fixture: ComponentFixture<RequestSavingWithdrawalComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RequestSavingWithdrawalComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(RequestSavingWithdrawalComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
