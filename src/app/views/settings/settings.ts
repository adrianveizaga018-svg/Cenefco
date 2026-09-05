import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { SettingsService } from '../../common/application/services/settings.service';
import { ToastService } from '../../common/application/services/toast.service';
import { CobroEstadoSettings, ColorEstadoCobro, GeneralSettings } from '../../common/domain/models/settings.model';
import { PageTitle } from '../../common/components/page-title/page-title';

@Component({
  selector: 'app-settings',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, NgIcon, PageTitle],
  templateUrl: './settings.html',
  styleUrls: ['./settings.scss']
})
export class SettingsComponent implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly settingsService = inject(SettingsService);
  private readonly toastService = inject(ToastService);

  settingsForm!: FormGroup;
  isLoading = signal(true);
  isSaving = signal(false);
  logoPreview = signal<string | null>(null);
  selectedLogoFile: File | null = null;

  readonly coloresEstadoCobro: { value: ColorEstadoCobro; label: string }[] = [
    { value: 'success', label: 'Verde (éxito)' },
    { value: 'warning', label: 'Naranja (advertencia)' },
    { value: 'danger',  label: 'Rojo (peligro)' },
    { value: 'primary', label: 'Azul (primario)' },
    { value: 'info',    label: 'Celeste (info)' },
  ];

  cobroEstadoForm!: FormGroup;
  isLoadingCobroEstado = signal(true);
  isSavingCobroEstado = signal(false);

  ngOnInit(): void {
    this.initForm();
    this.loadSettings();
    this.initCobroEstadoForm();
    this.loadCobroEstadoSettings();
  }

  private initForm(): void {
    this.settingsForm = this.fb.group({
      site_name:        ['', [Validators.required, Validators.maxLength(255)]],
      site_active:      [true],
      contact_email:    ['', [Validators.required, Validators.email]],
      items_per_page:   [10, [Validators.required, Validators.min(1), Validators.max(100)]],
      maintenance_mode: [false]
    });
  }

  private loadSettings(): void {
    this.isLoading.set(true);
    this.settingsService.getSettings().subscribe({
      next: (settings: GeneralSettings) => {
        this.settingsForm.patchValue(settings);
        if (settings.site_logo) {
          this.logoPreview.set(settings.site_logo);
        }
        this.isLoading.set(false);
      },
      error: (error) => {
        console.error('Error al cargar configuraciones:', error);
        this.toastService.error('Error al cargar las configuraciones');
        this.isLoading.set(false);
      }
    });
  }

  onLogoSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;

    this.selectedLogoFile = file;

    const reader = new FileReader();
    reader.onload = (e) => this.logoPreview.set(e.target?.result as string);
    reader.readAsDataURL(file);
  }

  removeLogo(): void {
    this.selectedLogoFile = null;
    this.logoPreview.set(null);
  }

  onSubmit(): void {
    if (this.settingsForm.invalid) {
      this.settingsForm.markAllAsTouched();
      return;
    }

    this.isSaving.set(true);
    const formValue = this.settingsForm.value;

    this.settingsService.updateSettings({
      ...formValue,
      site_logo: this.selectedLogoFile ?? undefined,
    }).subscribe({
      next: (response) => {
        this.toastService.success(response.message || 'Configuraciones actualizadas correctamente');
        this.selectedLogoFile = null;
        this.isSaving.set(false);
      },
      error: (error) => {
        console.error('Error al actualizar configuraciones:', error);
        this.toastService.error('Error al actualizar las configuraciones');
        this.isSaving.set(false);
      }
    });
  }

  resetForm(): void {
    this.selectedLogoFile = null;
    this.loadSettings();
  }

  get f() {
    return this.settingsForm.controls;
  }

  private initCobroEstadoForm(): void {
    this.cobroEstadoForm = this.fb.group({
      completo_label:  ['', [Validators.required, Validators.maxLength(30)]],
      completo_color:  ['success', Validators.required],
      parcial_label:   ['', [Validators.required, Validators.maxLength(30)]],
      parcial_color:   ['warning', Validators.required],
      sin_pagos_label: ['', [Validators.required, Validators.maxLength(30)]],
      sin_pagos_color: ['danger', Validators.required],
    });
  }

  private loadCobroEstadoSettings(): void {
    this.isLoadingCobroEstado.set(true);
    this.settingsService.getCobroEstadoSettings().subscribe({
      next: (settings: CobroEstadoSettings) => {
        this.cobroEstadoForm.patchValue(settings);
        this.isLoadingCobroEstado.set(false);
      },
      error: (error) => {
        console.error('Error al cargar la configuración de estado de cobro:', error);
        this.toastService.error('Error al cargar la configuración de estado de cobro');
        this.isLoadingCobroEstado.set(false);
      }
    });
  }

  get cf() {
    return this.cobroEstadoForm.controls;
  }

  onSubmitCobroEstado(): void {
    if (this.cobroEstadoForm.invalid) {
      this.cobroEstadoForm.markAllAsTouched();
      return;
    }

    this.isSavingCobroEstado.set(true);
    this.settingsService.updateCobroEstadoSettings(this.cobroEstadoForm.value).subscribe({
      next: (response) => {
        this.toastService.success(response.message || 'Configuración de estado de cobro actualizada correctamente');
        this.isSavingCobroEstado.set(false);
      },
      error: (error) => {
        console.error('Error al actualizar la configuración de estado de cobro:', error);
        this.toastService.error('Error al actualizar la configuración de estado de cobro');
        this.isSavingCobroEstado.set(false);
      }
    });
  }

  resetCobroEstadoForm(): void {
    this.loadCobroEstadoSettings();
  }
}
