import { Component, inject, signal, computed, ChangeDetectorRef, OnInit } from '@angular/core'
import { FormsModule } from '@angular/forms'
import { RouterLink } from '@angular/router'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucideCheck, lucideLoader, lucideCircleCheck, lucideSettings, lucideUpload, lucideX } from '@ng-icons/lucide'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { ToastService } from '../../../common/application/services/toast.service'
import { HonorarioService } from '../../application/services/honorario.service'
import { DocenteHonorarioSugerido } from '../../domain/models/honorario.model'
import { SueldoDocenteService } from '../../../sueldos-docentes/application/services/sueldo-docente.service'

interface CursoOption {
  id_programa: number
  nombre_curso: string
}

@Component({
  selector: 'app-honorarios-del-mes',
  standalone: true,
  imports: [FormsModule, RouterLink, NgIcon, PageTitle],
  viewProviders: [provideIcons({ lucideCheck, lucideLoader, lucideCircleCheck, lucideSettings, lucideUpload, lucideX })],
  templateUrl: './honorarios-del-mes.html',
})
export class HonorariosDelMes implements OnInit {
  private honorarioSvc = inject(HonorarioService)
  private sueldoSvc    = inject(SueldoDocenteService)
  private toast        = inject(ToastService)
  private cdr          = inject(ChangeDetectorRef)

  hoy  = new Date()
  anio = signal(this.hoy.getFullYear())
  mes  = signal(this.hoy.getMonth() + 1)

  loading   = signal(true)
  sugeridos = signal<DocenteHonorarioSugerido[]>([])

  cursoSeleccionado = signal<number | null>(null)

  cursos = computed<CursoOption[]>(() => {
    const vistos = new Map<number, CursoOption>()
    for (const item of this.sugeridos()) {
      if (!vistos.has(item.id_programa)) {
        vistos.set(item.id_programa, { id_programa: item.id_programa, nombre_curso: item.nombre_curso })
      }
    }
    return Array.from(vistos.values()).sort((a, b) => a.nombre_curso.localeCompare(b.nombre_curso))
  })

  docentesDelCurso = computed<DocenteHonorarioSugerido[]>(() => {
    const curso = this.cursoSeleccionado()
    if (curso == null) return []
    return this.sugeridos().filter(s => s.id_programa === curso)
  })

  modalAbierto  = signal(false)
  guardando     = signal(false)
  itemActivo    = signal<DocenteHonorarioSugerido | null>(null)
  montoForm     = signal(0)
  fechaPagoForm = signal(new Date().toISOString().split('T')[0])
  nroComprobanteForm = signal('')
  archivoForm: File | null = null

  ngOnInit() {
    this.load()
  }

  load() {
    this.loading.set(true)
    this.honorarioSvc.getSugeridosDelMes(this.anio(), this.mes()).subscribe({
      next: (res) => {
        this.sugeridos.set(res)
        if (this.cursoSeleccionado() == null && res.length > 0) {
          this.cursoSeleccionado.set(res[0].id_programa)
        }
        this.loading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar la lista de honorarios sugeridos.')
        this.loading.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  onCambiarPeriodo() {
    this.load()
  }

  onCambiarCurso(idPrograma: number) {
    this.cursoSeleccionado.set(idPrograma)
  }

  abrirModal(item: DocenteHonorarioSugerido) {
    this.itemActivo.set(item)
    this.montoForm.set(item.monto_sugerido)
    this.fechaPagoForm.set(new Date().toISOString().split('T')[0])
    this.nroComprobanteForm.set('')
    this.archivoForm = null
    this.modalAbierto.set(true)
  }

  cerrarModal() {
    this.modalAbierto.set(false)
    this.itemActivo.set(null)
    this.cdr.detectChanges()
  }

  onArchivoSeleccionado(event: Event) {
    const input = event.target as HTMLInputElement
    this.archivoForm = input.files?.[0] ?? null
  }

  confirmarRegistro() {
    const item = this.itemActivo()
    if (!item) return

    if (this.montoForm() <= 0) {
      this.toast.error('Monto inválido', 'Ingresa un monto mayor a cero.')
      return
    }
    if (!this.archivoForm) {
      this.toast.error('Falta el comprobante', 'Debes anexar el comprobante de pago.')
      return
    }

    this.guardando.set(true)

    this.sueldoSvc.create({
      id_us: item.id_us,
      id_imp: item.id_imp || null,
      id_programa: item.id_programa,
      concepto: `Honorario ${item.nombre_curso} - ${this.mes()}/${this.anio()}`,
      periodo: `${this.anio()}-${String(this.mes()).padStart(2, '0')}`,
      gestion: this.anio(),
      monto_total: this.montoForm(),
    }).subscribe({
      next: (sueldo) => {
        this.sueldoSvc.addPago(sueldo.id, {
          monto_pagado: this.montoForm(),
          fecha_pago: this.fechaPagoForm(),
          nro_comprobante: this.nroComprobanteForm(),
          observacion: '',
          archivo: this.archivoForm,
        }).subscribe({
          next: () => {
            this.toast.success('Registrado', 'El honorario fue registrado y pagado con su comprobante.')
            this.guardando.set(false)
            this.cerrarModal()
            this.load()
          },
          error: (err) => {
            this.toast.error('Error', err?.error?.message ?? 'El sueldo se creó, pero no se pudo registrar el pago. Complétalo en Sueldos Docentes.')
            this.guardando.set(false)
            this.cerrarModal()
            this.load()
          }
        })
      },
      error: (err) => {
        this.toast.error('Error', err?.error?.message ?? 'No se pudo registrar el honorario.')
        this.guardando.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  etiquetaTipo(tipo: string): string {
    switch (tipo) {
      case 'diplomado_fijo': return 'Diplomado'
      case 'rm_por_dia':     return 'Curso R.M.'
      case 'aval_por_dia':   return 'Curso con aval'
      default:               return tipo
    }
  }
}
