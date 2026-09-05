import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core'
import { ActivatedRoute, Router, RouterLink } from '@angular/router'
import { FormsModule } from '@angular/forms'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucideSave, lucideLoader } from '@ng-icons/lucide'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { SearchableSelect, SelectOption } from '../../../common/components/searchable-select/searchable-select'
import { ToastService } from '../../../common/application/services/toast.service'
import { CampanaLeadService } from '../../application/services/campana-lead.service'
import { EstadoCampanaLead } from '../../domain/models/campana-lead.model'

@Component({
  selector: 'app-campana-lead-edit',
  standalone: true,
  imports: [FormsModule, NgIcon, PageTitle, SearchableSelect, RouterLink],
  viewProviders: [provideIcons({ lucideSave, lucideLoader })],
  templateUrl: './campana-lead-edit.html',
})
export class CampanaLeadEdit implements OnInit {
  private route  = inject(ActivatedRoute)
  private router = inject(Router)
  private svc    = inject(CampanaLeadService)
  private toast  = inject(ToastService)
  private cdr    = inject(ChangeDetectorRef)

  isNew         = signal(true)
  campanaId     = signal<number | null>(null)
  loading       = signal(true)
  saving        = signal(false)

  readonly estadoOptions: SelectOption[] = [
    { value: 'activa', label: 'Activa' },
    { value: 'cerrada', label: 'Cerrada' },
  ]

  nombre      = signal('')
  descripcion = signal('')
  estado      = signal<EstadoCampanaLead>('activa')
  fechaInicio = signal('')
  fechaFin    = signal('')

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id')
    if (!id || id === 'new') {
      this.isNew.set(true)
      this.loading.set(false)
      return
    }

    this.isNew.set(false)
    const numId = Number(id)
    this.campanaId.set(numId)
    this.svc.getById(numId).subscribe({
      next: (c) => {
        this.nombre.set(c.nombre)
        this.descripcion.set(c.descripcion ?? '')
        this.estado.set(c.estado)
        this.fechaInicio.set(c.fecha_inicio ?? '')
        this.fechaFin.set(c.fecha_fin ?? '')
        this.loading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar la campaña de leads.')
        this.loading.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  save() {
    if (!this.nombre().trim()) {
      this.toast.error('Datos incompletos', 'El nombre del curso / campaña es obligatorio.')
      return
    }

    this.saving.set(true)

    const payload = {
      nombre:       this.nombre().trim(),
      descripcion:  this.descripcion() || null,
      estado:       this.estado(),
      fecha_inicio: this.fechaInicio() || null,
      fecha_fin:    this.fechaFin() || null,
    }

    const request = this.isNew()
      ? this.svc.create(payload)
      : this.svc.update(this.campanaId()!, payload)

    request.subscribe({
      next: () => {
        this.saving.set(false)
        this.toast.success('Guardado', this.isNew() ? 'Campaña de leads registrada correctamente.' : 'Campaña actualizada.')
        this.router.navigate(['/cenefco/campanas-leads'])
      },
      error: (err) => {
        this.toast.error('Error', err?.error?.message ?? err?.error?.error ?? 'No se pudo guardar la campaña.')
        this.saving.set(false)
        this.cdr.detectChanges()
      }
    })
  }
}
