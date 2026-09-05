import { Component, ChangeDetectorRef, inject, signal, OnInit } from '@angular/core'
import { CommonModule } from '@angular/common'
import { FormsModule } from '@angular/forms'
import { HttpErrorResponse } from '@angular/common/http'
import { ActivatedRoute, RouterLink } from '@angular/router'
import { NgIcon, provideIcons } from '@ng-icons/core'
import {
  lucidePencil, lucideTrash2, lucidePlus, lucideLoader, lucideUserPlus, lucideUsers,
  lucideSheet, lucideUpload, lucideDownload, lucideX, lucideCheck, lucideSearch,
  lucideArrowLeft, lucideLock, lucideCircleX, lucideTriangleAlert,
  lucideCalendarClock, lucideCalendarCheck, lucideActivity, lucidePhone
} from '@ng-icons/lucide'
import Swal from 'sweetalert2'
import * as XLSX from 'xlsx'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { Pagination } from '../../../common/components/pagination/pagination'
import { ToastService } from '../../../common/application/services/toast.service'
import { CampanaLeadService } from '../../application/services/campana-lead.service'
import { LeadService } from '../../application/services/lead.service'
import { CampanaLead, ImportarLeadsResult, Lead } from '../../domain/models/campana-lead.model'

const ESTADO_BADGE: Record<string, string> = {
  activa: 'bg-success/10 text-success',
  cerrada: 'bg-default-200 text-default-600',
}

@Component({
  selector: 'app-campana-lead-detail',
  standalone: true,
  imports: [CommonModule, FormsModule, NgIcon, RouterLink, PageTitle, Pagination],
  viewProviders: [provideIcons({
    lucidePencil, lucideTrash2, lucidePlus, lucideLoader, lucideUserPlus, lucideUsers,
    lucideSheet, lucideUpload, lucideDownload, lucideX, lucideCheck, lucideSearch,
    lucideArrowLeft, lucideLock, lucideCircleX, lucideTriangleAlert,
    lucideCalendarClock, lucideCalendarCheck, lucideActivity, lucidePhone
  })],
  templateUrl: './campana-lead-detail.html',
})
export class CampanaLeadDetail implements OnInit {
  private campanaSvc = inject(CampanaLeadService)
  private leadSvc     = inject(LeadService)
  private route       = inject(ActivatedRoute)
  private toast       = inject(ToastService)
  private cdr         = inject(ChangeDetectorRef)

  campanaId = 0
  campana   = signal<CampanaLead | null>(null)
  isLoading = signal(true)
  hasError  = signal(false)
  forbidden = signal(false)

  leads       = signal<Lead[]>([])
  leadsLoading = signal(true)
  currentPage = signal(1)
  pageSize    = 20
  total       = signal(0)
  searchQuery = signal('')

  estadoBadgeClass(e: string): string {
    return ESTADO_BADGE[e] ?? 'bg-default-200 text-default-600'
  }

  ngOnInit(): void {
    this.campanaId = Number(this.route.snapshot.paramMap.get('id'))
    this.cargarCampana()
    this.cargarLeads()
  }

  private cargarCampana(): void {
    this.campanaSvc.getById(this.campanaId).subscribe({
      next: (data) => {
        this.campana.set(data)
        this.isLoading.set(false)
        this.cdr.detectChanges()
      },
      error: (err: HttpErrorResponse) => {
        if (err.status === 403) {
          this.forbidden.set(true)
        } else {
          this.toast.error('Error', 'No se pudo cargar la campaña de leads.')
          this.hasError.set(true)
        }
        this.isLoading.set(false)
        this.cdr.detectChanges()
      },
    })
  }

  private cargarLeads(): void {
    this.leadsLoading.set(true)
    this.leadSvc.getAll(this.campanaId, {
      pageIndex: this.currentPage(),
      pageSize: this.pageSize,
      query: this.searchQuery(),
    }).subscribe({
      next: (res) => {
        this.leads.set(res.data)
        this.total.set(res.total)
        this.leadsLoading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar la lista de leads.')
        this.leadsLoading.set(false)
        this.cdr.detectChanges()
      },
    })
  }

  onPageChange(page: number): void {
    this.currentPage.set(page)
    this.cargarLeads()
  }

  onBuscar(valor: string): void {
    this.searchQuery.set(valor)
    this.currentPage.set(1)
    this.cargarLeads()
  }

  private refrescarContador(): void {
    this.campanaSvc.getById(this.campanaId).subscribe({
      next: (data) => { this.campana.set(data); this.cdr.detectChanges() },
    })
  }

  modalAbierto = signal(false)
  nuevoNombre  = signal('')
  nuevoCelular = signal('')
  nuevoCorreo  = signal('')
  nuevaProfesion = signal('')
  guardando    = signal(false)

  abrirModal(): void {
    this.nuevoNombre.set('')
    this.nuevoCelular.set('')
    this.nuevoCorreo.set('')
    this.nuevaProfesion.set('')
    this.modalAbierto.set(true)
  }

  cerrarModal(): void {
    this.modalAbierto.set(false)
  }

