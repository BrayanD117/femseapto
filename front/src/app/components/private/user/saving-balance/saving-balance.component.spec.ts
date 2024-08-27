import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SavingBalanceComponent } from './saving-balance.component';

describe('SavingBalanceComponent', () => {
  let component: SavingBalanceComponent;
  let fixture: ComponentFixture<SavingBalanceComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SavingBalanceComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(SavingBalanceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
