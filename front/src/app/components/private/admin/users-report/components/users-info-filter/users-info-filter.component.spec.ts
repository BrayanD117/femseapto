import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UsersInfoFilterComponent } from './users-info-filter.component';

describe('UsersInfoFilterComponent', () => {
  let component: UsersInfoFilterComponent;
  let fixture: ComponentFixture<UsersInfoFilterComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UsersInfoFilterComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(UsersInfoFilterComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
