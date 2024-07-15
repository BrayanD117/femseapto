import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GenerateCreditRequestComponent } from './generate-credit-request.component';

describe('GenerateCreditRequestComponent', () => {
  let component: GenerateCreditRequestComponent;
  let fixture: ComponentFixture<GenerateCreditRequestComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [GenerateCreditRequestComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(GenerateCreditRequestComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
