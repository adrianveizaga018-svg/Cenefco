import { Component, inject, signal, OnInit } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { CalendarioAcademicoService } from '../../application/services/calendario-academico.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { TIPOS_EVENTO } from '../../domain/models/calendario-academico.model';
import { VendedorService } from '../../../vendedores/application/services/vendedor.service';
import { Vendedor } from '../../../vendedores/domain/models/vendedor.model';

interface ProgramaOpt { id_programa: number; nombre_programa: string; descripcion: string | null; }

@Component({
  selector: 'app-calendario-edit',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './calendario-edit.html',
})
export class CalendarioEdit implements OnInit {
  private service = inject(CalendarioAcademicoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private fb      = inject(FormBuilder);
  private http    = inject(HttpClient);
  private vendedorService = inject(VendedorService);

  readonly tipos = TIPOS_EVENTO;

  submitting        = signal(false);
  loading           = signal(true);
  programas         = signal<ProgramaOpt[]>([]);
  programasLoading  = signal(true);
  vendedores        = signal<Vendedor[]>([]);
  vendedoresLoading = signal(true);
  id = Number(this.route.snapshot.paramMap.get('id'));

  form = this.fb.group({
    titulo:       ['', [Validators.required, Validators.maxLength(300)]],
    descripcion:  [null as string | null],
    tipo:         [null as string | null],
    color:        ['#3b82f6'],
    programa_id:  [null as number | null],
    fecha_inicio: ['', [Validators.required]],
    fecha_fin:    [null as string | null],
    todo_el_dia:  [true],
    destacado:    [false],
    publico:      [true],
    vendedor_id:   [null as number | null],
    pagina:        [null as string | null],
    duracion_dias: [null as number | null],
    costo_inflado: [null as number | null],
    descuento:     [null as number | null],
    precio_vip:    [null as number | null],
    observaciones: [null as string | null],
  });

  ngOnInit(): void {
    this.http.get<{ data: ProgramaOpt[] }>('/api/v1/cursos', { params: { pageSize: '300' } })
      .subscribe({ next: r => { this.programas.set(r.data); this.programasLoading.set(false); } });

    this.vendedorService.getAll({ pageSize: 200, activo: '1' })
      .subscribe({ next: r => { this.vendedores.set(r.data); this.vendedoresLoading.set(false); } });

    this.service.getById(this.id).subscribe({
      next: d => {
        this.form.patchValue({
          titulo:        d.titulo,
          descripcion:   d.descripcion,
          tipo:          d.tipo,
          color:         d.color ?? '#3b82f6',
          programa_id:   d.programa_id,
          fecha_inicio:  d.fecha_inicio?.slice(0, 10) ?? '',
          fecha_fin:     d.fecha_fin?.slice(0, 10) ?? null,
          todo_el_dia:   d.todo_el_dia,
          destacado:     d.destacado,
          publico:       d.publico,
          vendedor_id:   d.vendedor_id,
          pagina:        d.pagina,
          duracion_dias: d.duracion_dias,
          costo_inflado: d.costo_inflado,
          descuento:     d.descuento,
          precio_vip:    d.precio_vip,
          observaciones: d.observaciones,
        });
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar el registro');
        this.router.navigate(['/cenefco/calendario-academico']);
      },
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => {
        this.toast.success('¡Actualizado!', 'Registro actualizado correctamente');
        this.router.navigate(['/cenefco/calendario-academico']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar'));
        this.submitting.set(false);
      },
    });
  }
}
