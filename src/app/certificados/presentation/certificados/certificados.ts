import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { SlicePipe } from '@angular/common';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { Pagination } from '../../../common/components/pagination/pagination';
import { ToastService } from '../../../common/application/services/toast.service';
import { CertificadoService } from '../../application/services/certificado.service';
import { CertificadoListResponse, CertPlantilla, GenerarLoteResult } from '../../domain/models/certificado.model';
import { extractErrorMessage } from '../../../utils/http-error';
import Swal from 'sweetalert2';

type ApiState =
  | { type: 'loading' }
  | { type: 'success'; response: CertificadoListResponse }
  | { type: 'error' }
  | { type: 'forbidden' };

@Component({
  selector: 'app-certificados',
  imports: [NgIcon, PageTitle, Pagination, SlicePipe],
  templateUrl: './certificados.html',
  styles: ``
})
export class Certificados {
  private service = inject(CertificadoService);
  private toast   = inject(ToastService);

  pageIndex  = signal(1);
  pageSize   = signal(15);
  filtroImp  = signal<number | null>(null);
  private refresh = signal(0);

  private params = computed(() => ({
    pageIndex:  this.pageIndex(),
    pageSize:   this.pageSize(),
    imparte_id: this.filtroImp() ?? undefined,
    refresh:    this.refresh(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      switchMap(p =>
        this.service.getCertificados(p).pipe(
          map(response => ({ type: 'success', response } as ApiState)),
          startWith({ type: 'loading' } as ApiState),
          catchError((err: HttpErrorResponse) => of({ type: err.status === 403 ? 'forbidden' : 'error' } as ApiState)),
        )
      ),
      startWith({ type: 'loading' } as ApiState),
    ),
    { requireSync: true }
  );

  get certificados() { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()        { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading()    { return this.state().type === 'loading'; }
  get isError()      { return this.state().type === 'error'; }
  get forbidden()    { return this.state().type === 'forbidden'; }

  onPageChange(p: number): void { this.pageIndex.set(p); }
  onFiltrarImp(e: Event): void {
    const v = (e.target as HTMLInputElement).value;
    this.filtroImp.set(v ? +v : null);
    this.pageIndex.set(1);
  }

  plantillas     = signal<CertPlantilla[]>([]);
  plantillasLoad = signal(false);
  genImparteId   = signal<number | null>(null);
  genPlantillaId = signal<number | null>(null);
  generando      = signal(false);
  resultado      = signal<GenerarLoteResult | null>(null);

  ngOnInit(): void {
    this.plantillasLoad.set(true);
    this.service.getPlantillas({ soloActivos: true, pageSize: 100 }).subscribe({
      next: res => { this.plantillas.set(res.data); this.plantillasLoad.set(false); },
      error: ()  => this.plantillasLoad.set(false),
    });
  }

  generarLote(): void {
    const imp = this.genImparteId();
    const plt = this.genPlantillaId();
    if (!imp || !plt) {
      this.toast.warning('Faltan datos', 'Ingrese el ID de apertura y seleccione una plantilla.');
      return;
    }
    this.generando.set(true);
    this.resultado.set(null);
    this.service.generarLote(imp, plt).subscribe({
      next: res => {
        this.generando.set(false);
        this.resultado.set(res);
        this.toast.success('¡Listo!', `Se generaron ${res.generados} certificado(s).`);
        this.refresh.update(n => n + 1);
      },
      error: (err: HttpErrorResponse) => {
        this.generando.set(false);
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo generar el lote.'));
      },
    });
  }

  deleteCertificado(id: number): void {
    Swal.fire({
      title: '¿Eliminar certificado?',
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Sí, eliminar',
    }).then(result => {
      if (!result.isConfirmed) return;
      this.service.deleteCertificado(id).subscribe({
        next: () => { this.toast.success('Eliminado', 'Certificado eliminado.'); this.refresh.update(n => n + 1); },
        error: (err: HttpErrorResponse) => this.toast.error('Error', extractErrorMessage(err)),
      });
    });
  }

  estadoBadge(estado: string): string {
    const map: Record<string, string> = {
      generado: 'bg-success/15 text-success',
      anulado:  'bg-danger/15 text-danger',
    };
    return map[estado] ?? 'bg-default-200 text-default-600';
  }

  previewUrl(archivoUrl: string | null): string | null {
    if (!archivoUrl) return null;
    return archivoUrl.replace(/\.pdf$/, '_preview.jpg');
  }

  readonly storageBase = '';
}
