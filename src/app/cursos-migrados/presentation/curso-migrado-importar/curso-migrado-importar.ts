import { ChangeDetectorRef, Component, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { CursoMigradoService } from '../../application/services/curso-migrado.service';
import { ImportarJsonResult } from '../../domain/models/curso-migrado.model';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-curso-migrado-importar',
  standalone: true,
  imports: [RouterLink, NgIcon, PageTitle],
  templateUrl: './curso-migrado-importar.html',
})
export class CursoMigradoImportar {
  private service = inject(CursoMigradoService);
  private toast   = inject(ToastService);
  private cdr     = inject(ChangeDetectorRef);

  archivo      = signal<File | null>(null);
  cargando     = signal(false);
  resultado    = signal<ImportarJsonResult | null>(null);
  previewItems = signal<{ curso: string; estudiantes: number; mes: string }[]>([]);

  onArchivoChange(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    this.archivo.set(file);
    this.resultado.set(null);
    this.previewItems.set([]);

    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
      try {
        const data = JSON.parse(e.target?.result as string);
        if (Array.isArray(data)) {
          const preview = data
            .filter((i: any) => (i.status ?? 'ok').toLowerCase() === 'ok' && i.curso && i.url)
            .slice(0, 8)
            .map((i: any) => ({
              curso:       i.curso ?? '',
              estudiantes: (i.estudiantes ?? []).length,
              mes:         i.mes ?? '',
            }));
          this.previewItems.set(preview);
        }
      } catch {  }
      this.cdr.detectChanges();
    };
    reader.readAsText(file);
  }

  limpiar(): void {
    this.archivo.set(null);
    this.resultado.set(null);
    this.previewItems.set([]);
  }

  importar(): void {
    const file = this.archivo();
    if (!file) return;

    this.cargando.set(true);
    this.resultado.set(null);

    this.service.importarJson(file).subscribe({
      next: (res) => {
        this.resultado.set(res);
        this.cargando.set(false);
        if (res.creados > 0) {
          this.toast.success(
            '¡Importación completa!',
            `${res.creados} curso${res.creados !== 1 ? 's' : ''} importado${res.creados !== 1 ? 's' : ''} correctamente.`
          );
        }
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo importar el archivo.'));
        this.cargando.set(false);
      },
    });
  }
}
