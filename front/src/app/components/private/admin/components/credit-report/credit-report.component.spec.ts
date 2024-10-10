import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CreditReportComponent } from './credit-report.component';

describe('CreditReportComponent', () => {
  let component: CreditReportComponent;
  let fixture: ComponentFixture<CreditReportComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CreditReportComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(CreditReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
