import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AgreementTypesListComponent } from './agreement-types-list.component';

describe('AgreementTypesListComponent', () => {
  let component: AgreementTypesListComponent;
  let fixture: ComponentFixture<AgreementTypesListComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AgreementTypesListComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(AgreementTypesListComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
