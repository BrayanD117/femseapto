import { ComponentFixture, TestBed } from '@angular/core/testing';

import { NaturalPersonComponent } from './natural-person.component';

describe('NaturalPersonComponent', () => {
  let component: NaturalPersonComponent;
  let fixture: ComponentFixture<NaturalPersonComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [NaturalPersonComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(NaturalPersonComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
