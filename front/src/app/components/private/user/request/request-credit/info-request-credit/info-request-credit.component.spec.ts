import { ComponentFixture, TestBed } from '@angular/core/testing';

import { InfoRequestCreditComponent } from './info-request-credit.component';

describe('InfoRequestCreditComponent', () => {
  let component: InfoRequestCreditComponent;
  let fixture: ComponentFixture<InfoRequestCreditComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [InfoRequestCreditComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(InfoRequestCreditComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
