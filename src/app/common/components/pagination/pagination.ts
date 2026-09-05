import { Component, computed, input, output } from '@angular/core';
import { NgIcon } from '@ng-icons/core';

@Component({
  selector: 'app-pagination',
  imports: [NgIcon],
  templateUrl: './pagination.html',
  styles: ``
})
export class Pagination {
  total       = input<number>(0);
  pageSize    = input<number>(10);
  currentPage = input<number>(1);

  pageChange = output<number>();

  totalPages = computed(() => Math.max(1, Math.ceil(this.total() / this.pageSize())));

  pages = computed(() => {
    const total   = this.totalPages();
    const current = this.currentPage();
    const window  = 5;

    let start = Math.max(1, current - Math.floor(window / 2));
    let end   = start + window - 1;

    if (end > total) {
      end   = total;
      start = Math.max(1, end - window + 1);
    }

    return Array.from({ length: end - start + 1 }, (_, i) => start + i);
  });

  goTo(page: number): void {
    if (page >= 1 && page <= this.totalPages() && page !== this.currentPage()) {
      this.pageChange.emit(page);
    }
  }
}
