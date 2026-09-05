import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core'
import { FormsModule } from '@angular/forms'
import { RouterLink } from '@angular/router'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucidePencil, lucideTrash2, lucidePlus, lucideLoader, lucideReceipt } from '@ng-icons/lucide'
import Swal from 'sweetalert2'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { Pagination } from '../../../common/components/pagination/pagination'
import { SearchableSelect, SelectOption } from '../../../common/components/searchable-select/searchable-select'
import { ToastService } from '../../../common/application/services/toast.service'
import { GastoService } from '../../application/services/gasto.service'
import { Gasto, CategoriaGasto } from '../../domain/models/gasto.model'

@Component({
  selector: 'app-gastos',
  standalone: true,
  imports: [FormsModule, RouterLink, NgIcon, PageTitle, Pagination, SearchableSelect],
  viewProviders: [provideIcons({ lucidePencil, lucideTrash2, lucidePlus, lucideLoader, lucideReceipt })],
  templateUrl: './gastos.html',
})
export class Gastos implements OnInit {
  private svc   = inject(GastoService)
  private toast = inject(ToastService)
  private cdr   = inject(ChangeDetectorRef)

  gastos       = signal<Gasto[]>([])
  categorias   = signal<CategoriaGasto[]>([])
  categoriaOptions = signal<SelectOption[]>([])
  loading      = signal(true)
  deleting     = signal<number | null>(null)

  currentPage  = signal(1)
  pageSize     = 15
  total        = signal(0)
  query        = signal('')
  categoriaFiltro = signal<number | null>(null)

  ngOnInit() {
    this.cargarCategorias()
    this.load()
  }

  private cargarCategorias() {
    this.svc.getCategorias().subscribe({
      next: (cats) => {
        this.categorias.set(cats)
        this.categoriaOptions.set(cats.map(c => ({ value: c.id, label: c.nombre })))
        this.cdr.detectChanges()
      },
      error: () => {}
    })
  }

  load() {
    this.loading.set(true)
    this.svc.getAll({
      pageIndex: this.currentPage(),
      pageSize: this.pageSize,
      query: this.query(),
      categoria_gasto_id: this.categoriaFiltro() ?? undefined,
    }).subscribe({
      next: (res) => {
        this.gastos.set(res.data)
        this.total.set(res.total)
        this.loading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar la lista de gastos.')
        this.loading.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  onPageChange(page: number) {
    this.currentPage.set(page)
    this.load()
  }

  onBuscar(valor: string) {
    this.query.set(valor)
    this.currentPage.set(1)
    this.load()
  }

  onFiltrarCategoria(categoriaId: number | null) {
    this.categoriaFiltro.set(categoriaId)
    this.currentPage.set(1)
    this.load()
  }

  eliminar(gasto: Gasto): void {
    Swal.fire({
      title: '¿Eliminar gasto?',
      text: `Se eliminará el gasto "${gasto.concepto}" por Bs. ${gasto.monto}. Esta acción no se puede deshacer.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(result => {
      if (!result.isConfirmed) return
      this.deleting.set(gasto.id)
      this.svc.delete(gasto.id).subscribe({
        next: () => {
          this.toast.success('Eliminado', 'El gasto fue eliminado.')
          this.deleting.set(null)
          this.load()
        },
        error: (err) => {
          this.toast.error('Error', err?.error?.error ?? 'No se pudo eliminar el gasto.')
          this.deleting.set(null)
          this.cdr.detectChanges()
        }
      })
    })
  }
}
