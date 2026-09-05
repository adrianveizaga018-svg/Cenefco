import { Component, OnInit, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { CatalogoTareaService, CatalogoTarea } from '../../application/services/catalogo-tarea.service';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-catalogo-requisitos',
  standalone: true,
  imports: [ReactiveFormsModule, PageTitle, NgIcon],
  templateUrl: './catalogo-requisitos.html'
})
export class CatalogoRequisitosComponent implements OnInit {
  items = signal<CatalogoTarea[]>([]);
  isLoading = signal(true);
  isError = signal(false);
  saving = signal(false);

  form: FormGroup;
  isEditing = false;
  currentId: number | null = null;
  showModal = signal(false);

  constructor(
    private service: CatalogoTareaService,
    private fb: FormBuilder
  ) {
    this.form = this.fb.group({
      titulo: ['', [Validators.required, Validators.maxLength(200)]],
      requiere_archivo: [false],
      estado: [true]
    });
  }

  ngOnInit(): void {
    this.loadItems();
  }

  loadItems() {
    this.isLoading.set(true);
    this.isError.set(false);
    this.service.getAll().subscribe({
      next: (data) => { this.items.set(data); this.isLoading.set(false); },
      error: () => { this.isError.set(true); this.isLoading.set(false); }
    });
  }

  openCreateForm() {
    this.isEditing = false;
    this.currentId = null;
    this.form.reset({ requiere_archivo: false, estado: true, titulo: '' });
    this.showModal.set(true);
  }

  openEditForm(item: CatalogoTarea) {
    this.isEditing = true;
    this.currentId = item.id!;
    this.form.patchValue(item);
    this.showModal.set(true);
  }

  closeModal() {
    this.showModal.set(false);
    this.saving.set(false);
  }

  save() {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.saving.set(true);

    const val = this.form.value;
    const request = this.isEditing && this.currentId 
      ? this.service.update(this.currentId, val)
      : this.service.create(val);

    request.subscribe({
      next: () => {
        this.saving.set(false);
        this.closeModal();
        this.loadItems();
        Swal.fire({ icon: 'success', title: 'Guardado', text: 'El requisito fue guardado correctamente.', timer: 2000, showConfirmButton: false });
      },
      error: () => {
        this.saving.set(false);
        Swal.fire('Error', 'No se pudo guardar. Intente nuevamente.', 'error');
      }
    });
  }

  delete(item: CatalogoTarea) {
    Swal.fire({
      title: '¿Eliminar requisito?',
      html: `Se eliminará <b>${item.titulo}</b>. Esta acción no se puede deshacer.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        this.service.delete(item.id!).subscribe({
          next: () => {
            this.loadItems();
            Swal.fire({ icon: 'success', title: 'Eliminado', timer: 1500, showConfirmButton: false });
          },
          error: () => Swal.fire('Error', 'No se pudo eliminar.', 'error')
        });
      }
    });
  }
}

