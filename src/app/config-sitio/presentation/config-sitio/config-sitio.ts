import { Component, inject, signal, ChangeDetectorRef, OnInit, computed } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { NgIcon, provideIcons } from '@ng-icons/core';
import { lucideLoader, lucideSave, lucideImage, lucideUpload } from '@ng-icons/lucide';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { ConfigSitioService } from '../../application/services/config-sitio.service';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import {
  ConfigSitioItem,
  ConfigSitioGrupo,
  GRUPOS_ETIQUETAS,
} from '../../domain/models/config-sitio.model';

@Component({
  selector: 'app-config-sitio',
  standalone: true,
  imports: [CommonModule, FormsModule, NgIcon, PageTitle],
  viewProviders: [provideIcons({ lucideLoader, lucideSave, lucideImage, lucideUpload })],
  templateUrl: './config-sitio.html',
})
export class ConfigSitio implements OnInit {
  private service       = inject(ConfigSitioService);
  private toast         = inject(ToastService);
  private cdr           = inject(ChangeDetectorRef);
  private fileUpload    = inject(FileUploadService);

  items        = signal<ConfigSitioItem[]>([]);
  loading      = signal(true);
  saving       = signal(false);
  grupoActivo  = signal<string>('institucional');
  uploading    = signal<string | null>(null);

  grupos = computed<ConfigSitioGrupo[]>(() => {
    const agrupado: Record<string, ConfigSitioItem[]> = {};
    for (const item of this.items()) {
      const g = item.grupo ?? 'otros';
      if (!agrupado[g]) agrupado[g] = [];
      agrupado[g].push(item);
    }
    return Object.entries(agrupado).map(([grupo, items]) => ({
      grupo,
      etiqueta: GRUPOS_ETIQUETAS[grupo] ?? grupo,
      items,
    }));
  });

  itemsGrupoActivo = computed<ConfigSitioItem[]>(() =>
    this.grupos().find(g => g.grupo === this.grupoActivo())?.items ?? []
  );

  etiquetaGrupoActivo = computed<string>(() =>
    this.grupos().find(g => g.grupo === this.grupoActivo())?.etiqueta ?? this.grupoActivo()
  );

  ngOnInit(): void {
    this.service.getAll().subscribe({
      next: (res) => {
        this.items.set(res.data);
        this.loading.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.toast.error('Error al cargar la configuración');
        this.loading.set(false);
        this.cdr.detectChanges();
      },
    });
  }

  seleccionarGrupo(grupo: string): void {
    this.grupoActivo.set(grupo);
  }

  updateValor(clave: string, valor: string): void {
    this.items.update(items =>
      items.map(i => i.clave === clave ? { ...i, valor } : i)
    );
  }

  onImageSelect(event: Event, clave: string): void {
    const input = event.target as HTMLInputElement;
    const file  = input.files?.[0];
    if (!file) return;

    this.uploading.set(clave);
    this.fileUpload.uploadImage(file).subscribe({
      next: (res) => {
        this.updateValor(clave, res.url);
        this.uploading.set(null);
        this.cdr.detectChanges();
      },
      error: () => {
        this.toast.error('No se pudo subir la imagen');
        this.uploading.set(null);
        input.value = '';
        this.cdr.detectChanges();
      },
    });
  }

  guardar(): void {
    this.saving.set(true);
    const payload = this.items().map(i => ({ clave: i.clave, valor: i.valor }));
    this.service.update(payload).subscribe({
      next: () => {
        this.toast.success('Configuración guardada correctamente');
        this.saving.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.toast.error('Error al guardar la configuración');
        this.saving.set(false);
        this.cdr.detectChanges();
      },
    });
  }
}
