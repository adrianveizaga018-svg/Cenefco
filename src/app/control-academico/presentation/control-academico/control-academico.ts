import { CommonModule } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { CursoProgresoTareas } from '../../domain/models/tarea-academica.model';
import { TareaAcademicaService } from '../../application/services/tarea-academica.service';

@Component({
  selector: 'app-control-academico',
  standalone: true,
  imports: [CommonModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './control-academico.html'
})
export default class ControlAcademicoComponent implements OnInit {
  private tareaService = inject(TareaAcademicaService);

  cursos = signal<CursoProgresoTareas[]>([]);
  isLoading = signal<boolean>(true);
  hasError = signal<boolean>(false);

  ngOnInit(): void {
    this.loadData();
  }

  loadData() {
    this.isLoading.set(true);
    this.hasError.set(false);
    this.tareaService.getControlAcademicoDashboard().subscribe({
      next: (res) => {
        this.cursos.set(res);
        this.isLoading.set(false);
      },
      error: (err) => {
        console.error(err);
        this.hasError.set(true);
        this.isLoading.set(false);
      }
    });
  }
}
