import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core'
import { ActivatedRoute, Router, RouterLink } from '@angular/router'
import { FormsModule } from '@angular/forms'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucideSave, lucideLoader, lucideFileText } from '@ng-icons/lucide'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { SearchableSelect, SelectOption } from '../../../common/components/searchable-select/searchable-select'
import { ToastService } from '../../../common/application/services/toast.service'
import { GastoService } from '../../application/services/gasto.service'
import { CampanaPublicidadService } from '../../../campanas-publicidad/application/services/campana-publicidad.service'

@Component({
  selector: 'app-gasto-edit',
  standalone: true,
  imports: [FormsModule, NgIcon, PageTitle, SearchableSelect, RouterLink],
  viewProviders: [provideIcons({ lucideSave, lucideLoader, lucideFileText })],
  templateUrl: './gasto-edit.html',
})
export class GastoEdit implements OnInit {
  private route = inject(ActivatedRoute)
  private router = inject(Router)
  private svc   = inject(GastoService)
  private campanaSvc = inject(CampanaPublicidadService)
  private toast = inject(ToastService)
  private cdr   = inject(ChangeDetectorRef)

  isNew   = signal(true)
  gastoId = signal<number | null>(null)
  loading = signal(true)
  saving  = signal(false)

  categoriaOptions = signal<SelectOption[]>([])
  campanaOptions    = signal<SelectOption[]>([])

  categoriaGastoId     = signal<number | null>(null)
  concepto             = signal('')
  monto                = signal(0)
  fecha                = signal(new Date().toISOString().substring(0, 10))
  responsable          = signal('')
  comprobanteFile      = signal<File | null>(null)
  comprobanteActualUrl = signal<string | null>(null)
  nota                 = signal('')
  campanaPublicidadId  = signal<number | null>(null)

  ngOnInit() {
    this.cargarCategorias()
    this.cargarCampanas()

    const id = this.route.snapshot.paramMap.get('id')
    if (!id || id === 'new') {
      this.isNew.set(true)
      this.loading.set(false)
      this.cdr.detectChanges()
      return
    }

    this.isNew.set(false)
    const numId = Number(id)
    this.gastoId.set(numId)
    this.svc.getById(numId).subscribe({
      next: (g) => {
        this.categoriaGastoId.set(g.categoria_gasto_id)
        this.concepto.set(g.concepto)
        this.monto.set(g.monto)
        this.fecha.set(g.fecha)
        this.responsable.set(g.responsable ?? '')
        this.comprobanteActualUrl.set(g.comprobante_url ?? null)
        this.nota.set(g.nota ?? '')
        this.campanaPublicidadId.set(g.campana_publicidad_id ?? null)
        this.loading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar el gasto.')
        this.loading.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  onComprobanteSeleccionado(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null
    this.comprobanteFile.set(file)
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

  private cargarCampanas() {
    this.campanaSvc.getAll({ pageSize: 100, sortKey: 'fecha_inicio', sortOrder: 'desc' }).subscribe({
      next: (res) => {
        this.campanaOptions.set(res.data.map(c => ({ value: c.id, label: c.nombre })))
        this.cdr.detectChanges()
      },
      error: () => {}
    })
  }

  save() {
    const categoriaId = this.categoriaGastoId()
    if (!categoriaId || !this.concepto() || this.monto() <= 0) {
      this.toast.error('Datos incompletos', 'Completa categoría, concepto y monto antes de guardar.')
      return
    }

    this.saving.set(true)

    const payload = {
      categoria_gasto_id: categoriaId,
      concepto:           this.concepto(),
      monto:              this.monto(),
      fecha:              this.fecha(),
      responsable:        this.responsable() || null,
      comprobante:        this.comprobanteFile(),
      nota:               this.nota() || null,
      campana_publicidad_id: this.campanaPublicidadId() || null,
    }

    const request = this.isNew()
      ? this.svc.create(payload)
      : this.svc.update(this.gastoId()!, payload)

    request.subscribe({
      next: () => {
        this.saving.set(false)
        this.toast.success('Guardado', this.isNew() ? 'Gasto registrado correctamente.' : 'Gasto actualizado.')
        this.router.navigate(['/cenefco/gastos'])
      },
      error: (err) => {
        this.toast.error('Error', err?.error?.message ?? 'No se pudo guardar el gasto.')
        this.saving.set(false)
        this.cdr.detectChanges()
      }
    })
  }
}
