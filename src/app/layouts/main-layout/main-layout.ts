import { Component } from '@angular/core';
import { Sidenav } from "../components/sidenav/sidenav";
import { Topbar } from "../components/topbar/topbar";
import { Footer } from "../components/footer/footer";
import { Customizer } from "../components/customizer/customizer";
import { RouterOutlet } from '@angular/router';
import { RouteLoader } from '../../common/components/route-loader/route-loader';

@Component({
  selector: 'app-main-layout',
  imports: [Sidenav, Topbar, RouterOutlet, Footer, Customizer, RouteLoader],
  templateUrl: './main-layout.html',
  styles: ``
})
export class MainLayout {
}
