import { Component, inject, signal, OnInit } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ZoomService } from '../../application/services/zoom.service';
import { ZoomCuenta } from '../../domain/models/zoom.model';
import { ToastService } from '../../../common/application/services/toast.service';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-zoom-cuentas',
  standalone: true,
  imports: [ReactiveFormsModule, NgIcon, PageTitle],
  templateUrl: './zoom-cuentas.html',
})
export class ZoomCuentas implements OnInit {
  private service = inject(ZoomService);
  private toast   = inject(ToastService);
  private fb      = inject(FormBuilder);

  cuentas     = signal<ZoomCuenta[]>([]);
  loading     = signal(true);
  showForm    = signal(false);
  editando    = signal<ZoomCuenta | null>(null);
  submitting  = signal(false);
  testando    = signal<number | null>(null);

  form = this.fb.group({
    nombre:        ['', [Validators.required, Validators.maxLength(100)]],
    account_id:    ['', [Validators.required]],
    client_id:     ['', [Validators.required]],
    client_secret: ['', [Validators.required]],
    timezone:      ['America/La_Paz'],
    descripcion:   [''],
    activa:        [true],
  });

  ngOnInit(): void { this.cargar(); }

  cargar(): void {
    this.loading.set(true);
    this.service.getCuentas().subscribe({
      next: (r) => { this.cuentas.set(r.data); this.loading.set(false); },
      error: () => { this.toast.error('Error', 'No se pudieron cargar las cuentas'); this.loading.set(false); }
    });
  }

  abrirFormNuevo(): void { this.editando.set(null); this.form.reset({ timezone: 'America/La_Paz', activa: true }); this.showForm.set(true); }

  abrirFormEditar(c: ZoomCuenta): void {
    this.editando.set(c);
    this.form.patchValue({ nombre: c.nombre, account_id: c.account_id, timezone: c.timezone, descripcion: c.descripcion, activa: c.activa });
    this.form.get('client_id')?.clearValidators(); this.form.get('client_secret')?.clearValidators();
    this.form.get('client_id')?.updateValueAndValidity(); this.form.get('client_secret')?.updateValueAndValidity();
    this.showForm.set(true);
  }

  cerrarForm(): void { this.showForm.set(false); this.editando.set(null); }

  guardar(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    const v = this.form.value as any;
    const e = this.editando();
    const obs = e ? this.service.updateCuenta(e.id, v) : this.service.createCuenta(v);
    obs.subscribe({
      next: () => { this.toast.success('¡Guardado!', e ? 'Cuenta actualizada' : 'Cuenta creada'); this.cerrarForm(); this.cargar(); this.submitting.set(false); },
      error: () => { this.toast.error('Error', 'No se pudo guardar'); this.submitting.set(false); }
    });
  }

  setPredeterminada(c: ZoomCuenta): void {
    this.service.setPredeterminada(c.id).subscribe({
      next: () => { this.toast.success('Actualizado', `${c.nombre} es ahora la cuenta predeterminada`); this.cargar(); },
      error: () => this.toast.error('Error', 'No se pudo actualizar')
    });
  }

  testear(c: ZoomCuenta): void {
    this.testando.set(c.id);
    this.service.testCuenta(c.id).subscribe({
      next: (r) => { this.testando.set(null); if (r.ok) this.toast.success('Conexión OK', `${c.nombre} — ${r.reuniones} reunión(es) activa(s)`); else this.toast.error('Error de conexión', r.message); },
      error: (err) => { this.testando.set(null); this.toast.error('Error', err?.error?.message ?? 'No se pudo conectar'); }
    });
  }

  eliminar(c: ZoomCuenta): void {
    Swal.fire({ title: `¿Eliminar ${c.nombre}?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonText: 'Cancelar', confirmButtonText: 'Eliminar' }).then(r => {
      if (r.isConfirmed) this.service.deleteCuenta(c.id).subscribe({ next: () => { this.toast.success('Eliminada', 'Cuenta eliminada'); this.cargar(); } });
    });
  }
}