  guardarLead(): void {
    const nombre  = this.nuevoNombre().trim()
    const celular = this.nuevoCelular().trim()
    if (!nombre || !celular) return

    this.guardando.set(true)
    this.leadSvc.create(this.campanaId, {
      nombre,
      celular,
      correo: this.nuevoCorreo().trim() || null,
      profesion: this.nuevaProfesion().trim() || null,
    }).subscribe({
      next: () => {
        this.guardando.set(false)
        this.modalAbierto.set(false)
        this.toast.success('Listo', 'Lead agregado correctamente.')
        this.cargarLeads()
        this.refrescarContador()
      },
      error: (err) => {
        this.guardando.set(false)
        this.toast.error('Error', err?.error?.message ?? 'No se pudo agregar el lead.')
        this.cdr.detectChanges()
      },
    })
  }

  editando       = signal<Lead | null>(null)
  editNombre     = signal('')
  editCelular    = signal('')
  editCorreo     = signal('')
  editProfesion  = signal('')
  editGuardando  = signal(false)

  abrirEditar(l: Lead): void {
    this.editando.set(l)
    this.editNombre.set(l.nombre)
    this.editCelular.set(l.celular)
    this.editCorreo.set(l.correo ?? '')
    this.editProfesion.set(l.profesion ?? '')
  }

  cerrarEditar(): void {
    this.editando.set(null)
  }

  guardarEdicion(): void {
    const l = this.editando()
    const nombre  = this.editNombre().trim()
    const celular = this.editCelular().trim()
    if (!l || !nombre || !celular) return

    this.editGuardando.set(true)
    this.leadSvc.update(this.campanaId, l.id, {
      nombre,
      celular,
      correo: this.editCorreo().trim() || null,
      profesion: this.editProfesion().trim() || null,
    }).subscribe({
      next: (actualizado) => {
        this.leads.update(list => list.map(x => x.id === actualizado.id ? actualizado : x))
        this.editGuardando.set(false)
        this.editando.set(null)
        this.toast.success('Listo', 'Lead actualizado.')
        this.cdr.detectChanges()
      },
      error: (err) => {
        this.editGuardando.set(false)
        this.toast.error('Error', err?.error?.message ?? 'No se pudo actualizar el lead.')
        this.cdr.detectChanges()
      },
    })
  }

  eliminando = signal<number | null>(null)

  confirmarEliminar(l: Lead): void {
    Swal.fire({
      title: '¿Eliminar lead?',
      text: `Se eliminará a "${l.nombre}". Esta acción no se puede deshacer.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(result => {
      if (!result.isConfirmed) return
      this.eliminando.set(l.id)
      this.leadSvc.delete(this.campanaId, l.id).subscribe({
        next: () => {
          this.eliminando.set(null)
          this.toast.success('Listo', 'Lead eliminado.')
          this.cargarLeads()
          this.refrescarContador()
        },
        error: () => {
          this.eliminando.set(null)
          this.toast.error('Error', 'No se pudo eliminar el lead.')
          this.cdr.detectChanges()
        },
      })
    })
  }

  modalExcelAbierto = signal(false)
  importandoExcel   = signal(false)
  resultadoExcel    = signal<ImportarLeadsResult | null>(null)
  archivoExcel      = signal<File | null>(null)

  abrirModalExcel(): void {
    this.archivoExcel.set(null)
    this.resultadoExcel.set(null)
    this.modalExcelAbierto.set(true)
  }

  cerrarModalExcel(): void {
    this.modalExcelAbierto.set(false)
  }

  onArchivoExcelSeleccionado(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null
    this.archivoExcel.set(file)
    this.resultadoExcel.set(null)
  }

  descargarPlantillaExcel(): void {
    const hoja = XLSX.utils.aoa_to_sheet([
      ['Nombre', 'Celular', 'Correo', 'Profesion'],
      ['Juan Perez Rodriguez', '70011122', 'juan.perez@correo.com', 'Ingeniero Comercial'],
    ])
    const libro = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(libro, hoja, 'Leads')
    XLSX.writeFile(libro, 'plantilla-leads.xlsx')
  }

  importarExcel(): void {
    const archivo = this.archivoExcel()
    if (!archivo) return

    this.importandoExcel.set(true)
    this.resultadoExcel.set(null)

    this.leadSvc.importarExcel(this.campanaId, archivo).subscribe({
      next: (res) => {
        this.resultadoExcel.set(res)
        this.importandoExcel.set(false)

        if (res.insertados > 0) {
          this.currentPage.set(1)
          this.cargarLeads()
          this.refrescarContador()
          this.toast.success('Importación completa', `${res.insertados} lead(s) agregado(s).`)
        } else {
          this.toast.warning('Sin cambios', 'Ningún lead fue insertado. Revisa los errores.')
        }
        this.cdr.detectChanges()
      },
      error: (err) => {
        this.importandoExcel.set(false)
        this.toast.error('Error', err?.error?.message ?? 'No se pudo procesar el archivo.')
        this.cdr.detectChanges()
      },
    })
  }
}
