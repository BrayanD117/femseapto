import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UserSavingComponent } from './user-saving.component';

describe('UserSavingComponent', () => {
  let component: UserSavingComponent;
  let fixture: ComponentFixture<UserSavingComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UserSavingComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(UserSavingComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
