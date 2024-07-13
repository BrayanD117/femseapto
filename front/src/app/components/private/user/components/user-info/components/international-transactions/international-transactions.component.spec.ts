import { ComponentFixture, TestBed } from '@angular/core/testing';

import { InternationalTransactionsComponent } from './international-transactions.component';

describe('InternationalTransactionsComponent', () => {
  let component: InternationalTransactionsComponent;
  let fixture: ComponentFixture<InternationalTransactionsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [InternationalTransactionsComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(InternationalTransactionsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
