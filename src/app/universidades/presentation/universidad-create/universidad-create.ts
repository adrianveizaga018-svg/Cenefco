import { Component, inject, signal } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { UniversidadService } from '../../application/services/universidad.service';
import { ToastService } from '../../../common/application/services/toast.service';

@Component({
  selector: 'app-universidad-create',
  imports: [PageTitle, RouterLink, FormsModule],
  templateUrl: './universidad-create.html',
})
export class UniversidadCreate {
  private service = inject(UniversidadService);
  private toast   = inject(ToastService);
  private router  = inject(Router);

  saving = signal(false);

  form = {
    nombre_universidad: '',
    id_ciudad:          null as number | null,
    id_tipouniversidad: null as number | null,
    estado:             1,
  };

  save(): void {
    if (!this.form.nombre_universidad.trim()) return;
    this.saving.set(true);
    this.service.create(this.form).subscribe({
      next: () => { this.toast.success('Creado', 'Universidad creada'); this.router.navigate(['/cenefco/universidades']); },
      error: () => { this.toast.error('Error', 'No se pudo crear'); this.saving.set(false); },
    });
  }
}
