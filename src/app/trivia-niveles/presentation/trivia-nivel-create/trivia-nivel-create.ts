import { Component, OnInit, inject, signal } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { TriviaNivelService } from '../../application/services/trivia-nivel.service';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { TriviaCategoriaService } from '../../../trivia-categorias/application/services/trivia-categoria.service';
import { TriviaCategoria } from '../../../trivia-categorias/domain/models/trivia-categoria.model';

@Component({
  selector: 'app-trivia-nivel-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './trivia-nivel-create.html',
  styles: ``
})
export class TriviaNivelCreate implements OnInit {
  private fb              = inject(FormBuilder);
  private service         = inject(TriviaNivelService);
  private categoriaService = inject(TriviaCategoriaService);
  private toast           = inject(ToastService);
  private router          = inject(Router);
  private route           = inject(ActivatedRoute);

  submitting  = signal(false);
  categorias  = signal<TriviaCategoria[]>([]);

  form: FormGroup = this.fb.group({
    categoria_id:  [null as number | null, [Validators.required]],
    nombre:        ['', [Validators.required, Validators.maxLength(100)]],
    orden:         [0],
    puntaje_base:  [100, [Validators.required, Validators.min(1)]],
    activo:        [true],
  });

  ngOnInit(): void {
    this.categoriaService.getAll({ pageSize: 200 }).subscribe({
      next: (res) => {
        this.categorias.set(res.data);
        const preseleccionada = Number(this.route.snapshot.queryParamMap.get('categoria_id')) || null;
        if (preseleccionada) this.form.patchValue({ categoria_id: preseleccionada });
      }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    const val = this.form.value;
    this.service.create({
      ...val,
      orden: Number(val.orden) || 0,
      puntaje_base: Number(val.puntaje_base) || 100,
    }).subscribe({
      next: () => {
        this.toast.success('¡Creado!', 'El nivel ha sido creado correctamente');
        this.router.navigate(['/cenefco/trivia-niveles'], { queryParams: { categoria_id: val.categoria_id } });
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear el nivel'));
        this.submitting.set(false);
      }
    });
  }
}
