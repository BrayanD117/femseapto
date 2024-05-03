import { Component } from '@angular/core';

// Login Animation
import { LottieComponent, AnimationOptions } from 'ngx-lottie';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [LottieComponent],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent {

  options: AnimationOptions = {
    path: '../../../assets/json/WelcomePeople.json' // download the JSON version of animation in your project directory and add the path to it like ./assets/animations/example.json
  };
}
