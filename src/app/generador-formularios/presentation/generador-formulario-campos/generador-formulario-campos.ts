import { Component, inject, signal, computed, OnInit } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { FormsModule } from '@angular/forms';
import { PageTitle }   from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { GeneradorFormularioService } from '../../application/services/generador-formulario.service';
import {
  CampoFormulario, GeneradorFormulario, TipoCampo,
  TIPOS_CAMPO, NOMBRES_SUGERIDOS,
} from '../../domain/models/generador-formulario.model';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-generador-formulario-campos',
  standalone: true,
  imports: [NgIcon, PageTitle, RouterLink, FormsModule],
  templateUrl: './generador-formulario-campos.html',
})
export class GeneradorFormularioCampos implements OnInit {
  private svc   = inject(GeneradorFormularioService);
  private route = inject(ActivatedRoute);
  private toast = inject(ToastService);
  private router = inject(Router);

  readonly tiposCampo = TIPOS_CAMPO;
  readonly sugeridos  = NOMBRES_SUGERIDOS;

  formulario    = signal<GeneradorFormulario | null>(null);
  campos        = signal<CampoFormulario[]>([]);
  loading       = signal(true);
  saving        = signal(false);
  selectedIndex = signal<number | null>(null);
  showNuevo     = signal(false);
  showSugeridos = signal(false);

  nuevoCampo: CampoFormulario = this.campoVacio();
  nuevaOpcion = '';

  readonly selected = computed(() => {
    const i = this.selectedIndex();
    return i !== null ? this.campos()[i] ?? null : null;
  });

  readonly id = computed(() => Number(this.route.snapshot.paramMap.get('id')));

  ngOnInit(): void {
    this.svc.getById(this.id()).subscribe({
      next: (f) => {
        this.formulario.set(f);
        this.campos.set(f.campos ? [...f.campos] : []);
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar el formulario.');
        this.router.navigate(['/cenefco/generador-formularios']);
      },
    });
  }

  private campoVacio(): CampoFormulario {
    return { nombre_campo: '', etiqueta: '', tipo: 'text', requerido: false, opciones: [], placeholder: '', ayuda: '' };
  }

  selectCampo(i: number): void {
    this.selectedIndex.set(i === this.selectedIndex() ? null : i);
  }

  usarSugerido(s: typeof NOMBRES_SUGERIDOS[0]): void {
    this.nuevoCampo = { ...this.campoVacio(), nombre_campo: s.nombre_campo, etiqueta: s.etiqueta, tipo: s.tipo };
    if (s.tipo === 'select') this.nuevoCampo.opciones = [];
    this.showNuevo.set(true);
    this.showSugeridos.set(false);
  }

  agregarOpcionNuevo(): void {
    const op = this.nuevaOpcion.trim();
    if (!op) return;
    if (!this.nuevoCampo.opciones) this.nuevoCampo.opciones = [];
    this.nuevoCampo.opciones.push(op);
    this.nuevaOpcion = '';
  }

  quitarOpcionNuevo(i: number): void {
    this.nuevoCampo.opciones?.splice(i, 1);
  }

  confirmarNuevoCampo(): void {
    if (!this.nuevoCampo.nombre_campo.trim() || !this.nuevoCampo.etiqueta.trim()) {
      this.toast.error('Error', 'El nombre y la etiqueta son obligatorios.');
      return;
    }
    const yaExiste = this.campos().some(c => c.nombre_campo === this.nuevoCampo.nombre_campo);
    if (yaExiste) {
      this.toast.error('Error', `Ya existe un campo con el nombre "${this.nuevoCampo.nombre_campo}".`);
      return;
    }
    const camp: CampoFormulario = { ...this.nuevoCampo, opciones: [...(this.nuevoCampo.opciones ?? [])] };
    this.campos.update(list => [...list, camp]);
    const newIndex = this.campos().length - 1;
    this.selectedIndex.set(newIndex);
    this.nuevoCampo  = this.campoVacio();
    this.nuevaOpcion = '';
    this.showNuevo.set(false);
  }

  updateSelected(patch: Partial<CampoFormulario>): void {
    const i = this.selectedIndex();
    if (i === null) return;
    this.campos.update(list => {
      const arr = [...list];
      arr[i] = { ...arr[i], ...patch };
      return arr;
    });
  }

  agregarOpcionSelected(): void {
    const i = this.selectedIndex();
    if (i === null) return;
    const val = (this as any)._opcionTemp?.trim?.();
    if (!val) return;
    this.campos.update(list => {
      const arr = [...list];
      const ops = [...(arr[i].opciones ?? []), val];
      arr[i] = { ...arr[i], opciones: ops };
      return arr;
    });
    (this as any)._opcionTemp = '';
  }

  quitarOpcionSelected(oi: number): void {
    const i = this.selectedIndex();
    if (i === null) return;
    this.campos.update(list => {
      const arr = [...list];
      const ops = [...(arr[i].opciones ?? [])];
      ops.splice(oi, 1);
      arr[i] = { ...arr[i], opciones: ops };
      return arr;
    });
  }

  moverArriba(i: number): void {
    if (i === 0) return;
    this.campos.update(list => {
      const arr = [...list];
      [arr[i - 1], arr[i]] = [arr[i], arr[i - 1]];
      return arr;
    });
    if (this.selectedIndex() === i) this.selectedIndex.set(i - 1);
    else if (this.selectedIndex() === i - 1) this.selectedIndex.set(i);
  }

  moverAbajo(i: number): void {
    this.campos.update(list => {
      if (i >= list.length - 1) return list;
      const arr = [...list];
      [arr[i], arr[i + 1]] = [arr[i + 1], arr[i]];
      return arr;
    });
    if (this.selectedIndex() === i) this.selectedIndex.set(i + 1);
    else if (this.selectedIndex() === i + 1) this.selectedIndex.set(i);
  }

  async eliminarCampo(i: number): Promise<void> {
    const camp = this.campos()[i];
    const result = await Swal.fire({
      title: `¿Eliminar "${camp.etiqueta}"?`,
      icon: 'warning', showCancelButton: true,
      confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar',
      confirmButtonColor: '#ef4444',
    });
    if (!result.isConfirmed) return;
    this.campos.update(list => list.filter((_, idx) => idx !== i));
    if (this.selectedIndex() === i) this.selectedIndex.set(null);
    else if ((this.selectedIndex() ?? 0) > i) this.selectedIndex.update(n => (n ?? 1) - 1);
  }

  guardar(): void {
    this.saving.set(true);
    this.svc.update(this.id(), { campos: this.campos() }).subscribe({
      next: () => {
        this.toast.success('Guardado', 'Campos del formulario actualizados.');
        this.saving.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudieron guardar los campos.');
        this.saving.set(false);
      },
    });
  }

  agregarOpcionSelectedDesdeInput(input: HTMLInputElement): void {
    const val = input.value.trim();
    if (!val) return;
    const i = this.selectedIndex();
    if (i === null) return;
    this.campos.update(list => {
      const arr = [...list];
      arr[i] = { ...arr[i], opciones: [...(arr[i].opciones ?? []), val] };
      return arr;
    });
    input.value = '';
  }

  selectedIndexStr(): string {
    return String(this.selectedIndex() ?? '');
  }

  labelTipo(tipo: TipoCampo): string {
    return this.tiposCampo.find(t => t.value === tipo)?.label ?? tipo;
  }

  iconTipo(tipo: TipoCampo): string {
    return this.tiposCampo.find(t => t.value === tipo)?.icon ?? 'lucideType';
  }
}
