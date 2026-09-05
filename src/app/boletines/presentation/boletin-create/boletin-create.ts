import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { BoletinService } from '../../application/services/boletin.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-boletin-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './boletin-create.html' })
export class BoletinCreate {
    private service = inject(BoletinService);
    private toast   = inject(ToastService);
    private router  = inject(Router);
    private fb      = inject(FormBuilder);

    submitting   = signal(false);
    uploadingImg = signal(false);
    previewUrl   = signal<string | null>(null);

    private autoId = Math.floor(Date.now() / 1000);

    form = this.fb.group({
        id_boletin:          [this.autoId, [Validators.required]],
        num_boletin:         [this.autoId, [Validators.required]],
        titulo_boletin:      ['', [Validators.required, Validators.maxLength(200)]],
        titulo_pagina:       [''],
        descripcion_boletin: [''],
        estado:              [1],
        imagen_url:          [''],
    });

    onImageSelected(event: Event): void {
        const file = (event.target as HTMLInputElement).files?.[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = e => this.previewUrl.set(e.target?.result as string);
        reader.readAsDataURL(file);

        this.uploadingImg.set(true);
        this.service.uploadImage(file).subscribe({
            next: (res) => { this.form.patchValue({ imagen_url: res.url }); this.uploadingImg.set(false); },
            error: () => { this.toast.error('Error', 'No se pudo subir la imagen'); this.previewUrl.set(null); this.uploadingImg.set(false); }
        });
    }

    removeImage(): void {
        this.previewUrl.set(null);
        this.form.patchValue({ imagen_url: '' });
    }

    onSubmit(): void {
        if (this.form.invalid) { this.form.markAllAsTouched(); return; }
        if (this.uploadingImg()) return;
        this.submitting.set(true);
        this.service.create(this.form.value as any).subscribe({
            next: () => { this.toast.success('¡Creado!', 'Boletín registrado'); this.router.navigate(['/cenefco/boletines']); },
            error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); }
        });
    }
}
