import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core'
import { FormsModule } from '@angular/forms'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucidePlus, lucideCheck, lucideLoader, lucideCalendarClock } from '@ng-icons/lucide'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { SearchableSelect, SelectOption } from '../../../common/components/searchable-select/searchable-select'
import { ToastService } from '../../../common/application/services/toast.service'
import { GastoService } from '../../application/services/gasto.service'
import { GastoRecurrente } from '../../domain/models/gasto.model'

@Component({
  selector: 'app-gastos-recurrentes',
  standalone: true,
  imports: [FormsModule, NgIcon, PageTitle, SearchableSelect],
  viewProviders: [provideIcons({ lucidePlus, lucideCheck, lucideLoader, lucideCalendarClock })],
  templateUrl: './gastos-recurrentes.html',
})
export class GastosRecurrentes implements OnInit {
  private svc   = inject(GastoService)
  private toast = inject(ToastService)
  private cdr   = inject(ChangeDetectorRef)

  recurrentes = signal<GastoRecurrente[]>([])
  categoriaOptions = signal<SelectOption[]>([])
  loading     = signal(true)
  confirming  = signal<number | null>(null)
  creating    = signal(false)

  nuevo = signal({ categoria_gasto_id: null as number | null, concepto: '', monto: 0, dia_del_mes: 1 })

  ngOnInit() {
    this.cargarCategorias()
    this.load()
  }

  private cargarCategorias() {
    this.svc.getCategorias().subscribe({
      next: (cats) => {
        this.categoriaOptions.set(cats.map(c => ({ value: c.id, label: c.nombre })))
        this.cdr.detectChanges()
      },
      error: () => {}
    })
  }

  load() {
    this.loading.set(true)
    this.svc.getRecurrentes().subscribe({
      next: (res) => {
        this.recurrentes.set(res)
        this.loading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar la lista de gastos recurrentes.')
        this.loading.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  crear() {
    const n = this.nuevo()
    if (!n.categoria_gasto_id || !n.concepto || n.monto <= 0) {
      this.toast.error('Datos incompletos', 'Completa categoría, concepto y monto.')
      return
    }
    this.creating.set(true)
    this.svc.createRecurrente({
      categoria_gasto_id: n.categoria_gasto_id,
      concepto: n.concepto,
      monto: n.monto,
      dia_del_mes: n.dia_del_mes,
    }).subscribe({
      next: (nuevo) => {
        this.recurrentes.update(list => [...list, nuevo])
        this.nuevo.set({ categoria_gasto_id: null, concepto: '', monto: 0, dia_del_mes: 1 })
        this.creating.set(false)
        this.toast.success('Creado', 'Gasto recurrente creado.')
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo crear el gasto recurrente.')
        this.creating.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  confirmando          = signal<GastoRecurrente | null>(null)
  comprobanteConfirmar = signal<File | null>(null)

  abrirConfirmar(recurrente: GastoRecurrente) {
    this.comprobanteConfirmar.set(null)
    this.confirmando.set(recurrente)
  }

  cerrarConfirmar() {
    this.confirmando.set(null)
  }

  onComprobanteConfirmarSeleccionado(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null
    this.comprobanteConfirmar.set(file)
  }

  confirmar() {
    const recurrente = this.confirmando()
    if (!recurrente) return

    this.confirming.set(recurrente.id)
    const hoy = new Date().toISOString().substring(0, 10)
    this.svc.confirmarRecurrente(recurrente.id, hoy, this.comprobanteConfirmar()).subscribe({
      next: () => {
        this.toast.success('Confirmado', `Se registró el gasto de "${recurrente.concepto}" del mes actual.`)
        this.confirming.set(null)
        this.confirmando.set(null)
        this.load()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo confirmar el gasto recurrente.')
        this.confirming.set(null)
        this.cdr.detectChanges()
      }
    })
  }

  yaConfirmadoEsteMes(recurrente: GastoRecurrente): boolean {
    if (!recurrente.ultima_confirmacion) return false
    const hoy = new Date()
    const ultima = new Date(recurrente.ultima_confirmacion)
    return ultima.getFullYear() === hoy.getFullYear() && ultima.getMonth() === hoy.getMonth()
  }

  updateNuevo(field: string, value: any) {
    this.nuevo.update(n => ({ ...n, [field]: value }))
  }
}
