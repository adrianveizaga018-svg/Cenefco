import { Component, OnInit, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { TriviaNivelService } from '../../application/services/trivia-nivel.service';
import { TriviaNivel } from '../../domain/models/trivia-nivel.model';
import { TriviaCategoriaService } from '../../../trivia-categorias/application/services/trivia-categoria.service';
import { TriviaCategoria } from '../../../trivia-categorias/domain/models/trivia-categoria.model';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-trivia-niveles',
  imports: [NgIcon, PageTitle, RouterLink, FormsModule],
  templateUrl: './trivia-niveles.html',
  styles: ``
})
export class TriviaNiveles implements OnInit {
  private nivelService     = inject(TriviaNivelService);
  private categoriaService = inject(TriviaCategoriaService);
  private toast             = inject(ToastService);
  private route             = inject(ActivatedRoute);

  categorias        = signal<TriviaCategoria[]>([]);
  niveles           = signal<TriviaNivel[]>([]);
  categoriaId       = signal<number | null>(null);
  loadingCategorias = signal(true);
  loadingNiveles    = signal(false);
  error             = signal(false);
  forbidden         = signal(false);

  ngOnInit(): void {
    const preseleccionada = Number(this.route.snapshot.queryParamMap.get('categoria_id')) || null;

    this.categoriaService.getAll({ pageSize: 200 }).subscribe({
      next: (res) => {
        this.categorias.set(res.data);
        this.loadingCategorias.set(false);
        const idInicial = preseleccionada ?? res.data[0]?.id ?? null;
        if (idInicial) this.onCategoriaChange(idInicial);
      },
      error: () => { this.loadingCategorias.set(false); }
    });
  }

  onCategoriaChange(id: number | string): void {
    const categoriaId = Number(id);
    this.categoriaId.set(categoriaId);
    this.loadingNiveles.set(true);
    this.error.set(false);
    this.forbidden.set(false);

    this.nivelService.getByCategoria(categoriaId).subscribe({
      next: (niveles) => { this.niveles.set(niveles); this.loadingNiveles.set(false); },
      error: (err: HttpErrorResponse) => {
        this.loadingNiveles.set(false);
        if (err.status === 403) this.forbidden.set(true);
        else this.error.set(true);
      }
    });
  }

  deleteNivel(id: number): void {
    Swal.fire({ title: '¿Eliminar nivel?', text: 'Las preguntas asociadas dejarán de estar disponibles.', icon: 'warning',
      showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
    }).then(result => {
      if (result.isConfirmed) {
        this.nivelService.delete(id).subscribe({
          next: () => {
            this.toast.success('¡Eliminado!', 'El nivel ha sido eliminado');
            const categoriaId = this.categoriaId();
            if (categoriaId) this.onCategoriaChange(categoriaId);
          },
          error: () => this.toast.error('Error', 'No se pudo eliminar el nivel')
        });
      }
    });
  }
}
