import { Component, inject, signal, OnInit } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { RedSocialService } from '../../application/services/red-social.service';
import { RedSocial } from '../../domain/models/red-social.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-red-social-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './red-social-edit.html',
  styles: ``
})
export class RedSocialEdit implements OnInit {
  private fb             = inject(FormBuilder);
  private redSocialService = inject(RedSocialService);
  private toast          = inject(ToastService);
  private router         = inject(Router);
  private route          = inject(ActivatedRoute);

  submitting      = signal(false);
  loadingRedSocial = signal(true);
  private id!: number;

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

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.redSocialService.getById(this.id).subscribe({
      next: (red) => {
        this.form.patchValue({
          red:            red.red,
          nombre_display: red.nombre_display ?? '',
          url:            red.url,
          orden:          red.orden,
          activo:         red.activo,
        });
        this.loadingRedSocial.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar la red social'));
        this.router.navigate(['/cenefco/redes-sociales']);
      }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.submitting.set(true);
    this.redSocialService.update(this.id, this.form.value as Partial<RedSocial>).subscribe({
      next: () => {
        this.toast.success('¡Actualizada!', 'La red social ha sido actualizada correctamente');
        this.router.navigate(['/cenefco/redes-sociales']);
      },
      error: (err: HttpErrorResponse) => {
        if (err.status === 422 && err.error?.errors?.red) {
          this.toast.warning('Plataforma duplicada', 'Ya existe otra red social registrada con esta plataforma.');
        } else {
          this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar la red social'));
        }
        this.submitting.set(false);
      }
    });
  }
}
