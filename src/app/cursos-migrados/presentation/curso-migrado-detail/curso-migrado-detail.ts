import { Component, ChangeDetectorRef, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle }  from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { CursoMigradoService } from '../../application/services/curso-migrado.service';
import { CursoMigrado, ImportarExcelResult, LogoMigrado, ParticipanteMigrado, UpdateCursoMigradoPayload } from '../../domain/models/curso-migrado.model';
import * as XLSX from 'xlsx';

@Component({
  selector: 'app-curso-migrado-detail',
  standalone: true,
  imports: [NgIcon, RouterLink, PageTitle],
  templateUrl: './curso-migrado-detail.html',
})
export class CursoMigradoDetail implements OnInit {
  private service = inject(CursoMigradoService);
  private route   = inject(ActivatedRoute);
  private toast   = inject(ToastService);
  private cdr     = inject(ChangeDetectorRef);

  curso     = signal<CursoMigrado | null>(null);
  isLoading = signal(true);
  hasError  = signal(false);
  forbidden = signal(false);

  searchQuery = signal('');

  get participantes() {
    const q = this.searchQuery().toLowerCase().trim();
    const lista = this.curso()?.participantes ?? [];
    return q ? lista.filter(p => p.nombre_completo.toLowerCase().includes(q)) : lista;
  }

  modalAbierto = signal(false);
  nuevoNombre  = signal('');
  guardando    = signal(false);

  abrirModal(): void {
    this.nuevoNombre.set('');
    this.modalAbierto.set(true);
  }

  cerrarModal(): void {
    this.modalAbierto.set(false);
  }

  setNuevoNombre(valor: string): void {
    this.nuevoNombre.set(valor.toUpperCase());
  }

  guardarParticipante(): void {
    const nombre = this.nuevoNombre().trim();
    if (!nombre) return;
    const id = this.curso()?.id;
    if (id == null) return;

    this.guardando.set(true);
    this.service.addParticipante(id, nombre).subscribe({
      next: (nuevo) => {
        const c = this.curso();
        if (c) {
          this.curso.set({ ...c, participantes: [...(c.participantes ?? []), nuevo] });
        }
        this.guardando.set(false);
        this.modalAbierto.set(false);
        this.toast.success('Listo', 'Participante agregado correctamente.');
        this.cdr.detectChanges();
      },
      error: (err) => {
        this.guardando.set(false);
        this.toast.error('Error', err?.error?.message ?? 'No se pudo agregar el participante.');
        this.cdr.detectChanges();
      },
    });
  }

  editando       = signal<ParticipanteMigrado | null>(null);
  editNombre     = signal('');
  editGuardando  = signal(false);

  abrirEditar(p: ParticipanteMigrado): void {
    this.editando.set(p);
    this.editNombre.set(p.nombre_completo.toUpperCase());
  }

  setEditNombre(valor: string): void {
    this.editNombre.set(valor.toUpperCase());
  }

  cerrarEditar(): void {
    this.editando.set(null);
  }

  guardarEdicion(): void {
    const nombre = this.editNombre().trim();
    const p      = this.editando();
    const id     = this.curso()?.id;
    if (!nombre || !p || id == null) return;

    this.editGuardando.set(true);
    this.service.updateParticipante(id, p.id, nombre).subscribe({
      next: (actualizado) => {
        const c = this.curso();
        if (c) {
          this.curso.set({
            ...c,
            participantes: (c.participantes ?? []).map(x => x.id === actualizado.id ? actualizado : x),
          });
        }
        this.editGuardando.set(false);
        this.editando.set(null);
        this.toast.success('Listo', 'Participante actualizado.');
        this.cdr.detectChanges();
      },
      error: (err) => {
        this.editGuardando.set(false);
        this.toast.error('Error', err?.error?.message ?? 'No se pudo actualizar el participante.');
        this.cdr.detectChanges();
      },
    });
  }

  eliminando = signal<number | null>(null);

