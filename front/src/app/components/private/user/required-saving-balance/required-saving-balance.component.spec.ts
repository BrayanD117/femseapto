import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RequiredSavingBalanceComponent } from './required-saving-balance.component';

describe('RequiredSavingBalanceComponent', () => {
  let component: RequiredSavingBalanceComponent;
  let fixture: ComponentFixture<RequiredSavingBalanceComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RequiredSavingBalanceComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(RequiredSavingBalanceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
