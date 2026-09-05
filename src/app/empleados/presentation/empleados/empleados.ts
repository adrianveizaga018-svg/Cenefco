import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core'
import { FormsModule } from '@angular/forms'
import { RouterLink } from '@angular/router'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucidePencil, lucideTrash2, lucidePlus, lucideLoader, lucideToggleLeft, lucideToggleRight } from '@ng-icons/lucide'
import Swal from 'sweetalert2'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { Pagination } from '../../../common/components/pagination/pagination'
import { ToastService } from '../../../common/application/services/toast.service'
import { EmpleadoService } from '../../application/services/empleado.service'
import { Empleado } from '../../domain/models/empleado.model'

@Component({
  selector: 'app-empleados',
  standalone: true,
  imports: [FormsModule, RouterLink, NgIcon, PageTitle, Pagination],
  viewProviders: [provideIcons({ lucidePencil, lucideTrash2, lucidePlus, lucideLoader, lucideToggleLeft, lucideToggleRight })],
  templateUrl: './empleados.html',
})
export class Empleados implements OnInit {
  private svc   = inject(EmpleadoService)
  private toast = inject(ToastService)
  private cdr   = inject(ChangeDetectorRef)

  empleados   = signal<Empleado[]>([])
  loading     = signal(true)
  deleting    = signal<number | null>(null)

  currentPage = signal(1)
  pageSize    = 15
  total       = signal(0)
  query       = signal('')

  ngOnInit() {
    this.load()
  }

  load() {
    this.loading.set(true)
    this.svc.getAll({ pageIndex: this.currentPage(), pageSize: this.pageSize, query: this.query() }).subscribe({
      next: (res) => {
        this.empleados.set(res.data)
        this.total.set(res.total)
        this.loading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar la lista de empleados.')
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

  toggleActivo(empleado: Empleado) {
    this.svc.update(empleado.id, { activo: !empleado.activo }).subscribe({
      next: (actualizado) => {
        this.empleados.update(list => list.map(e => e.id === actualizado.id ? actualizado : e))
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cambiar el estado.')
      }
    })
  }

  eliminar(empleado: Empleado): void {
    Swal.fire({
      title: '¿Eliminar empleado?',
      text: `Se eliminará a "${empleado.nombre_completo}" de la planilla de personal.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(result => {
      if (!result.isConfirmed) return
      this.deleting.set(empleado.id)
      this.svc.delete(empleado.id).subscribe({
        next: () => {
          this.toast.success('Eliminado', 'El empleado fue eliminado.')
          this.deleting.set(null)
          this.load()
        },
        error: (err) => {
          this.toast.error('Error', err?.error?.error ?? 'No se pudo eliminar el empleado.')
          this.deleting.set(null)
          this.cdr.detectChanges()
        }
      })
    })
  }
}
