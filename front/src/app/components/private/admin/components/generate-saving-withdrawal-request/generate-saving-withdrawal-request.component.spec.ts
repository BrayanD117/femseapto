import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GenerateSavingWithdrawalRequestComponent } from './generate-saving-withdrawal-request.component';

describe('GenerateSavingWithdrawalRequestComponent', () => {
  let component: GenerateSavingWithdrawalRequestComponent;
  let fixture: ComponentFixture<GenerateSavingWithdrawalRequestComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [GenerateSavingWithdrawalRequestComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(GenerateSavingWithdrawalRequestComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
