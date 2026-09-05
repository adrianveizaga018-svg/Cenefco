import { Component, inject, OnInit, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { UsuarioService } from '../../../usuarios/application/services/usuario.service';
import { ToastService } from '../../../common/application/services/toast.service';
import { Rol } from '../../../usuarios/domain/models/usuario.model';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-usuario-edit',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './usuario-edit.html',
})
export class UsuarioEdit implements OnInit {
  private fb      = inject(FormBuilder);
  private service = inject(UsuarioService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private toast   = inject(ToastService);

  id         = 0;
  roles      = signal<Rol[]>([]);
  loading    = signal(true);
  submitting = signal(false);

  readonly tiposUsuario = [
    { value: 'admin',        label: 'Administrador' },
    { value: 'coordinador',  label: 'Coordinador' },
    { value: 'docente',      label: 'Docente' },
    { value: 'participante', label: 'Participante' },
  ];

  form: FormGroup = this.fb.group({
    nombre:   ['', [Validators.required, Validators.maxLength(100)]],
    apellido: ['', [Validators.required, Validators.maxLength(100)]],
    email:    ['', [Validators.required, Validators.email, Validators.maxLength(150)]],
    password: ['', [Validators.minLength(8)]],
    tipo:     ['participante'],
    rol_id:   [null],
    activo:   [true],
  });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.service.getRoles().subscribe({ next: roles => this.roles.set(roles) });
    this.service.getById(this.id).subscribe({
      next: u => {
        this.form.patchValue({
          nombre:   u.nombre,
          apellido: u.apellido,
          email:    u.email,
          tipo:     u.tipo,
          rol_id:   u.rolId,
          activo:   u.activo,
        });
        this.loading.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se encontró el usuario'));
        this.router.navigate(['/cenefco/usuarios']);
      },
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    const { password, ...rest } = this.form.value;
    const payload = password ? { ...rest, password } : rest;
    this.service.update(this.id, payload).subscribe({
      next: () => {
        this.toast.success('¡Actualizado!', 'Usuario actualizado exitosamente');
        this.router.navigate(['/cenefco/usuarios']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el usuario'));
        this.submitting.set(false);
      },
    });
  }
}
