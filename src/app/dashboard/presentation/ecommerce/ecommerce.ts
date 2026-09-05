import { Component, inject, OnInit, signal } from '@angular/core';
import { NgIcon } from "@ng-icons/core";
import { Banner } from "./components/banner/banner";
import { Overview } from "./components/overview/overview";
import { PageTitle } from "../../../common/components/page-title/page-title";
import { DashboardService } from "../../../dashboard/application/services/dashboard.service";
import { DashboardStats } from "../../../dashboard/domain/models/dashboard.model";

@Component({
  selector: 'app-ecommerce',
  imports: [NgIcon, PageTitle, Banner, Overview],
  templateUrl: './ecommerce.html',
})
export class Ecommerce implements OnInit {
  private dashboardService = inject(DashboardService);

  stats   = signal<DashboardStats | null>(null);
  loading = signal(true);

  ngOnInit(): void { this.load(); }

  reload(): void { this.loading.set(true); this.stats.set(null); this.load(); }

  private load(): void {
    this.dashboardService.getStats().subscribe({
      next: (data) => { this.stats.set(data); this.loading.set(false); },
      error: ()     => this.loading.set(false),
    });
  }
}
