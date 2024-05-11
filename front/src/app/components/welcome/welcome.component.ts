import { Component, OnInit } from '@angular/core';
import { UserInfoService } from '../../services/user-info.service';

@Component({
  selector: 'app-welcome',
  standalone: true,
  imports: [],
  templateUrl: './welcome.component.html',
  styleUrl: './welcome.component.css'
})
export class WelcomeComponent {
  userInfo: any;

  constructor(private userInfoService: UserInfoService) {}

  ngOnInit(): void {
    this.userInfoService.getUserInfo().subscribe({
      next: (data) => {
        if (data.success) {
          this.userInfo = data.data;
          console.log("User info", data.data);
        } else {
          console.error('Failed to retrieve user info:', data.message);
        }
      },
      error: (error) => {
        console.error('Error:', error);
      }
    });
  }
}
