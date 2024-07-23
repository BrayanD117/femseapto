import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GenerateSavingRequestComponent } from './generate-saving-request.component';

describe('GenerateSavingRequestComponent', () => {
  let component: GenerateSavingRequestComponent;
  let fixture: ComponentFixture<GenerateSavingRequestComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [GenerateSavingRequestComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(GenerateSavingRequestComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
