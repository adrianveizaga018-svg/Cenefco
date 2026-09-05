import { Component, inject, signal } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { SlicePipe } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { CartaGeneradaService } from '../../application/services/carta-generada.service';
import { CartaGenerada } from '../../domain/models/carta-generada.model';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';

interface AlumnoInfo  { id_us: number; nombre: string; appaterno: string | null; ci: string | null; }
interface ModeloInfo  { id_cartamod: number; nombremodelo: string; }

@Component({
  selector: 'app-carta-generada-detail',
  imports: [RouterLink, NgIcon, PageTitle, SlicePipe],
  templateUrl: './carta-generada-detail.html'
})
export class CartaGeneradaDetail {
  private service = inject(CartaGeneradaService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private http    = inject(HttpClient);

  loading      = signal(true);
  carta        = signal<CartaGenerada | null>(null);
  alumno       = signal<AlumnoInfo | null>(null);
  modelo       = signal<ModeloInfo | null>(null);
  fechaHoy     = new Date().toLocaleDateString('es-BO', { year: 'numeric', month: 'long', day: 'numeric' });

  id = Number(this.route.snapshot.paramMap.get('id'));

  constructor() {
    this.service.getById(this.id).subscribe({
      next: (d) => {
        this.carta.set(d);
        this.loading.set(false);

        if (d.id_us) {
          this.http.get<{ data: AlumnoInfo[] }>('/api/v1/usuarios-academicos', {
            params: { pageSize: '300', pageIndex: '1', conInactivos: 'true' }
          }).subscribe({ next: r => this.alumno.set(r.data.find(a => a.id_us === d.id_us) ?? null) });
        }

        if (d.id_cartamod) {
          this.http.get<ModeloInfo>(`/api/v1/cartas-modelo/${d.id_cartamod}`)
            .subscribe({ next: m => this.modelo.set(m) });
        }
      },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/cartas-generadas']); }
    });
  }

  nombreAlumno(): string {
    const a = this.alumno();
    if (!a) return '—';
    return `${a.nombre} ${a.appaterno ?? ''}`.trim();
  }

  imprimir(): void { window.print(); }
}
