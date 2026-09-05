import { Component, inject, signal, OnInit, ChangeDetectorRef } from '@angular/core';
import { NgClass } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { WhatsappService } from '../../application/services/whatsapp.service';
import { WhatsappAsesor } from '../../domain/models/whatsapp.model';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';

@Component({
  selector: 'app-whatsapp-asesores',
  imports: [NgClass, FormsModule, NgIcon, PageTitle],
  templateUrl: './whatsapp-asesores.html',
  styles: ``
})
export class WhatsappAsesores implements OnInit {
  private svc   = inject(WhatsappService);
  private toast = inject(ToastService);
  private cdr   = inject(ChangeDetectorRef);

  asesores = signal<WhatsappAsesor[]>([]);
  loading  = signal(true);
  saving   = signal(false);
  deleting = signal<number | null>(null);

  showModal  = signal(false);
  editTarget = signal<WhatsappAsesor | null>(null);

  form = {
    nombre:       '',
    telefono:     '',
    email:        '',
    especialidad: '',
    disponible:   true,
    activo:       true,
  };

  ngOnInit() { this.cargar(); }

  cargar() {
    this.loading.set(true);
    this.svc.getAsesores().subscribe({
      next: r => { this.asesores.set(r.data); this.loading.set(false); this.cdr.detectChanges(); },
      error: () => { this.loading.set(false); this.cdr.detectChanges(); },
    });
  }

  abrirCrear() {
    this.editTarget.set(null);
    this.form = { nombre: '', telefono: '', email: '', especialidad: '', disponible: true, activo: true };
    this.showModal.set(true);
  }

  abrirEditar(a: WhatsappAsesor) {
    this.editTarget.set(a);
    this.form = {
      nombre:       a.nombre,
      telefono:     a.telefono,
      email:        a.email ?? '',
      especialidad: a.especialidad ?? '',
      disponible:   a.disponible,
      activo:       a.activo,
    };
    this.showModal.set(true);
  }

  cerrarModal() { this.showModal.set(false); }

  guardar() {
    if (!this.form.nombre.trim() || !this.form.telefono.trim()) {
      this.toast.warning('Campos requeridos', 'Nombre y teléfono son obligatorios.');
      return;
    }
    this.saving.set(true);
    const payload = { ...this.form, email: this.form.email || null, especialidad: this.form.especialidad || null };
    const target = this.editTarget();

    const op = target
      ? this.svc.updateAsesor(target.id, payload)
      : this.svc.createAsesor(payload);

    op.subscribe({
      next: () => {
        this.toast.success(target ? 'Actualizado' : 'Creado', `Asesor ${target ? 'actualizado' : 'creado'} correctamente.`);
        this.saving.set(false);
        this.showModal.set(false);
        this.cargar();
      },
      error: () => {
        this.toast.error('Error', 'No se pudo guardar el asesor.');
        this.saving.set(false);
        this.cdr.detectChanges();
      },
    });
  }

  eliminar(a: WhatsappAsesor) {
    if (!confirm(`¿Eliminar a ${a.nombre}?`)) return;
    this.deleting.set(a.id);
    this.svc.deleteAsesor(a.id).subscribe({
      next: () => {
        this.toast.success('Eliminado', `${a.nombre} fue eliminado.`);
        this.deleting.set(null);
        this.cargar();
      },
      error: () => {
        this.toast.error('Error', 'No se pudo eliminar el asesor.');
        this.deleting.set(null);
        this.cdr.detectChanges();
      },
    });
  }
}
