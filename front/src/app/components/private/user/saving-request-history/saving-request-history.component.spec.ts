import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SavingRequestHistoryComponent } from './saving-request-history.component';

describe('SavingRequestHistoryComponent', () => {
  let component: SavingRequestHistoryComponent;
  let fixture: ComponentFixture<SavingRequestHistoryComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SavingRequestHistoryComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(SavingRequestHistoryComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
