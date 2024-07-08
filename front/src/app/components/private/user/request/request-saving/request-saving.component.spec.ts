import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RequestSavingComponent } from './request-saving.component';

describe('RequestSavingComponent', () => {
  let component: RequestSavingComponent;
  let fixture: ComponentFixture<RequestSavingComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RequestSavingComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(RequestSavingComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
