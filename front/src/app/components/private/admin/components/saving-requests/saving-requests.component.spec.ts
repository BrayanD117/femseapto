import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SavingRequestsComponent } from './saving-requests.component';

describe('SavingRequestsComponent', () => {
  let component: SavingRequestsComponent;
  let fixture: ComponentFixture<SavingRequestsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SavingRequestsComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(SavingRequestsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
