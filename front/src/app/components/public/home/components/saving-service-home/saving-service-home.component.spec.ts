import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SavingServiceHomeComponent } from './saving-service-home.component';

describe('SavingServiceHomeComponent', () => {
  let component: SavingServiceHomeComponent;
  let fixture: ComponentFixture<SavingServiceHomeComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SavingServiceHomeComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(SavingServiceHomeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
