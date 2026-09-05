import { Component, inject } from '@angular/core';
import { NgIcon } from '@ng-icons/core';
import { RouterLink } from '@angular/router';
import { AuthService } from '../../../../../auth/application/services/auth.service';

@Component({
  selector: 'app-user-profile',
  imports: [NgIcon, RouterLink],
  templateUrl: './user-profile.html',
  styles: ``
})
export class UserProfile {
  auth = inject(AuthService);

  logout(): void {
    this.auth.logout();
  }
}
