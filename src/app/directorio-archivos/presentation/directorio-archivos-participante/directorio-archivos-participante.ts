import { Component, inject, signal } from '@angular/core';
import { KeyValuePipe } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { DirectorioArchivoService } from '../../application/services/directorio-archivo.service';
import { ArchivoParticipante } from '../../domain/models/directorio-archivo.model';

type ApiState =
  | { type: 'loading' }
  | { type: 'success'; data: ArchivoParticipante }
  | { type: 'error' } | { type: 'forbidden' } | { type: 'not-found' };

@Component({
  selector: 'app-directorio-archivos-participante',
  imports: [NgIcon, PageTitle, RouterLink, KeyValuePipe],
  templateUrl: './directorio-archivos-participante.html',
  styles: ``
})
export class DirectorioArchivosParticipante {
  private route = inject(ActivatedRoute);
  private directorioService = inject(DirectorioArchivoService);

  idIns = Number(this.route.snapshot.paramMap.get('idIns'));
  idImp = Number(this.route.snapshot.paramMap.get('idImp'));

  private state = signal<ApiState>({ type: 'loading' });

  constructor() {
    this.directorioService.getArchivos(this.idIns).subscribe({
      next: (data) => this.state.set({ type: 'success', data }),
      error: (err) => {
        if (err.status === 403) this.state.set({ type: 'forbidden' });
        else if (err.status === 404) this.state.set({ type: 'not-found' });
        else this.state.set({ type: 'error' });
      },
    });
  }

  get isLoading()  { return this.state().type === 'loading'; }
  get error()      { return this.state().type === 'error'; }
  get forbidden()  { return this.state().type === 'forbidden'; }
  get notFound()   { return this.state().type === 'not-found'; }
  get participante() { const s = this.state(); return s.type === 'success' ? s.data : null; }
}