  confirmarEliminar(p: ParticipanteMigrado): void {
    const id = this.curso()?.id;
    if (id == null) return;
    if (!confirm(`Â¿Eliminar a "${p.nombre_completo}"?`)) return;

    this.eliminando.set(p.id);
    this.service.deleteParticipante(id, p.id).subscribe({
      next: () => {
        const c = this.curso();
        if (c) {
          this.curso.set({
            ...c,
            participantes: (c.participantes ?? []).filter(x => x.id !== p.id),
          });
        }
        this.eliminando.set(null);
        this.toast.success('Listo', 'Participante eliminado.');
        this.cdr.detectChanges();
      },
      error: () => {
        this.eliminando.set(null);
        this.toast.error('Error', 'No se pudo eliminar el participante.');
        this.cdr.detectChanges();
      },
    });
  }

  subiendoImagen   = signal(false);
  eliminandoImagen = signal(false);

  onImagenSeleccionada(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    const id = this.curso()?.id;
    if (id == null) return;

    this.subiendoImagen.set(true);
    this.service.uploadImagen(id, file).subscribe({
      next: (res) => {
        const c = this.curso();
        if (c) this.curso.set({ ...c, imagen_path: res.imagen_path });
        this.subiendoImagen.set(false);
        this.toast.success('Listo', 'Imagen actualizada.');
        this.cdr.detectChanges();
      },
      error: () => {
        this.subiendoImagen.set(false);
        this.toast.error('Error', 'No se pudo subir la imagen.');
        this.cdr.detectChanges();
      },
    });
  }

  confirmarEliminarImagen(): void {
    const id = this.curso()?.id;
    if (id == null) return;
    if (!confirm('¿Eliminar la imagen de portada?')) return;

    this.eliminandoImagen.set(true);
    this.service.deleteImagen(id).subscribe({
      next: () => {
        const c = this.curso();
        if (c) this.curso.set({ ...c, imagen_path: null });
        this.eliminandoImagen.set(false);
        this.toast.success('Listo', 'Imagen eliminada.');
        this.cdr.detectChanges();
      },
      error: () => {
        this.eliminandoImagen.set(false);
        this.toast.error('Error', 'No se pudo eliminar la imagen.');
        this.cdr.detectChanges();
      },
    });
  }

  subiendoLogo   = signal(false);
  eliminandoLogo = signal<number | null>(null);

  onLogoSeleccionado(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    const id = this.curso()?.id;
    if (id == null) return;

    this.subiendoLogo.set(true);
    this.service.uploadLogo(id, file).subscribe({
      next: (logo: LogoMigrado) => {
        const c = this.curso();
        if (c) this.curso.set({ ...c, logos: [...(c.logos ?? []), logo] });
        this.subiendoLogo.set(false);
        (event.target as HTMLInputElement).value = '';
        this.toast.success('Listo', 'Logo agregado correctamente.');
        this.cdr.detectChanges();
      },
      error: () => {
        this.subiendoLogo.set(false);
        this.toast.error('Error', 'No se pudo subir el logo.');
        this.cdr.detectChanges();
      },
    });
  }

  confirmarEliminarLogo(logo: LogoMigrado): void {
    const id = this.curso()?.id;
    if (id == null) return;
    if (!confirm(`¿Eliminar el logo "${logo.nombre ?? logo.path}"?`)) return;

    this.eliminandoLogo.set(logo.id);
    this.service.deleteLogo(id, logo.id).subscribe({
      next: () => {
        const c = this.curso();
        if (c) this.curso.set({ ...c, logos: (c.logos ?? []).filter(l => l.id !== logo.id) });
        this.eliminandoLogo.set(null);
        this.toast.success('Listo', 'Logo eliminado.');
        this.cdr.detectChanges();
      },
      error: () => {
        this.eliminandoLogo.set(null);
        this.toast.error('Error', 'No se pudo eliminar el logo.');
        this.cdr.detectChanges();
      },
    });
  }

  editandoCargaHoraria  = signal(false);
  cargaHorariaVal       = signal<number | null>(null);
  guardandoCargaHoraria = signal(false);

