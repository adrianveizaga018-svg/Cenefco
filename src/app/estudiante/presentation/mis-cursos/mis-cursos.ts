import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { EstudianteService, CursoEstudiante } from '../../application/services/estudiante.service';

@Component({
  selector: 'app-mis-cursos',
  standalone: true,
  imports: [CommonModule, NgIcon, PageTitle],
  template: `
    <div class="p-6 md:p-10 space-y-8 animate-in fade-in duration-500 pb-20">
      <app-page-title title="Mis Cursos e Imparticiones" subtitle="Accede a tus materias, docentes y grabaciones de Zoom"></app-page-title>

      @if (cargando()) {
        <div class="flex justify-center p-20">
          <div class="h-12 w-12 animate-spin rounded-full border-4 border-blue-500 border-t-transparent shadow-lg shadow-blue-500/20"></div>
        </div>
      } @else if (cursos().length === 0) {
        <div class="card p-16 text-center border border-default-200 rounded-3xl bg-white shadow-sm hover:shadow-xl transition-all">
          <div class="mx-auto h-24 w-24 bg-blue-50 text-blue-300 rounded-full flex items-center justify-center mb-6">
            <ng-icon name="lucideBookX" style="font-size: 56px;"></ng-icon>
          </div>
          <h3 class="text-xl font-bold text-default-900">Aún no tienes cursos</h3>
          <p class="text-sm text-default-500 max-w-sm mx-auto mt-2 leading-relaxed">Cuando te inscribas a un programa o curso, aparecerá aquí junto con su material y clases grabadas.</p>
        </div>
      } @else {
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
          @for (curso of cursos(); track curso.id_ins) {
            <div class="group card bg-white border border-default-200 hover:border-blue-300 rounded-[2rem] p-6 sm:p-8 space-y-6 shadow-sm hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
              
              <!-- Decoration -->
              <div class="absolute -right-12 -top-12 w-40 h-40 bg-gradient-to-br from-blue-50 to-transparent rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700 pointer-events-none"></div>

              <div class="space-y-4 relative z-10">
                <div class="flex flex-wrap items-center justify-between gap-2">
                  <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 shadow-inner">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    {{ curso.estado_inscripcion | uppercase }}
                  </span>
                  <span class="text-[10px] font-bold text-default-400 tracking-widest uppercase bg-default-100 px-2 py-0.5 rounded-md">ID: {{ curso.id_imp }}</span>
                </div>

                <h3 class="text-xl sm:text-2xl font-black text-default-900 leading-tight tracking-tight">
                  {{ curso.titulo_personalizado || curso.nombre_materia }}
                </h3>

                @if (curso.nombre_docente) {
                  <div class="text-sm font-medium text-default-600 flex items-center gap-2 bg-default-50 p-2 rounded-xl">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                      <ng-icon name="lucideUser" style="font-size: 16px;"></ng-icon>
                    </div>
                    <span>Prof. <strong class="text-default-900">{{ curso.nombre_docente }}</strong></span>
                  </div>
                }

                <div class="flex flex-col gap-2 text-xs font-semibold text-default-500 pt-2">
                  @if (curso.imparte_fecha_inicio) {
                    <div class="flex items-center gap-2">
                      <ng-icon name="lucideCalendarDays" class="text-blue-500 text-base"></ng-icon>
                      <span>Inició el {{ curso.imparte_fecha_inicio | date:'longDate' }}</span>
                    </div>
                  }
                  @if (curso.horas_academicas) {
                    <div class="flex items-center gap-2">
                      <ng-icon name="lucideClock" class="text-amber-500 text-base"></ng-icon>
                      <span>{{ curso.horas_academicas }} Horas Académicas</span>
                    </div>
                  }
                </div>
              </div>

              <!-- Seccion de Grabaciones Zoom -->
              <div class="space-y-3 pt-4 border-t border-default-100 relative z-10">
                <h4 class="text-xs font-black text-default-800 uppercase tracking-widest flex items-center gap-2">
                  <span class="flex items-center justify-center w-6 h-6 rounded-md bg-rose-100 text-rose-600">
                    <ng-icon name="lucideVideo" style="font-size: 14px;"></ng-icon>
                  </span>
                  Clases Grabadas
                </h4>

                @if (curso.grabaciones.length === 0) {
                  <div class="p-4 rounded-xl bg-default-50 border border-default-100 text-center">
                    <p class="text-xs text-default-400 font-medium">Las grabaciones aparecerán aquí cuando estén disponibles.</p>
                  </div>
                } @else {
                  <div class="space-y-2 max-h-[180px] overflow-y-auto pr-2 custom-scrollbar">
                    @for (g of curso.grabaciones; track g.id) {
                      <div class="group/play flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 rounded-xl bg-default-50 border border-default-100 hover:border-rose-200 hover:bg-rose-50/30 transition-all">
                        <div class="min-w-0">
                          <p class="font-bold text-sm text-default-800 truncate group-hover/play:text-rose-700 transition-colors">{{ g.tema || 'Clase Grabada' }}</p>
                          <p class="text-[10px] font-semibold text-default-400 uppercase tracking-wide mt-0.5">{{ g.recording_start | date:'dd/MM/yyyy HH:mm' }}</p>
                        </div>
                        <a [href]="g.play_url" target="_blank" class="shrink-0 px-4 py-2 rounded-lg bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 shadow-md shadow-rose-500/20 hover:shadow-lg hover:shadow-rose-500/40 transition-all inline-flex items-center justify-center gap-1.5 active:scale-95">
                          <ng-icon name="lucidePlay" style="font-size: 14px;"></ng-icon> Ver Clase
                        </a>
                      </div>
                    }
                  </div>
                }
              </div>
            </div>
          }
        </div>
      }
    </div>
  `
})
export class MisCursosComponent implements OnInit {
  private service = inject(EstudianteService);
  cursos = signal<CursoEstudiante[]>([]);
  cargando = signal(true);

  ngOnInit(): void {
    this.service.getMisCursos().subscribe({
      next: res => {
        this.cursos.set(res.data);
        this.cargando.set(false);
      },
      error: () => this.cargando.set(false)
    });
  }
}
