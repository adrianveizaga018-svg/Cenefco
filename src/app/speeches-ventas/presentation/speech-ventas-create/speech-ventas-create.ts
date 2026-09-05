import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { FormsModule } from '@angular/forms';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { SpeechVentasService } from '../../application/services/speech-ventas.service';
import { ToastService } from '../../../common/application/services/toast.service';

@Component({
  selector: 'app-speech-ventas-create',
  standalone: true,
  imports: [NgIcon, RouterLink, FormsModule, PageTitle],
  templateUrl: './speech-ventas-create.html',
})
export class SpeechVentasCreate implements OnInit {
  private service = inject(SpeechVentasService);
  private router  = inject(Router);
  private toast   = inject(ToastService);
  private cdr     = inject(ChangeDetectorRef);

  saving     = signal(false);
  categorias = signal<string[]>([]);

  form = {
    titulo:         '',
    categoria:      '',
    categoriaCustom:'',
    contenido:      '',
    palabras_clave: '',
    activo:         true,
    orden:          0,
  };

  usarCategoriaCustom = false;

  ngOnInit(): void {
    this.service.getCategorias().subscribe({
      next: cats => { this.categorias.set(cats); this.cdr.detectChanges(); },
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
    this.service.create({
      titulo:         this.form.titulo.trim(),
      categoria:      this.categoriaFinal || null,
      contenido:      this.form.contenido.trim(),
      palabras_clave: this.form.palabras_clave.trim() || null,
      activo:         this.form.activo,
      orden:          this.form.orden,
    }).subscribe({
      next: () => {
        this.saving.set(false);
        this.toast.success('Creado', 'Speech registrado correctamente.');
        this.router.navigate(['/cenefco/speeches-ventas']);
      },
      error: () => {
        this.saving.set(false);
        this.toast.error('Error', 'No se pudo guardar el speech.');
        this.cdr.detectChanges();
      },
    });
  }
}