  abrirEditCargaHoraria(): void {
    this.cargaHorariaVal.set(this.curso()?.carga_horaria ?? null);
    this.editandoCargaHoraria.set(true);
  }

  cerrarEditCargaHoraria(): void {
    this.editandoCargaHoraria.set(false);
  }

  guardarCargaHoraria(): void {
    const id = this.curso()?.id;
    if (id == null) return;

    const payload: UpdateCursoMigradoPayload = { carga_horaria: this.cargaHorariaVal() };
    this.guardandoCargaHoraria.set(true);
    this.service.update(id, payload).subscribe({
      next: (updated) => {
        const c = this.curso();
        if (c) this.curso.set({ ...c, carga_horaria: updated.carga_horaria });
        this.guardandoCargaHoraria.set(false);
        this.editandoCargaHoraria.set(false);
        this.toast.success('Listo', 'Carga horaria actualizada.');
        this.cdr.detectChanges();
      },
      error: () => {
        this.guardandoCargaHoraria.set(false);
        this.toast.error('Error', 'No se pudo actualizar la carga horaria.');
        this.cdr.detectChanges();
      },
    });
  }

  editandoInfo  = signal(false);
  guardandoInfo = signal(false);
  infoForm = signal({
    periodo:       '' as string | null,
    gestion:       '' as string | null,
    fecha_inicio:  '' as string | null,
    carga_horaria: null as number | null,
  });

  abrirEditInfo(): void {
    const c = this.curso();
    this.infoForm.set({
      periodo:       c?.periodo ?? '',
      gestion:       c?.gestion ?? '',
      fecha_inicio:  c?.fecha_inicio ?? '',
      carga_horaria: c?.carga_horaria ?? null,
    });
    this.editandoInfo.set(true);
  }

  cerrarEditInfo(): void {
    this.editandoInfo.set(false);
  }

  setInfoCampo(campo: 'periodo' | 'gestion' | 'fecha_inicio', valor: string): void {
    this.infoForm.update(f => ({ ...f, [campo]: valor }));
  }

  setInfoCargaHoraria(valor: number | null): void {
    this.infoForm.update(f => ({ ...f, carga_horaria: valor }));
  }

  guardarInfo(): void {
    const id = this.curso()?.id;
    if (id == null) return;

    const f = this.infoForm();
    const payload: UpdateCursoMigradoPayload = {
      periodo:       f.periodo || null,
      gestion:       f.gestion || null,
      fecha_inicio:  f.fecha_inicio || null,
      carga_horaria: f.carga_horaria,
    };

    this.guardandoInfo.set(true);
    this.service.update(id, payload).subscribe({
      next: (updated) => {
        const c = this.curso();
        if (c) {
          this.curso.set({
            ...c,
            periodo:       updated.periodo,
            gestion:       updated.gestion,
            fecha_inicio:  updated.fecha_inicio,
            carga_horaria: updated.carga_horaria,
          });
        }
        this.guardandoInfo.set(false);
        this.editandoInfo.set(false);
        this.toast.success('Listo', 'Información del curso actualizada.');
        this.cdr.detectChanges();
      },
      error: (err) => {
        this.guardandoInfo.set(false);
        this.toast.error('Error', err?.error?.message ?? 'No se pudo actualizar la información del curso.');
        this.cdr.detectChanges();
      },
    });
  }

  generandoQr = signal(false);

  generarQr(): void {
    const id = this.curso()?.id;
    if (id == null) return;

    this.generandoQr.set(true);
    this.service.generarQr(id).subscribe({
      next: (res) => {
        const c = this.curso();
        if (c) this.curso.set({ ...c, qr_path: res.qr_path });
        this.generandoQr.set(false);
        this.toast.success('Listo', 'Código QR generado correctamente.');
        this.cdr.detectChanges();
      },
      error: () => {
        this.generandoQr.set(false);
        this.toast.error('Error', 'No se pudo generar el código QR.');
        this.cdr.detectChanges();
      },
    });
  }

