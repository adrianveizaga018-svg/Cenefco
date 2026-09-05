import { Component } from '@angular/core';
import { NgIcon } from "@ng-icons/core";
import { SidebarMenu } from "./components/sidebar-menu/sidebar-menu";
import { SimplebarAngularModule } from "simplebar-angular";
import { LayoutService } from '../../../common/application/services/layout-store.service';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-sidenav',
  imports: [NgIcon, SidebarMenu, SimplebarAngularModule, RouterLink],
  templateUrl: './sidenav.html',
  styles: ``
})

export class Sidenav {
  constructor(public layout: LayoutService) { }

  hoverSidebar() {
    this.layout.setSidenavSize(this.layout.sidenavSize === 'hover-active' ? 'hover' : 'hover-active')
  }

  closeSidebar() {
    const html = document.documentElement;
    html.classList.toggle('sidebar-enable')
    this.layout.hideBackdrop()
  }

}
