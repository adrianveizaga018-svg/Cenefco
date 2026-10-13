import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { FilaRanking, RankingVentas, TablaComisionesService } from '../../../comisiones/application/services/tabla-comisiones.service';

const MESES = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

/** Ranking de vendedores del mes con su podio: para que el equipo compita sanamente. */
@Component({
  selector: 'app-ranking-ventas',
  standalone: true,
  imports: [FormsModule, NgIcon, PageTitle, DecimalPipe],
  templateUrl: './ranking.html',
})
export class RankingVentasComponent implements OnInit {
  private svc = inject(TablaComisionesService);
  private toast = inject(ToastService);

  mes = signal(new Date().toISOString().slice(0, 7));
  datos = signal<RankingVentas | null>(null);
  cargando = signal(true);

  readonly podio = computed(() => {
    const top = (this.datos()?.ranking ?? []).filter(f => f.puesto !== null && f.puesto <= 3);
    // Orden visual del podio: 2° · 1° · 3°
    return [top[1], top[0], top[2]].filter((f): f is FilaRanking => !!f);
  });
  readonly yo = computed(() => this.datos()?.ranking.find(f => f.es_yo) ?? null);
  readonly delante = computed(() => {
    const yo = this.yo();
    if (!yo?.puesto || yo.puesto === 1) return null;
    const anterior = this.datos()!.ranking.find(f => f.puesto === yo.puesto! - 1);
    return anterior ? { nombre: anterior.nombre, falta: Math.max(0, anterior.recaudado - yo.recaudado) } : null;
  });

  ngOnInit(): void { this.cargar(); }

  cargar(): void {
    this.cargando.set(true);
    this.svc.ranking(this.mes()).subscribe({
      next: (d) => { this.datos.set(d); this.cargando.set(false); },
      error: () => { this.cargando.set(false); this.toast.error('No se pudo cargar el ranking', 'Inténtalo de nuevo.'); },
    });
  }

  nombreMes(periodo: string): string {
    const [anio, mes] = periodo.split('-');
    return `${MESES[Number(mes) - 1]} ${anio}`;
  }

  medalla(puesto: number | null): string {
    return puesto === 1 ? '🥇' : puesto === 2 ? '🥈' : puesto === 3 ? '🥉' : '';
  }

  alto(puesto: number | null): string {
    return puesto === 1 ? 'h-36' : puesto === 2 ? 'h-28' : 'h-20';
  }

  iniciales(nombre: string): string {
    return nombre.split(' ').filter(Boolean).slice(0, 2).map(p => p[0]).join('').toUpperCase();
  }
}