  descargandoPdf   = signal(false);
  descargandoExcel = signal(false);

  descargarPdf(): void {
    const id = this.curso()?.id;
    if (id == null) return;
    this.descargandoPdf.set(true);
    this.service.exportPdf(id).subscribe({
      next: (blob) => {
        this.descargandoPdf.set(false);
        const url = URL.createObjectURL(blob);
        const a   = document.createElement('a');
        a.href     = url;
        a.download = `participantes-${this.curso()?.slug ?? id}.pdf`;
        a.click();
        URL.revokeObjectURL(url);
        this.cdr.detectChanges();
      },
      error: () => {
        this.descargandoPdf.set(false);
        this.toast.error('Error', 'No se pudo generar el PDF.');
        this.cdr.detectChanges();
      },
    });
  }

  descargarExcel(): void {
    const id = this.curso()?.id;
    if (id == null) return;
    this.descargandoExcel.set(true);
    this.service.exportExcel(id).subscribe({
      next: (blob) => {
        this.descargandoExcel.set(false);
        const url = URL.createObjectURL(blob);
        const a   = document.createElement('a');
        a.href     = url;
        a.download = `participantes-${this.curso()?.slug ?? id}.xlsx`;
        a.click();
        URL.revokeObjectURL(url);
        this.cdr.detectChanges();
      },
      error: () => {
        this.descargandoExcel.set(false);
        this.toast.error('Error', 'No se pudo generar el Excel.');
        this.cdr.detectChanges();
      },
    });
  }

  modalExcelAbierto  = signal(false);
  importandoExcel    = signal(false);
  resultadoExcel     = signal<ImportarExcelResult | null>(null);
  archivoExcel       = signal<File | null>(null);

  abrirModalExcel(): void {
    this.archivoExcel.set(null);
    this.resultadoExcel.set(null);
    this.modalExcelAbierto.set(true);
  }

  cerrarModalExcel(): void {
    this.modalExcelAbierto.set(false);
  }

  onArchivoExcelSeleccionado(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    this.archivoExcel.set(file);
    this.resultadoExcel.set(null);
  }

  descargarPlantillaExcel(): void {
    const hoja = XLSX.utils.aoa_to_sheet([
      ['Nombre Completo'],
      ['Juan Perez Rodriguez'],
    ]);
    const libro = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(libro, hoja, 'Participantes');
    XLSX.writeFile(libro, 'plantilla-participantes-migrados.xlsx');
  }

  importarExcel(): void {
    const archivo = this.archivoExcel();
    const id      = this.curso()?.id;
    if (!archivo || id == null) return;

    this.importandoExcel.set(true);
    this.resultadoExcel.set(null);

    this.service.importarExcel(id, archivo).subscribe({
      next: (res) => {
        this.resultadoExcel.set(res);
        this.importandoExcel.set(false);

        if (res.insertados > 0) {
          this.service.getById(id).subscribe({
            next: (data) => {
              this.curso.set(data);
              this.cdr.detectChanges();
            },
          });
          this.toast.success('Importación completa', `${res.insertados} participante(s) agregado(s).`);
        } else {
          this.toast.warning('Sin cambios', 'Todos los registros ya existían o fueron omitidos.');
        }
        this.cdr.detectChanges();
      },
      error: (err) => {
        this.importandoExcel.set(false);
        this.toast.error('Error', err?.error?.message ?? 'No se pudo procesar el archivo.');
        this.cdr.detectChanges();
      },
    });
  }

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.service.getById(id).subscribe({
      next: (data) => {
        this.curso.set(data);
        this.isLoading.set(false);
        this.cdr.detectChanges();
      },
      error: (err: HttpErrorResponse) => {
        if (err.status === 403) {
          this.forbidden.set(true);
        } else {
          this.toast.error('Error', 'No se pudo cargar el curso.');
          this.hasError.set(true);
        }
        this.isLoading.set(false);
        this.cdr.detectChanges();
      },
    });
  }
}
