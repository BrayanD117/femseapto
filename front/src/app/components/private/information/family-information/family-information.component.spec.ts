import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FamilyInformationComponent } from './family-information.component';

describe('FamilyInformationComponent', () => {
  let component: FamilyInformationComponent;
  let fixture: ComponentFixture<FamilyInformationComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FamilyInformationComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(FamilyInformationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
