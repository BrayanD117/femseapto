import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SavingServiceComponent } from './saving-service.component';

describe('SavingServiceComponent', () => {
  let component: SavingServiceComponent;
  let fixture: ComponentFixture<SavingServiceComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SavingServiceComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(SavingServiceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
