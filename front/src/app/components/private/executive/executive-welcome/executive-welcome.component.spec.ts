import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ExecutiveWelcomeComponent } from './executive-welcome.component';

describe('ExecutiveWelcomeComponent', () => {
  let component: ExecutiveWelcomeComponent;
  let fixture: ComponentFixture<ExecutiveWelcomeComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ExecutiveWelcomeComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ExecutiveWelcomeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
