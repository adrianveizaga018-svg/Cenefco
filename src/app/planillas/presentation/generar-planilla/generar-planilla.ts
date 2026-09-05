import { Component, inject, signal, computed, ChangeDetectorRef, OnInit } from '@angular/core'
import { toObservable, toSignal } from '@angular/core/rxjs-interop'
import { DecimalPipe } from '@angular/common'
import { FormsModule } from '@angular/forms'
import { Router, RouterLink } from '@angular/router'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucidePlayCircle, lucideLoader, lucideUsers, lucideChevronDown, lucideMinusCircle, lucidePlusCircle, lucideCirclePlus } from '@ng-icons/lucide'
import { catchError, debounceTime, map, of, startWith, switchMap } from 'rxjs'
import Swal from 'sweetalert2'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { ToastService } from '../../../common/application/services/toast.service'
import { PlanillaService } from '../../application/services/planilla.service'
import { PlanillaPreviewItem } from '../../domain/models/planilla.model'

const MESES = [
  { valor: 1,  nombre: 'Enero' },
  { valor: 2,  nombre: 'Febrero' },
  { valor: 3,  nombre: 'Marzo' },
  { valor: 4,  nombre: 'Abril' },
  { valor: 5,  nombre: 'Mayo' },
  { valor: 6,  nombre: 'Junio' },
  { valor: 7,  nombre: 'Julio' },
  { valor: 8,  nombre: 'Agosto' },
  { valor: 9,  nombre: 'Septiembre' },
  { valor: 10, nombre: 'Octubre' },
  { valor: 11, nombre: 'Noviembre' },
  { valor: 12, nombre: 'Diciembre' },
]

type S = { type: 'loading' } | { type: 'success'; items: PlanillaPreviewItem[] } | { type: 'error' };

@Component({
  selector: 'app-generar-planilla',
  standalone: true,
  imports: [FormsModule, NgIcon, PageTitle, RouterLink, DecimalPipe],
  viewProviders: [provideIcons({ lucidePlayCircle, lucideLoader, lucideUsers, lucideChevronDown, lucideMinusCircle, lucidePlusCircle, lucideCirclePlus })],
  templateUrl: './generar-planilla.html',
})
export class GenerarPlanilla implements OnInit {
  private planillaSvc = inject(PlanillaService)
  private toast       = inject(ToastService)
  private router      = inject(Router)
  private cdr         = inject(ChangeDetectorRef)

  generando  = signal(false)
  expandidos = signal<Record<number, boolean>>({})

  hoy   = new Date()
  anio  = signal(this.hoy.getFullYear())
  mes   = signal(this.hoy.getMonth() + 1)

  meses = MESES
  anios = Array.from({ length: 6 }, (_, i) => this.hoy.getFullYear() - 2 + i)

  private params = computed(() => ({ anio: this.anio(), mes: this.mes() }))

  private state = toSignal(
    toObservable(this.params).pipe(
      debounceTime(150),
      switchMap(p => this.planillaSvc.preview(p.anio, p.mes).pipe(
        map(r => ({ type: 'success', items: r.data } as S)),
        startWith({ type: 'loading' } as S),
        catchError(() => of({ type: 'error' } as S)),
      )),
      startWith({ type: 'loading' } as S),
    ),
    { requireSync: true }
  )

  get items()     { const s = this.state(); return s.type === 'success' ? s.items : [] }
  get isLoading() { return this.state().type === 'loading' }
  get isError()   { return this.state().type === 'error' }

  totalPlanilla = () => this.items.reduce((acc, e) => acc + e.monto_neto, 0)
  totalConAjustes = () => this.items.filter(e => e.ajustes.length > 0).length

  nombreMes(mes: number): string {
    return this.meses.find(m => m.valor === mes)?.nombre ?? String(mes)
  }

  ngOnInit() {}

  toggleExpand(empleadoId: number): void {
    this.expandidos.update(cur => ({ ...cur, [empleadoId]: !cur[empleadoId] }))
  }

  isExpanded(empleadoId: number): boolean { return !!this.expandidos()[empleadoId] }

  generar() {
    const tieneAjustes = this.totalConAjustes() > 0
    Swal.fire({
      title: '¿Generar planilla del mes?',
      text: `Se generará un gasto por Bs. ${this.totalPlanilla().toFixed(2)} correspondiente a ${this.items.length} empleado(s) activo(s) para ${this.nombreMes(this.mes())} ${this.anio()}.` +
            (tieneAjustes ? ` Incluye ${this.totalConAjustes()} empleado(s) con descuentos/bonos aplicados.` : ''),
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      confirmButtonText: 'Sí, generar',
      cancelButtonText: 'Cancelar',
    }).then(result => {
      if (!result.isConfirmed) return
      this.generando.set(true)
      this.planillaSvc.generar({ anio: this.anio(), mes: this.mes() }).subscribe({
        next: () => {
          this.generando.set(false)
          this.toast.success('Planilla generada', 'El gasto de sueldos fue registrado correctamente.')
          this.router.navigate(['/cenefco/planillas'])
        },
        error: (err) => {
          this.toast.error('Error', err?.error?.message ?? 'No se pudo generar la planilla.')
          this.generando.set(false)
          this.cdr.detectChanges()
        }
      })
    })
  }
}
