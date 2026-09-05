import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core'
import { FormsModule } from '@angular/forms'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucideSave, lucideLoader, lucideTrash2 } from '@ng-icons/lucide'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { SearchableSelect, SelectOption } from '../../../common/components/searchable-select/searchable-select'
import { ToastService } from '../../../common/application/services/toast.service'
import { HonorarioService } from '../../application/services/honorario.service'
import { ConfigHonorario, TipoHonorario } from '../../domain/models/honorario.model'
import { CursoService } from '../../../cursos/application/services/curso.service'

@Component({
  selector: 'app-config-honorarios',
  standalone: true,
  imports: [FormsModule, NgIcon, PageTitle, SearchableSelect],
  viewProviders: [provideIcons({ lucideSave, lucideLoader, lucideTrash2 })],
  templateUrl: './config-honorarios.html',
})
export class ConfigHonorarios implements OnInit {
  private svc         = inject(HonorarioService)
  private cursoSvc    = inject(CursoService)
  private toast       = inject(ToastService)
  private cdr         = inject(ChangeDetectorRef)

  configuraciones = signal<ConfigHonorario[]>([])
  programaOptions = signal<SelectOption[]>([])
  loading         = signal(true)
  saving          = signal(false)
  eliminando      = signal<number | null>(null)

  form = signal({
    id_programa: null as number | null,
    tipo_honorario: 'diplomado_fijo' as TipoHonorario,
    monto_fijo: 0,
    monto_por_dia: 0,
  })

  ngOnInit() {
    this.cargarProgramas()
    this.load()
  }

  private cargarProgramas() {
    this.cursoSvc.getAll({ pageSize: 500 }).subscribe({
      next: (res) => {
        this.programaOptions.set(res.data.map(c => ({ value: c.id_programa, label: c.nombre_programa })))
        this.cdr.detectChanges()
      },
      error: () => {}
    })
  }

  load() {
    this.loading.set(true)
    this.svc.getConfiguraciones().subscribe({
      next: (res) => {
        this.configuraciones.set(res)
        this.loading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar la configuración de honorarios.')
        this.loading.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  guardar() {
    const f = this.form()
    if (!f.id_programa) {
      this.toast.error('Falta el programa', 'Selecciona un programa antes de guardar.')
      return
    }

    this.saving.set(true)
    this.svc.upsertConfiguracion({
      id_programa: f.id_programa,
      tipo_honorario: f.tipo_honorario,
      monto_fijo: f.tipo_honorario === 'diplomado_fijo' ? f.monto_fijo : null,
      monto_por_dia: f.tipo_honorario !== 'diplomado_fijo' ? f.monto_por_dia : null,
    }).subscribe({
      next: () => {
        this.saving.set(false)
        this.toast.success('Guardado', 'Configuración de honorario guardada.')
        this.form.set({ id_programa: null, tipo_honorario: 'diplomado_fijo', monto_fijo: 0, monto_por_dia: 0 })
        this.load()
      },
      error: (err) => {
        this.toast.error('Error', err?.error?.message ?? 'No se pudo guardar.')
        this.saving.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  eliminar(config: ConfigHonorario) {
    this.eliminando.set(config.id_programa)
    this.svc.eliminarConfiguracion(config.id_programa).subscribe({
      next: () => {
        this.toast.success('Eliminado', 'Configuración eliminada.')
        this.eliminando.set(null)
        this.load()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo eliminar.')
        this.eliminando.set(null)
        this.cdr.detectChanges()
      }
    })
  }

  updateForm(field: string, value: any) {
    this.form.update(f => ({ ...f, [field]: value }))
  }

  etiquetaTipo(tipo: TipoHonorario): string {
    switch (tipo) {
      case 'diplomado_fijo': return 'Diplomado (monto fijo)'
      case 'rm_por_dia':     return 'Curso con R.M. (por día)'
      case 'aval_por_dia':   return 'Curso con aval (por día)'
    }
  }
}
