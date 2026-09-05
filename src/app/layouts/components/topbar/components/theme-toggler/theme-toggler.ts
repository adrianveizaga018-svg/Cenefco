import { Component } from '@angular/core';
import { NgIcon } from "@ng-icons/core";
import { LayoutService } from '../../../../../common/application/services/layout-store.service';

@Component({
  selector: 'app-theme-toggler',
  imports: [NgIcon],
  templateUrl: './theme-toggler.html',
  styles: ``
})
export class ThemeToggler {

  constructor(public layout: LayoutService) {
  }

  toggleTheme() {
    if (this.layout.theme === 'light') {
      this.layout.setTheme('dark')
    } else {
      this.layout.setTheme('light')
    }
  }
}
