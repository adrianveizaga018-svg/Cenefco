import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { Curso } from '../../../cursos/domain/models/curso.model';

@Component({
  selector: 'app-oferta-academica',
  standalone: true,
  imports: [CommonModule, NgIcon, PageTitle],
  templateUrl: './oferta-academica.html',
  styles: ``
})
export class OfertaAcademicaComponent implements OnInit {
  private http = inject(HttpClient);
  
  cursos = signal<Curso[]>([]);
  loading = signal(true);
  error = signal('');
  
  cursoSeleccionado = signal<Curso | null>(null);
  showModal = signal(false);
  showComprobanteModal = signal(false);

  ngOnInit() {
    this.cargarOferta();
  }

  cargarOferta() {
    this.loading.set(true);
    this.http.get<{ data: Curso[] }>('/api/v1/public/cursos').subscribe({
      next: (res) => {
        this.cursos.set(res.data);
        this.loading.set(false);
      },
      error: (err) => {
        this.error.set('No se pudo cargar el catálogo de cursos.');
        this.loading.set(false);
      }
    });
  }

  abrirDetalles(curso: Curso) {
    this.cursoSeleccionado.set(curso);
    this.showModal.set(true);
  }
  
  cerrarDetalles() {
    this.showModal.set(false);
    this.cursoSeleccionado.set(null);
  }

  abrirInscripcion(curso: Curso) {
    this.cerrarDetalles();
    this.cursoSeleccionado.set(curso);
    this.showComprobanteModal.set(true);
  }
  
  cerrarInscripcion() {
    this.showComprobanteModal.set(false);
    this.cursoSeleccionado.set(null);
  }
  
  getImageUrl(path: string | null): string {
    if (!path) return 'assets/images/placeholder.jpg';
    if (path.startsWith('http')) return path;
    return `/storage/${path}`;
  }
}
