import { Component, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ConfiguracionService } from '../../../configuraciones/application/services/configuracion.service';
import { GeneralSettings } from '../../../configuraciones/domain/models/configuracion.model';
import { ToastService } from '../../../common/application/services/toast.service';

@Component({
  selector: 'app-configuraciones',
  standalone: true,
  imports: [CommonModule, FormsModule, NgIcon, PageTitle],
  templateUrl: './configuraciones.html',
  styles: ``
})
export class Configuraciones {
  private configuracionService = inject(ConfiguracionService);
  private toast = inject(ToastService);

  settings = signal<GeneralSettings | null>(null);
  loading = signal(false);
  saving = signal(false);

  ngOnInit() {
    this.loadSettings();
  }

  loadSettings() {
    this.loading.set(true);
    this.configuracionService.getSettings().subscribe({
      next: (data) => {
        this.settings.set(data);
        this.loading.set(false);
      },
      error: (error) => {
        console.error('Error al cargar configuraciones:', error);
        this.toast.error('Error al cargar las configuraciones');
        this.loading.set(false);
      }
    });
  }

  onSubmit() {
    const currentSettings = this.settings();
    if (!currentSettings) return;

    this.saving.set(true);
    this.configuracionService.updateSettings(currentSettings).subscribe({
      next: (response) => {
        this.settings.set(response.settings);
        this.toast.success('Configuraciones actualizadas correctamente');
        this.saving.set(false);
      },
      error: (error) => {
        console.error('Error al actualizar configuraciones:', error);
        this.toast.error('Error al actualizar las configuraciones');
        this.saving.set(false);
      }
    });
  }

  resetForm() {
    this.loadSettings();
  }
}
