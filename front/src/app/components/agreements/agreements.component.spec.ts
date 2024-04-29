import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AgreementsComponent } from './agreements.component';

describe('AgreementsComponent', () => {
  let component: AgreementsComponent;
  let fixture: ComponentFixture<AgreementsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AgreementsComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(AgreementsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
