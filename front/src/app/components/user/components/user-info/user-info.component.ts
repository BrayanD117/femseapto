import { Component, OnInit } from '@angular/core';
import { UserInfoService } from '../../../../services/user-info.service';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-user-info',
  standalone: true,
  imports: [FormsModule, CommonModule],
  templateUrl: './user-info.component.html',
  styleUrls: ['./user-info.component.css']
})
export class UserInfoComponent implements OnInit {
  userInfo: any = {};
  loading: boolean = true;
  error: string = '';
  originalUserInfo: any = {};
  isDirty: boolean = false;

  constructor(private userInfoService: UserInfoService) {}

  ngOnInit(): void {
    this.userInfoService.getUserInfo().subscribe({
      next: (data) => {
        if (data.success) {
          this.userInfo = data.data;
          this.originalUserInfo = { ...data.data };
        } else {
          this.error = data.message;
        }
        this.loading = false;
      },
      error: (err) => {
        this.error = 'Error al obtener la información del usuario.';
        this.loading = false;
      }
    });
  }

  onInputChange(): void {
    this.isDirty = Object.keys(this.userInfo).some(
      key => this.userInfo[key] !== this.originalUserInfo[key]
    );
  }

  onSubmit(): void {
    if (this.isDirty) {
      console.log('Updating user info:', this.userInfo);
    }
  }
}
