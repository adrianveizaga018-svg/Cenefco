import { Component, inject, signal, OnInit } from '@angular/core';
import { Router, RouterLink, ActivatedRoute } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { UniversidadService } from '../../application/services/universidad.service';
import { ToastService } from '../../../common/application/services/toast.service';

@Component({
  selector: 'app-universidad-edit',
  imports: [PageTitle, RouterLink, FormsModule],
  templateUrl: './universidad-edit.html',
})
export class UniversidadEdit implements OnInit {
  private service = inject(UniversidadService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);

  loading = signal(true);
  saving  = signal(false);

  form = {
    nombre_universidad: '',
    id_ciudad:          null as number | null,
    id_tipouniversidad: null as number | null,
    estado:             1,
  };

  private id = 0;

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.service.getById(this.id).subscribe({
      next: r => { Object.assign(this.form, r); this.loading.set(false); },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/universidades']); },
    });
  }

  save(): void {
    this.saving.set(true);
    this.service.update(this.id, this.form).subscribe({
      next: () => { this.toast.success('Actualizado', 'Universidad actualizada'); this.router.navigate(['/cenefco/universidades']); },
      error: () => { this.toast.error('Error', 'No se pudo actualizar'); this.saving.set(false); },
    });
  }
}
