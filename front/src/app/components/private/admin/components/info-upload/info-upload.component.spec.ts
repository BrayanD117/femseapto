import { ComponentFixture, TestBed } from '@angular/core/testing';

import { InfoUploadComponent } from './info-upload.component';

describe('InfoUploadComponent', () => {
  let component: InfoUploadComponent;
  let fixture: ComponentFixture<InfoUploadComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [InfoUploadComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(InfoUploadComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
