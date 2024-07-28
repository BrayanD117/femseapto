import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ExecutiveReportsComponent } from './executive-reports.component';

describe('ExecutiveReportsComponent', () => {
  let component: ExecutiveReportsComponent;
  let fixture: ComponentFixture<ExecutiveReportsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ExecutiveReportsComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ExecutiveReportsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
