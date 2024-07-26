import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ManageExecutiveComponent } from './manage-executive.component';

describe('ManageExecutiveComponent', () => {
  let component: ManageExecutiveComponent;
  let fixture: ComponentFixture<ManageExecutiveComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ManageExecutiveComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ManageExecutiveComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
