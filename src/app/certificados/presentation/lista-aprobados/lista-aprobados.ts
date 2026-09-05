import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { SlicePipe } from '@angular/common';
import { NgIcon } from '@ng-icons/core';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { Pagination } from '../../../common/components/pagination/pagination';
import { ToastService } from '../../../common/application/services/toast.service';
import { CertificadoService } from '../../application/services/certificado.service';
import { CertPlantilla, GenerarLoteResult, ListaAprobadoListResponse } from '../../domain/models/certificado.model';
import { extractErrorMessage } from '../../../utils/http-error';
import Swal from 'sweetalert2';

type ApiState =
  | { type: 'loading' }
  | { type: 'success'; response: ListaAprobadoListResponse }
  | { type: 'error' };

@Component({
  selector: 'app-lista-aprobados',
  imports: [NgIcon, PageTitle, Pagination, SlicePipe],
  templateUrl: './lista-aprobados.html',
  styles: ``
})
export class ListaAprobados implements OnInit {
  private service = inject(CertificadoService);
  private toast   = inject(ToastService);

  pageIndex  = signal(1);
  pageSize   = signal(15);
  busqueda   = signal('');
  private refresh = signal(0);

  private params = computed(() => ({
    pageIndex: this.pageIndex(),
    pageSize:  this.pageSize(),
    query:     this.busqueda() || undefined,
    refresh:   this.refresh(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      switchMap(p =>
        this.service.getAprobados(p).pipe(
          map(response => ({ type: 'success', response } as ApiState)),
          startWith({ type: 'loading' } as ApiState),
          catchError(() => of({ type: 'error' } as ApiState)),
        )
      ),
      startWith({ type: 'loading' } as ApiState),
    ),
    { requireSync: true }
  );

  get aprobados()  { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()      { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading()  { return this.state().type === 'loading'; }
  get isError()    { return this.state().type === 'error'; }

  onBusqueda(e: Event): void {
    this.busqueda.set((e.target as HTMLInputElement).value.trim());
    this.pageIndex.set(1);
  }
  onPageChange(p: number): void { this.pageIndex.set(p); }

  plantillas      = signal<CertPlantilla[]>([]);
  plantillaId     = signal<number | null>(null);

  ngOnInit(): void {
    this.service.getPlantillas({ soloActivos: true, pageSize: 100 }).subscribe({
      next: res => {
        this.plantillas.set(res.data);
        if (res.data.length === 1) this.plantillaId.set(res.data[0].id);
      },
      error: () => {},
    });
  }

  generandoImp    = signal<number | null>(null);
  resultado       = signal<GenerarLoteResult | null>(null);

  generarParaFila(imparteId: number): void {
    const plt = this.plantillaId();
    if (!plt) {
      this.toast.warning('Selecciona plantilla', 'Elige una plantilla de certificado antes de generar.');
      return;
    }
    this.generandoImp.set(imparteId);
    this.resultado.set(null);
    this.service.generarLote(imparteId, plt).subscribe({
      next: res => {
        this.generandoImp.set(null);
        this.resultado.set(res);
        this.toast.success('¡Listo!', `Se generaron ${res.generados} certificado(s).`);
        this.refresh.update(n => n + 1);
      },
      error: (err: HttpErrorResponse) => {
        this.generandoImp.set(null);
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo generar el certificado.'));
      },
    });
  }

  showForm  = signal(false);
  guardando = signal(false);
  form = signal({ imparte_id: null as number | null, usuario_id: null as number | null,
    nota_final: null as number | null, condicion: 'aprobado', observacion: '' });

  mostrarForm(): void {
    this.form.set({ imparte_id: null as number | null, usuario_id: null as number | null, nota_final: null as number | null, condicion: 'aprobado', observacion: '' });
    this.showForm.set(true);
  }

  cancelar(): void { this.showForm.set(false); }

  onField(field: string, value: any): void {
    this.form.update(f => ({ ...f, [field]: value }));
  }

  guardar(): void {
    const f = this.form();
    if (!f.imparte_id || !f.usuario_id) {
      this.toast.warning('Faltan datos', 'Ingrese el ID de apertura y el ID de estudiante.');
      return;
    }
    this.guardando.set(true);
    this.service.createAprobado(f).subscribe({
      next: () => {
        this.guardando.set(false);
        this.toast.success('¡Guardado!', 'Aprobado registrado correctamente.');
        this.showForm.set(false);
        this.refresh.update(n => n + 1);
      },
      error: (err: HttpErrorResponse) => {
        this.guardando.set(false);
        this.toast.error('Error', extractErrorMessage(err));
      },
    });
  }

  deleteAprobado(id: number): void {
    Swal.fire({ title: '¿Eliminar este registro?', icon: 'warning', showCancelButton: true,
      confirmButtonColor: '#d33', cancelButtonText: 'Cancelar', confirmButtonText: 'Sí, eliminar',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.service.deleteAprobado(id).subscribe({
        next: () => { this.toast.success('Eliminado', 'Registro eliminado.'); this.refresh.update(n => n + 1); },
        error: (err: HttpErrorResponse) => this.toast.error('Error', extractErrorMessage(err)),
      });
    });
  }

  condicionBadge(condicion: string): string {
    return condicion === 'aprobado' ? 'bg-success/15 text-success' : 'bg-info/15 text-info';
  }

  estadoCertBadge(estado: string): string {
    const map: Record<string, string> = {
      pendiente: 'bg-warning/15 text-warning',
      generado:  'bg-success/15 text-success',
    };
    return map[estado] ?? 'bg-default-200 text-default-600';
  }
}
