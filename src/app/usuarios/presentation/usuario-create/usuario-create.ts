import { Component, inject, OnInit, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { UsuarioService } from '../../../usuarios/application/services/usuario.service';
import { ToastService } from '../../../common/application/services/toast.service';
import { Rol } from '../../../usuarios/domain/models/usuario.model';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-usuario-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './usuario-create.html',
})
export class UsuarioCreate implements OnInit {
  private fb      = inject(FormBuilder);
  private service = inject(UsuarioService);
  private router  = inject(Router);
  private toast   = inject(ToastService);

  roles      = signal<Rol[]>([]);
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
    password: ['', [Validators.required, Validators.minLength(8)]],
    tipo:     ['participante'],
    rol_id:   [null],
    activo:   [true],
  });

  ngOnInit(): void {
    this.service.getRoles().subscribe({
      next: roles => this.roles.set(roles),
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value).subscribe({
      next: () => {
        this.toast.success('¡Creado!', 'Usuario registrado exitosamente');
        this.router.navigate(['/cenefco/usuarios']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear el usuario'));
        this.submitting.set(false);
      },
    });
  }

  onReset(): void { this.form.reset({ tipo: 'participante', activo: true }); }
}
