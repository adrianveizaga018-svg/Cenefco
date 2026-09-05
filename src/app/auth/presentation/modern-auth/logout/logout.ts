import { Component, inject, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { NgIcon } from "@ng-icons/core";
import { AuthService } from '../../../application/services/auth.service';

@Component({
  selector: 'app-logout',
  imports: [RouterLink, NgIcon],
  templateUrl: './logout.html',
  styles: ``
})
export class Logout implements OnInit {
  private authService = inject(AuthService);

  ngOnInit(): void {
    this.authService.logout();
  }
}
