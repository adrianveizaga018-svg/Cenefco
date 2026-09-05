import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { Router } from '@angular/router';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { Pagination } from '../../../common/components/pagination/pagination';
import { ToastService } from '../../../common/application/services/toast.service';
import { CertificadoService } from '../../application/services/certificado.service';
import { CertPlantilla, CertPlantillaListResponse } from '../../domain/models/certificado.model';
import { extractErrorMessage } from '../../../utils/http-error';
import Swal from 'sweetalert2';

type ApiState =
  | { type: 'loading' }
  | { type: 'success'; response: CertPlantillaListResponse }
  | { type: 'error' };

@Component({
  selector: 'app-cert-plantillas',
  imports: [NgIcon, PageTitle, Pagination],
  templateUrl: './cert-plantillas.html',
  styles: ``
})
export class CertPlantillas {
  private service = inject(CertificadoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);

  pageIndex = signal(1);
  pageSize  = signal(20);
  private refresh = signal(0);

  private params = computed(() => ({ pageIndex: this.pageIndex(), pageSize: this.pageSize(), refresh: this.refresh() }));

  private state = toSignal(
    toObservable(this.params).pipe(
      switchMap(p =>
        this.service.getPlantillas(p).pipe(
          map(response => ({ type: 'success', response } as ApiState)),
          startWith({ type: 'loading' } as ApiState),
          catchError(() => of({ type: 'error' } as ApiState)),
        )
      ),
      startWith({ type: 'loading' } as ApiState),
    ),
    { requireSync: true }
  );

  get plantillas() { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()      { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading()  { return this.state().type === 'loading'; }
  get isError()    { return this.state().type === 'error'; }

  showForm   = signal(false);
  editando   = signal<CertPlantilla | null>(null);
  subiendo   = signal(false);
  guardando  = signal(false);

  form = signal({
    nombre: '', tipo: 'aprobacion', imagen_url: '',
    ancho_px: 3508, alto_px: 2480, orientacion: 'horizontal',
    formato_salida: 'jpg', calidad_jpg: 95,
    color_default: '#000000', estado: 'activo', notas: '',
  });

  nuevaPlantilla(): void {
    this.editando.set(null);
    this.form.set({ nombre: '', tipo: 'aprobacion', imagen_url: '', ancho_px: 3508, alto_px: 2480,
      orientacion: 'horizontal', formato_salida: 'jpg', calidad_jpg: 95, color_default: '#000000', estado: 'activo', notas: '' });
    this.showForm.set(true);
  }

  editarPlantilla(p: CertPlantilla): void {
    this.editando.set(p);
    this.form.set({ nombre: p.nombre, tipo: p.tipo, imagen_url: p.imagen_url,
      ancho_px: p.ancho_px, alto_px: p.alto_px, orientacion: p.orientacion,
      formato_salida: p.formato_salida, calidad_jpg: p.calidad_jpg,
      color_default: p.color_default, estado: p.estado, notas: p.notas ?? '' });
    this.showForm.set(true);
  }

  cancelar(): void { this.showForm.set(false); this.editando.set(null); }

  onInputChange(field: string, value: any): void {
    this.form.update(f => ({ ...f, [field]: value }));
  }

  subirImagen(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    this.subiendo.set(true);
    this.service.uploadImagenPlantilla(file).subscribe({
      next: res => { this.form.update(f => ({ ...f, imagen_url: res.url })); this.subiendo.set(false); },
      error: () => { this.toast.error('Error', 'No se pudo subir la imagen.'); this.subiendo.set(false); },
    });
  }

  guardar(): void {
    const f = this.form();
    if (!f.nombre || !f.imagen_url) {
      this.toast.warning('Faltan datos', 'Nombre e imagen son obligatorios.');
      return;
    }
    this.guardando.set(true);
    const ed = this.editando();
    const obs = ed ? this.service.updatePlantilla(ed.id, f) : this.service.createPlantilla(f);
    obs.subscribe({
      next: () => {
        this.guardando.set(false);
        this.toast.success('¡Guardado!', ed ? 'Plantilla actualizada.' : 'Plantilla creada.');
        this.showForm.set(false);
        this.editando.set(null);
        this.refresh.update(n => n + 1);
      },
      error: (err: HttpErrorResponse) => {
        this.guardando.set(false);
        this.toast.error('Error', extractErrorMessage(err));
      },
    });
  }

  verCampos(p: CertPlantilla): void {
    this.router.navigate(['/cenefco/cert-plantilla-campos', p.id]);
  }

  deletePlantilla(p: CertPlantilla): void {
    Swal.fire({ title: `¿Eliminar "${p.nombre}"?`, icon: 'warning', showCancelButton: true,
      confirmButtonColor: '#d33', cancelButtonText: 'Cancelar', confirmButtonText: 'Sí, eliminar',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.service.deletePlantilla(p.id).subscribe({
        next: () => { this.toast.success('Eliminada', 'Plantilla eliminada.'); this.refresh.update(n => n + 1); },
        error: (err: HttpErrorResponse) => this.toast.error('Error', extractErrorMessage(err)),
      });
    });
  }

  storageUrl(url: string | null | undefined): string {
    if (!url) return '';
    if (url.startsWith('http')) return url;
    return url.startsWith('/') ? url : '/' + url;
  }
}
