import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PublicPersonComponent } from './public-person.component';

describe('PublicPersonComponent', () => {
  let component: PublicPersonComponent;
  let fixture: ComponentFixture<PublicPersonComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PublicPersonComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(PublicPersonComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
