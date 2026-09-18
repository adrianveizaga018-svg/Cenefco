import { Component, inject, signal, OnInit } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { ProgramaAcademicoService } from '../../application/services/programa-academico.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';

@Component({
  selector: 'app-programa-academico-edit',
  imports: [RouterLink, NgIcon, PageTitle],
  templateUrl: './programa-academico-edit.html',
})
export class ProgramaAcademicoEdit implements OnInit {
  private service = inject(ProgramaAcademicoService);
  private toast = inject(ToastService);
  private router = inject(Router);
  private route = inject(ActivatedRoute);

  loading = signal(true);
  id = Number(this.route.snapshot.paramMap.get('id'));
  slug = signal<string | null>(null);
  nombrePrograma = signal('');
  imparticiones = signal<any[]>([]);

  ngOnInit() {
    this.service.getById(this.id).subscribe({
      next: (d) => {
        this.nombrePrograma.set(d.nombre_programa ?? '');
        this.slug.set((d as any).slug ?? null);
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar');
        this.router.navigate(['/cenefco/programas-academicos']);
      },
    });
    this.service.getImparticiones(this.id).subscribe(res => this.imparticiones.set(res));
  }
}
