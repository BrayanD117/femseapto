import { ComponentFixture, TestBed } from '@angular/core/testing';

import { InfoRequestSavingComponent } from './info-request-saving.component';

describe('InfoRequestSavingComponent', () => {
  let component: InfoRequestSavingComponent;
  let fixture: ComponentFixture<InfoRequestSavingComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [InfoRequestSavingComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(InfoRequestSavingComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
