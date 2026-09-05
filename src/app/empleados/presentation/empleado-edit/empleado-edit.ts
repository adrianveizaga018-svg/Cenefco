import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core'
import { ActivatedRoute, Router, RouterLink } from '@angular/router'
import { FormsModule } from '@angular/forms'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucideSave, lucideLoader, lucideFileText } from '@ng-icons/lucide'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { ToastService } from '../../../common/application/services/toast.service'
import { EmpleadoService } from '../../application/services/empleado.service'

@Component({
  selector: 'app-empleado-edit',
  standalone: true,
  imports: [FormsModule, NgIcon, PageTitle, RouterLink],
  viewProviders: [provideIcons({ lucideSave, lucideLoader, lucideFileText })],
  templateUrl: './empleado-edit.html',
})
export class EmpleadoEdit implements OnInit {
  private route  = inject(ActivatedRoute)
  private router = inject(Router)
  private svc    = inject(EmpleadoService)
  private toast  = inject(ToastService)
  private cdr    = inject(ChangeDetectorRef)

  isNew      = signal(true)
  empleadoId = signal<number | null>(null)
  loading    = signal(true)
  saving     = signal(false)

  form = signal({
    nombre_completo: '',
    cargo: '',
    sueldo_mensual: 0,
    ci: '',
    correo: '',
    celular_personal: '',
    celular_corporativo: '',
    direccion: '',
    fecha_ingreso: '',
  })
  carnetFile      = signal<File | null>(null)
  carnetActualUrl = signal<string | null>(null)

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id')
    if (!id || id === 'new') {
      this.isNew.set(true)
      this.loading.set(false)
      this.cdr.detectChanges()
      return
    }

    this.isNew.set(false)
    const numId = Number(id)
    this.empleadoId.set(numId)

    this.svc.getById(numId).subscribe({
      next: (empleado) => {
        this.form.set({
          nombre_completo: empleado.nombre_completo,
          cargo: empleado.cargo,
          sueldo_mensual: empleado.sueldo_mensual,
          ci: empleado.ci,
          correo: empleado.correo ?? '',
          celular_personal: empleado.celular_personal ?? '',
          celular_corporativo: empleado.celular_corporativo ?? '',
          direccion: empleado.direccion ?? '',
          fecha_ingreso: empleado.fecha_ingreso ?? '',
        })
        this.carnetActualUrl.set(empleado.carnet_pdf)
        this.loading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar el empleado.')
        this.loading.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  onCarnetSeleccionado(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null
    this.carnetFile.set(file)
  }

  updateForm(field: string, value: any) {
    this.form.update(f => ({ ...f, [field]: value }))
  }

  guardar() {
    const f = this.form()
    const editingId = this.empleadoId()

    if (!f.nombre_completo || !f.cargo || f.sueldo_mensual <= 0 || !f.ci) {
      this.toast.error('Datos incompletos', 'Completa nombre, cargo, sueldo mensual y CI.')
      return
    }

    if (this.isNew() && !this.carnetFile()) {
      this.toast.error('Falta el carné', 'Debes adjuntar el PDF del carné de identidad.')
      return
    }

    this.saving.set(true)
    const payload = {
      nombre_completo: f.nombre_completo,
      cargo: f.cargo,
      sueldo_mensual: f.sueldo_mensual,
      ci: f.ci,
      carnet: this.carnetFile(),
      correo: f.correo || null,
      celular_personal: f.celular_personal || null,
      celular_corporativo: f.celular_corporativo || null,
      direccion: f.direccion || null,
      fecha_ingreso: f.fecha_ingreso || null,
    }

    const request = editingId ? this.svc.update(editingId, payload) : this.svc.create(payload as any)

    request.subscribe({
      next: () => {
        this.saving.set(false)
        this.toast.success('Guardado', editingId ? 'Empleado actualizado.' : 'Empleado registrado.')
        this.router.navigate(['/cenefco/empleados'])
      },
      error: (err) => {
        this.toast.error('Error', err?.error?.message ?? 'No se pudo guardar el empleado.')
        this.saving.set(false)
        this.cdr.detectChanges()
      }
    })
  }
}
