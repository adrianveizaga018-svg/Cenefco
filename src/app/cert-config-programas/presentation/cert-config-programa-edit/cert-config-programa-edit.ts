import { Component, inject, signal, computed, ChangeDetectorRef, OnInit } from '@angular/core'
import { ActivatedRoute, RouterLink } from '@angular/router'
import { FormsModule } from '@angular/forms'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucidePlus, lucideTrash2, lucideSave, lucideLoader, lucidePencil } from '@ng-icons/lucide'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { SearchableSelect, SelectOption } from '../../../common/components/searchable-select/searchable-select'
import { ToastService } from '../../../common/application/services/toast.service'
import { CertConfigProgramaService } from '../../application/services/cert-config-programa.service'
import { CertConfigPrograma, CertConfigItem } from '../../domain/models/cert-config-programa.model'
import { CursoService } from '../../../cursos/application/services/curso.service'
import { CertificadoService } from '../../../certificados/application/services/certificado.service'

@Component({
  selector: 'app-cert-config-programa-edit',
  standalone: true,
  imports: [FormsModule, NgIcon, PageTitle, SearchableSelect, RouterLink],
  viewProviders: [provideIcons({ lucidePlus, lucideTrash2, lucideSave, lucideLoader, lucidePencil })],
  templateUrl: './cert-config-programa-edit.html',
})
export class CertConfigProgramaEdit implements OnInit {
  private route            = inject(ActivatedRoute)
  private svc              = inject(CertConfigProgramaService)
  private toast            = inject(ToastService)
  private cdr              = inject(ChangeDetectorRef)
  private cursoService     = inject(CursoService)
  private certificadoSvc   = inject(CertificadoService)

  config     = signal<CertConfigPrograma | null>(null)
  loading    = signal(true)
  saving     = signal(false)
  isNew      = signal(false)

  programaId  = signal<number | null>(null)
  activo      = signal(true)
  titulo      = signal('')
  descripcion = signal('')

  programaOptions  = signal<SelectOption[]>([])
  plantillaOptions = signal<SelectOption[]>([])

  programaNombre = computed(() => this.config()?.nombre_programa || `Programa #${this.config()?.programa_id ?? ''}`)

  newItem = signal({ nombre_cert: '', precio: 0, es_gratuito: false, plantilla_id: null as number | null, orden: 0 })
  addingItem = signal(false)
  editandoItemId = signal<number | null>(null)

  ngOnInit() {
    this.cargarProgramas()
    this.cargarPlantillas()

    const id = this.route.snapshot.paramMap.get('id')
    if (id === 'new') {
      this.isNew.set(true)
      this.loading.set(false)
      this.cdr.detectChanges()
      return
    }
    const numId = Number(id)
    this.svc.getByPrograma(numId).subscribe({
      next: (cfg) => {
        this.config.set(cfg)
        this.programaId.set(cfg.programa_id)
        this.activo.set(cfg.activo)
        this.titulo.set(cfg.titulo ?? '')
        this.descripcion.set(cfg.descripcion ?? '')
        this.loading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar la configuración.')
        this.loading.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  private cargarProgramas() {
    this.cursoService.getAll({ pageSize: 500 }).subscribe({
      next: (res) => {
        this.programaOptions.set(res.data.map(c => ({ value: c.id_programa, label: c.nombre_programa })))
        this.cdr.detectChanges()
      },
      error: () => {}
    })
  }

  private cargarPlantillas() {
    this.certificadoSvc.getPlantillas({ soloActivos: true, pageSize: 200 }).subscribe({
      next: (res) => {
        this.plantillaOptions.set(res.data.map(p => ({ value: p.id, label: p.nombre })))
        this.cdr.detectChanges()
      },
      error: () => {}
    })
  }

  save() {
    const programaId = this.programaId()
    if (!programaId) {
      this.toast.error('Falta el programa', 'Selecciona un programa antes de guardar.')
      return
    }
    this.saving.set(true)
    this.svc.upsert({
      programa_id: programaId,
      activo:      this.activo(),
      titulo:      this.titulo() || null,
      descripcion: this.descripcion() || null,
    }).subscribe({
      next: (cfg) => {
        this.config.set(cfg)
        this.isNew.set(false)
        this.saving.set(false)
        this.toast.success('Guardado', 'Configuración guardada correctamente.')
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo guardar.')
        this.saving.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  addItem() {
    const cfg = this.config()
    if (!cfg) return
    const item = this.newItem()
    const editandoId = this.editandoItemId()
    this.addingItem.set(true)

    const payload = {
      nombre_cert:  item.nombre_cert,
      precio:       item.precio,
      es_gratuito:  item.es_gratuito,
      plantilla_id: item.plantilla_id,
      orden:        item.orden,
    }

    const request = editandoId
      ? this.svc.updateItem(cfg.id, editandoId, payload)
      : this.svc.createItem(cfg.id, payload)

    request.subscribe({
      next: (savedItem) => {
        this.config.update(c => c ? {
          ...c,
          items: editandoId
            ? c.items.map(i => i.id === editandoId ? savedItem : i)
            : [...c.items, savedItem],
        } : c)
        this.cancelarEdicionItem()
        this.addingItem.set(false)
        this.toast.success('Listo', editandoId ? 'Certificado actualizado.' : 'Certificado agregado.')
        this.cdr.detectChanges()
      },
      error: (err) => {
        this.toast.error('Error', err?.error?.message ?? 'No se pudo guardar.')
        this.addingItem.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  editarItem(item: CertConfigItem) {
    this.editandoItemId.set(item.id)
    this.newItem.set({
      nombre_cert:  item.nombre_cert,
      precio:       item.precio,
      es_gratuito:  item.es_gratuito,
      plantilla_id: item.plantilla_id,
      orden:        item.orden,
    })
    this.cdr.detectChanges()
  }

  cancelarEdicionItem() {
    this.editandoItemId.set(null)
    this.newItem.set({ nombre_cert: '', precio: 0, es_gratuito: false, plantilla_id: null, orden: 0 })
  }

  removeItem(item: CertConfigItem) {
    const cfg = this.config()
    if (!cfg) return
    this.svc.deleteItem(cfg.id, item.id).subscribe({
      next: () => {
        this.config.update(c => c ? { ...c, items: c.items.filter(i => i.id !== item.id) } : c)
        if (this.editandoItemId() === item.id) {
          this.cancelarEdicionItem()
        }
        this.toast.success('Eliminado', 'Certificado eliminado.')
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo eliminar.')
        this.cdr.detectChanges()
      }
    })
  }

  updateNewItem(field: string, value: any) {
    this.newItem.update(n => ({ ...n, [field]: value }))
  }
}
