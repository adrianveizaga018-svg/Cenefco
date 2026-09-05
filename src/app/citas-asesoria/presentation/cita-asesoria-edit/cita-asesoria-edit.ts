import { Component, inject, signal, OnInit } from '@angular/core';
import { Router, RouterLink, ActivatedRoute } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { CitaAsesoriaService } from '../../application/services/cita-asesoria.service';
import { ToastService } from '../../../common/application/services/toast.service';

@Component({
  selector: 'app-cita-asesoria-edit',
  imports: [PageTitle, RouterLink, FormsModule],
  templateUrl: './cita-asesoria-edit.html',
})
export class CitaAsesoriaEdit implements OnInit {
  private service = inject(CitaAsesoriaService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);

  loading = signal(true);
  saving  = signal(false);

  form = {
    nombres:          '',
    apellidos:        '',
    email:            '',
    celular:          '',
    mensaje:          '',
    programa_interes: '',
    fecha_preferida:  '',
    hora_preferida:   '',
    estado:           'pendiente',
    observacion:      '',
  };

  private id = 0;

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.service.getById(this.id).subscribe({
      next: r => { Object.assign(this.form, r); this.loading.set(false); },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/citas-asesoria']); },
    });
  }

  save(): void {
    this.saving.set(true);
    this.service.update(this.id, this.form).subscribe({
      next: () => { this.toast.success('Actualizado', 'Cita actualizada'); this.router.navigate(['/cenefco/citas-asesoria']); },
      error: () => { this.toast.error('Error', 'No se pudo actualizar'); this.saving.set(false); },
    });
  }
}
