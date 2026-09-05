import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core'
import { RouterLink } from '@angular/router'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucideSettings, lucideToggleLeft, lucideToggleRight, lucidePlus, lucideTrash2, lucideLoader } from '@ng-icons/lucide'
import Swal from 'sweetalert2'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { ToastService } from '../../../common/application/services/toast.service'
import { CertConfigProgramaService } from '../../application/services/cert-config-programa.service'
import { CertConfigPrograma } from '../../domain/models/cert-config-programa.model'

@Component({
  selector: 'app-cert-config-programas',
  standalone: true,
  imports: [RouterLink, NgIcon, PageTitle],
  viewProviders: [provideIcons({ lucideSettings, lucideToggleLeft, lucideToggleRight, lucidePlus, lucideTrash2, lucideLoader })],
  templateUrl: './cert-config-programas.html',
})
export class CertConfigProgramas implements OnInit {
  private svc  = inject(CertConfigProgramaService)
  private toast = inject(ToastService)
  private cdr   = inject(ChangeDetectorRef)

  configs   = signal<CertConfigPrograma[]>([])
  loading   = signal(true)
  toggling  = signal<number | null>(null)
  deleting  = signal<number | null>(null)

  ngOnInit() {
    this.load()
  }

  load() {
    this.svc.getAll().subscribe({
      next: (res) => {
        this.configs.set(res.data)
        this.loading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar la lista.')
        this.loading.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  toggle(config: CertConfigPrograma) {
    this.toggling.set(config.id)
    this.svc.toggle(config.id).subscribe({
      next: (updated) => {
        this.configs.update(list => list.map(c => c.id === updated.id ? updated : c))
        this.toggling.set(null)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cambiar el estado.')
        this.toggling.set(null)
        this.cdr.detectChanges()
      }
    })
  }

  eliminar(config: CertConfigPrograma): void {
    Swal.fire({
      title: '¿Eliminar configuración?',
      text: `Se eliminará la configuración de certificados de "${config.nombre_programa ?? 'Programa #' + config.programa_id}" y todos sus certificados ofrecidos. Esta acción no se puede deshacer.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(result => {
      if (!result.isConfirmed) return
      this.deleting.set(config.id)
      this.svc.delete(config.id).subscribe({
        next: () => {
          this.configs.update(list => list.filter(c => c.id !== config.id))
          this.deleting.set(null)
          this.toast.success('Eliminado', 'La configuración fue eliminada.')
          this.cdr.detectChanges()
        },
        error: (err) => {
          this.toast.error('Error', err?.error?.error ?? 'No se pudo eliminar la configuración.')
          this.deleting.set(null)
          this.cdr.detectChanges()
        }
      })
    })
  }
}
