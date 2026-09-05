import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { RedSocialService } from '../../application/services/red-social.service';
import { RedSocial } from '../../domain/models/red-social.model';
import { ToastService } from '../../../common/application/services/toast.service';

@Component({
  selector: 'app-red-social-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './red-social-create.html',
  styles: ``
})
export class RedSocialCreate {
  private fb             = inject(FormBuilder);
  private redSocialService = inject(RedSocialService);
  private toast          = inject(ToastService);
  private router         = inject(Router);

  submitting = signal(false);

  plataformas = [
    'facebook', 'twitter', 'instagram', 'youtube',
    'tiktok', 'linkedin', 'whatsapp', 'telegram', 'otra'
  ];

  iconosPorPlataforma: Record<string, string> = {
    facebook:  'tablerBrandFacebook',
    twitter:   'tablerBrandX',
    instagram: 'tablerBrandInstagram',
    youtube:   'tablerBrandYoutube',
    tiktok:    'tablerBrandTiktok',
    linkedin:  'tablerBrandLinkedin',
    whatsapp:  'tablerBrandWhatsapp',
    telegram:  'tablerBrandTelegram',
    otra:      'tablerLink',
  };

  get iconoPlataforma(): string {
    return this.iconosPorPlataforma[this.form.get('red')?.value ?? ''] ?? 'tablerLink';
  }

  form = this.fb.group({
    red:            ['facebook', [Validators.required]],
    nombre_display: ['', [Validators.maxLength(100)]],
    url:            ['', [Validators.required, Validators.maxLength(255)]],
    orden:          [0, [Validators.required, Validators.min(0)]],
    activo:         [true],
  });

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.submitting.set(true);
    this.redSocialService.create(this.form.value as Partial<RedSocial>).subscribe({
      next: () => {
        this.toast.success('¡Creada!', 'La red social ha sido registrada correctamente');
        this.router.navigate(['/cenefco/redes-sociales']);
      },
      error: (err: HttpErrorResponse) => {
        if (err.status === 422 && err.error?.errors?.red) {
          this.toast.error('Plataforma duplicada', 'Ya existe una red social registrada con esta plataforma. Elige otra o edita la existente.');
        } else {
          this.toast.error('Error', 'No se pudo guardar la red social');
        }
        this.submitting.set(false);
      }
    });
  }
}
