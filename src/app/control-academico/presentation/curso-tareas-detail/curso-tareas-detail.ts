import { CommonModule } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { FormsModule } from '@angular/forms';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { TareaAcademica } from '../../domain/models/tarea-academica.model';
import { TareaAcademicaService } from '../../application/services/tarea-academica.service';

@Component({
  selector: 'app-curso-tareas-detail',
  standalone: true,
  imports: [CommonModule, RouterLink, NgIcon, FormsModule, PageTitle],
  templateUrl: './curso-tareas-detail.html'
})
export default class CursoTareasDetailComponent implements OnInit {
  private route = inject(ActivatedRoute);
  private tareaService = inject(TareaAcademicaService);

  cursoId = signal<number>(0);
  tareas = signal<TareaAcademica[]>([]);
  isLoading = signal<boolean>(true);
  isSubmitting = signal<boolean>(false);
  
  selectedFile: File | null = null;
  activeTareaId: number | null = null;

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.cursoId.set(+id);
      this.loadTareas();
    }
  }

  loadTareas() {
    this.isLoading.set(true);
    this.tareaService.getTareasByCurso(this.cursoId()).subscribe({
      next: (res) => {
        this.tareas.set(res);
        this.isLoading.set(false);
      },
      error: (err) => {
        console.error(err);
        this.isLoading.set(false);
      }
    });
  }

  get progreso(): number {
    const t = this.tareas();
    if (t.length === 0) return 0;
    const completadas = t.filter(x => x.estado === 'completada').length;
    return (completadas / t.length) * 100;
  }

  onFileSelected(event: any, tareaId: number) {
    const file = event.target.files[0];
    if (file) {
      this.selectedFile = file;
      this.activeTareaId = tareaId;
    }
  }

  marcarCompletada(tarea: TareaAcademica) {
    if (tarea.requiere_archivo && (!this.selectedFile || this.activeTareaId !== tarea.id)) {
      alert('Debes subir un archivo como evidencia para esta tarea.');
      return;
    }

    this.isSubmitting.set(true);
    this.tareaService.completarTarea(tarea.id, this.activeTareaId === tarea.id ? this.selectedFile : null).subscribe({
      next: () => {
        this.loadTareas();
        this.isSubmitting.set(false);
        this.selectedFile = null;
        this.activeTareaId = null;
      },
      error: (err) => {
        console.error(err);
        alert('Error al completar la tarea');
        this.isSubmitting.set(false);
      }
    });
  }

  getArchivoUrl(url: string): string {
    if (!url) return '#';
    // If it's already an absolute URL, use as-is
    if (url.startsWith('http://') || url.startsWith('https://')) return url;
    // Otherwise prefix with the Laravel storage base (proxied via /storage)
    return '/storage/' + url;
  }
}
