import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FinancialInformationComponent } from './financial-information.component';

describe('FinancialInformationComponent', () => {
  let component: FinancialInformationComponent;
  let fixture: ComponentFixture<FinancialInformationComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FinancialInformationComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(FinancialInformationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
