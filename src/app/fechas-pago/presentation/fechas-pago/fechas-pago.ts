import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { FechaPagoService } from '../../application/services/fecha-pago.service';
import { FechaPagoListResponse } from '../../domain/models/fecha-pago.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { AsistenteCuotas } from '../asistente-cuotas/asistente-cuotas';
import { FormsModule } from '@angular/forms';
import Swal from 'sweetalert2';
type S = { type: 'loading' } | { type: 'success'; response: FechaPagoListResponse } | { type: 'error' } | { type: 'forbidden' };
const L: S = { type: 'loading' }; const E: S = { type: 'error' }; const F: S = { type: 'forbidden' };
@Component({ selector: 'app-fechas-pago', imports: [NgIcon, Pagination, PageTitle, RouterLink, AsistenteCuotas, FormsModule], templateUrl: './fechas-pago.html' })
export class FechasPago {
  private service = inject(FechaPagoService); private toast = inject(ToastService);
  pageIndex = signal(1); pageSize = signal(10); private refresh = signal(0);
  showAsistente = signal(false);
  idPlanAsistente = signal<number | null>(null);
  private params = computed(() => ({ pageIndex: this.pageIndex(), pageSize: this.pageSize(), refresh: this.refresh(), conInactivos: true }));
  private state = toSignal(toObservable(this.params).pipe(switchMap(p => this.service.getAll(p).pipe(map(r => ({ type: 'success', response: r } as S)), startWith(L), catchError((err: HttpErrorResponse) => of(err.status === 403 ? F : E)))), startWith(L)), { requireSync: true });
  get items()     { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }
  get error()     { return this.state().type === 'error'; }

  get forbidden() { return this.state().type === 'forbidden'; }
  onPageChange(p: number): void { this.pageIndex.set(p); }
  onAsistenteGenerado(): void { this.showAsistente.set(false); this.refresh.update(n => n + 1); }
  delete(id: number): void {
    Swal.fire({ title: '¿Eliminar fecha de pago?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar' }).then(r => {
      if (r.isConfirmed) { this.service.delete(id).subscribe({ next: () => { this.toast.success('Eliminado', 'Fecha de pago eliminada'); this.refresh.update(n => n + 1); }, error: () => this.toast.error('Error', 'No se pudo eliminar') }); }
    });
  }
}
