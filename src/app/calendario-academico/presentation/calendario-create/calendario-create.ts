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
  selector: 'app-calendario-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './calendario-create.html',
})
export class CalendarioCreate implements OnInit {
  private service = inject(CalendarioAcademicoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private fb      = inject(FormBuilder);
  private http    = inject(HttpClient);
  private vendedorService = inject(VendedorService);

  readonly tipos = TIPOS_EVENTO;

  submitting       = signal(false);
  programas        = signal<ProgramaOpt[]>([]);
  programasLoading = signal(true);
  vendedores       = signal<Vendedor[]>([]);
  vendedoresLoading = signal(true);

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

    const fecha = this.route.snapshot.queryParamMap.get('fecha');
    if (fecha) this.form.patchValue({ fecha_inicio: fecha });

    this.form.get('tipo')!.valueChanges.subscribe(tipo => {
      const t = this.tipos.find(t => t.value === tipo);
      if (t) this.form.get('color')!.setValue(t.color, { emitEvent: false });
    });

    this.form.get('programa_id')!.valueChanges.subscribe(id => {
      if (!id) return;
      const prog = this.programas().find(p => p.id_programa === Number(id));
      if (prog?.descripcion && !this.form.get('descripcion')!.value) {
        this.form.get('descripcion')!.setValue(prog.descripcion, { emitEvent: false });
      }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => {
        this.toast.success('¡Creado!', 'Registro añadido al cronograma');
        this.router.navigate(['/cenefco/calendario-academico']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar'));
        this.submitting.set(false);
      },
    });
  }

  nombreVendedor(id: number | null): string {
    if (!id) return '';
    const v = this.vendedores().find(u => u.id === Number(id));
    return v ? `${v.nombre} ${v.apellido}` : '';
  }
}
