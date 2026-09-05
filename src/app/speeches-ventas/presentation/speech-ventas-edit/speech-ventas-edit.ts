import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { FormsModule } from '@angular/forms';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { SpeechVentasService } from '../../application/services/speech-ventas.service';
import { ToastService } from '../../../common/application/services/toast.service';

@Component({
  selector: 'app-speech-ventas-edit',
  standalone: true,
  imports: [NgIcon, RouterLink, FormsModule, PageTitle],
  templateUrl: './speech-ventas-edit.html',
})
export class SpeechVentasEdit implements OnInit {
  private service = inject(SpeechVentasService);
  private route   = inject(ActivatedRoute);
  private router  = inject(Router);
  private toast   = inject(ToastService);
  private cdr     = inject(ChangeDetectorRef);

  id         = 0;
  loading    = signal(true);
  hasError   = signal(false);
  forbidden  = signal(false);
  saving     = signal(false);
  categorias = signal<string[]>([]);

  form = {
    titulo:          '',
    categoria:       '',
    categoriaCustom: '',
    contenido:       '',
    palabras_clave:  '',
    activo:          true,
    orden:           0,
  };

  usarCategoriaCustom = false;

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    let loaded = 0;
    const checkDone = () => { if (++loaded === 2) { this.loading.set(false); this.cdr.detectChanges(); } };

    this.service.getCategorias().subscribe({
      next: cats => { this.categorias.set(cats); checkDone(); },
      error: () => checkDone(),
    });

    this.service.getById(this.id).subscribe({
      next: s => {
        this.form.titulo         = s.titulo;
        this.form.contenido      = s.contenido;
        this.form.palabras_clave = s.palabras_clave ?? '';
        this.form.activo         = s.activo;
        this.form.orden          = s.orden;

        if (s.categoria) {
          this.form.categoria = s.categoria;
        }
        checkDone();
      },
      error: (err: HttpErrorResponse) => {
        if (err.status === 403) this.forbidden.set(true);
        else this.hasError.set(true);
        checkDone();
      },
    });
  }

  onCategoriaChange(): void {
    this.usarCategoriaCustom = this.form.categoria === '__nueva__';
    this.cdr.detectChanges();
  }

  get categoriaFinal(): string {
    return this.usarCategoriaCustom
      ? this.form.categoriaCustom.trim()
      : this.form.categoria;
  }

  guardar(): void {
    if (!this.form.titulo.trim()) {
      this.toast.error('Validación', 'El título es obligatorio.');
      return;
    }
    if (!this.form.contenido.trim()) {
      this.toast.error('Validación', 'El contenido es obligatorio.');
      return;
    }
    this.saving.set(true);
    this.service.update(this.id, {
      titulo:         this.form.titulo.trim(),
      categoria:      this.categoriaFinal || null,
      contenido:      this.form.contenido.trim(),
      palabras_clave: this.form.palabras_clave.trim() || null,
      activo:         this.form.activo,
      orden:          this.form.orden,
    }).subscribe({
      next: () => {
        this.saving.set(false);
        this.toast.success('Actualizado', 'Speech actualizado correctamente.');
        this.router.navigate(['/cenefco/speeches-ventas']);
      },
      error: () => {
        this.saving.set(false);
        this.toast.error('Error', 'No se pudo actualizar el speech.');
        this.cdr.detectChanges();
      },
    });
  }
}
