import { Component, inject, signal, OnInit, computed } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { EstudianteService, EstudianteDashboardResponse, PlanPagoEstudiante } from '../../application/services/estudiante.service';

@Component({
  selector: 'app-estudiante-dashboard',
  standalone: true,
  imports: [CommonModule, RouterLink, NgIcon],
  templateUrl: './estudiante-dashboard.html',
  styles: ``
})
export class EstudianteDashboardComponent implements OnInit {
  private service = inject(EstudianteService);
  data = signal<EstudianteDashboardResponse | null>(null);
  planes = signal<PlanPagoEstudiante[]>([]);

  // Alertas computadas a partir de los planes de pago
  readonly alertasVencidas = computed(() =>
    this.planes().filter(p => p.estado_pago !== 'pagado' && p.saldo > 0 &&
      p.cuotas.some(c => !c.pagado && c.fecha_limite && new Date(c.fecha_limite) < new Date())
    )
  );

  readonly alertasProximas = computed(() =>
    this.planes().filter(p => p.estado_pago !== 'pagado' && p.saldo > 0 &&
      p.cuotas.some(c => {
        if (c.pagado || !c.fecha_limite) return false;
        const limite = new Date(c.fecha_limite);
        const hoy = new Date();
        const diff = (limite.getTime() - hoy.getTime()) / (1000 * 60 * 60 * 24);
        return diff >= 0 && diff <= 7;
      })
    ).filter(p => !this.alertasVencidas().includes(p)) // Excluir ya vencidas
  );

  readonly tieneAlertas = computed(() => this.alertasVencidas().length > 0 || this.alertasProximas().length > 0);

  ngOnInit(): void {
    this.service.getDashboard().subscribe({
      next: res => this.data.set(res),
      error: () => {}
    });
    this.service.getMisPagos().subscribe({
      next: res => this.planes.set(res.data),
      error: () => {}
    });
  }
}
