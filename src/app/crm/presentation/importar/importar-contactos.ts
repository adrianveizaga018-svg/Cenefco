import { Component, OnInit, inject, input, output, signal } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { extractErrorMessage } from '../../../utils/http-error';
import { CrmService, ResultadoImportacion, VendedorCrm } from '../../application/services/crm.service';

/**
 * Sube al embudo los contactos de un grupo de WhatsApp, una feria o una base.
 * Se reparten por turnos entre los vendedores (o a uno fijo) y no se duplican.
 */
@Component({
  selector: 'app-importar-contactos',
  standalone: true,
  imports: [FormsModule, NgIcon],
  templateUrl: './importar-contactos.html',
})
export class ImportarContactosComponent implements OnInit {
  private crm = inject(CrmService);
  private http = inject(HttpClient);

  /** Campañas activas: el contacto queda ligado a la campaña para su informe de inscritos. */
  campanas = signal<{ id: number; nombre: string }[]>([]);
  campana  = signal('');

  ngOnInit(): void {
    this.http.get<{ data: { id: number; nombre: string }[] }>('/api/v1/campanas-publicidad', { params: new HttpParams().set('estado', 'activa').set('pageSize', '200') })
      .subscribe({ next: (res) => this.campanas.set(res.data ?? []), error: () => {} });
  }

  readonly vendedores = input<VendedorCrm[]>([]);
  readonly cerrar = output<void>();
  readonly importado = output<void>();

  archivo   = signal<File | null>(null);
  origen    = signal<'whatsapp_grupo' | 'excel'>('whatsapp_grupo');
  vendedor  = signal('');
  nota      = signal('');
  enviando  = signal(false);
  error     = signal('');
  resultado = signal<ResultadoImportacion | null>(null);

  onArchivo(ev: Event): void {
    this.error.set('');
    this.archivo.set((ev.target as HTMLInputElement).files?.[0] ?? null);
  }

  descargarPlantilla(): void {
    this.crm.plantilla().subscribe({
      next: (blob) => {
        const url = URL.createObjectURL(blob);
        const a = Object.assign(document.createElement('a'), { href: url, download: 'plantilla-contactos-cenefco.csv' });
        a.click();
        URL.revokeObjectURL(url);
      },
      error: () => this.error.set('No se pudo descargar la plantilla.'),
    });
  }

  subir(): void {
    const archivo = this.archivo();
    if (!archivo) { this.error.set('Elige el archivo de Excel o CSV.'); return; }
    if (archivo.size > 5 * 1024 * 1024) { this.error.set('El archivo pesa más de 5 MB. Divídelo en partes.'); return; }

    const fd = new FormData();
    fd.append('archivo', archivo);
    fd.append('origen', this.origen());
    if (this.vendedor()) fd.append('vendedor_id', this.vendedor());
    if (this.campana()) fd.append('campana_publicidad_id', this.campana());
    if (this.nota().trim()) fd.append('nota', this.nota().trim());

    this.enviando.set(true);
    this.error.set('');
    this.crm.importar(fd).subscribe({
      next: (res) => { this.enviando.set(false); this.resultado.set(res); this.importado.emit(); },
      error: (err: HttpErrorResponse) => {
        this.enviando.set(false);
        this.error.set(extractErrorMessage(err, 'No se pudo importar el archivo.'));
      },
    });
  }
}
