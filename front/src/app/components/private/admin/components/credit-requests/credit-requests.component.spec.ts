import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CreditRequestsComponent } from './credit-requests.component';

describe('CreditRequestsComponent', () => {
  let component: CreditRequestsComponent;
  let fixture: ComponentFixture<CreditRequestsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CreditRequestsComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(CreditRequestsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
