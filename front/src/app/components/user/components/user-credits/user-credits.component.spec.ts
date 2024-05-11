import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UserCreditsComponent } from './user-credits.component';

describe('UserCreditsComponent', () => {
  let component: UserCreditsComponent;
  let fixture: ComponentFixture<UserCreditsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UserCreditsComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(UserCreditsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
