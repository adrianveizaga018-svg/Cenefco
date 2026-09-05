import { Component, inject, signal, OnInit, ChangeDetectorRef } from '@angular/core';
import { NgClass } from '@angular/common';
import { NgIcon } from '@ng-icons/core';
import { FormsModule } from '@angular/forms';
import { Router, ActivatedRoute, RouterLink } from '@angular/router';
import { WhatsappService } from '../../application/services/whatsapp.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { IntentPayload, NluContextRef } from '../../domain/models/whatsapp.model';
import { generateSlug } from '../../../utils/slug';

@Component({
  selector: 'app-intent-form',
  standalone: true,
  imports: [NgClass, NgIcon, FormsModule, RouterLink, PageTitle],
  templateUrl: './intent-form.html',
})
export class IntentForm implements OnInit {
  private svc    = inject(WhatsappService);
  private toast  = inject(ToastService);
  private router = inject(Router);
  private route  = inject(ActivatedRoute);
  private cdr    = inject(ChangeDetectorRef);

  editId: number | null = null;
  loading  = signal(false);
  saving   = signal(false);

  nombre   = '';
  slug     = '';
  dominio  = 'general';
  prioridad = 400;
  accion   = '';
  activo   = true;
  orden    = 0;

  eventos:             string[]        = [];
  input_contexts:      string[]        = [];
  output_contexts:     NluContextRef[] = [];
  frases_entrenamiento: string[]       = [];
  respuestas:          string[]        = [];

  newEvento       = '';
  newInputCtx     = '';
  newOutputCtx    = '';
  newOutputLife   = 3;
  newFrase        = '';
  newRespuesta    = '';

  readonly DOMAINS = ['general', 'academico', 'contenido'];
  readonly ACCIONES = [
    'handleSaludo', 'handlePresentacion', 'handleCursos', 'handleCursosInteres',
    'handleInscripciones', 'handleInscripcionConfirmar', 'handlePagos',
    'handlePagosMetodo', 'handleDocentes', 'handleNoticias', 'handleBoletines',
    'handleEventos', 'handleHorario', 'handleUbicacion', 'handleSoporte',
    'directResponse',
  ];

  get isEdit(): boolean { return this.editId !== null; }
  get title(): string   { return this.isEdit ? 'Editar Intent' : 'Nuevo Intent'; }

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.editId = parseInt(id);
      this.loadIntent(this.editId);
    }
  }

  private loadIntent(id: number) {
    this.loading.set(true);
    this.svc.getIntentById(id).subscribe({
      next: r => {
        this.nombre   = r.nombre;
        this.slug     = r.slug;
        this.dominio  = r.dominio;
        this.prioridad = r.prioridad;
        this.accion   = r.accion;
        this.activo   = r.activo;
        this.orden    = r.orden;
        this.eventos             = [...(r.eventos ?? [])];
        this.input_contexts      = [...(r.input_contexts ?? [])];
        this.output_contexts     = (r.output_contexts ?? []).map(c => ({ ...c }));
        this.frases_entrenamiento = [...(r.frases_entrenamiento ?? [])];
        this.respuestas          = [...(r.respuestas ?? [])];
        this.loading.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar el intent.');
        this.loading.set(false);
        this.router.navigate(['/cenefco/intents']);
      },
    });
  }

  autoSlug() {
    if (!this.isEdit) {
      this.slug = generateSlug(this.nombre ?? '');
    }
  }

  addEvento() {
    const v = this.newEvento.trim().toUpperCase();
    if (v && !this.eventos.includes(v)) { this.eventos = [...this.eventos, v]; }
    this.newEvento = '';
    this.cdr.detectChanges();
  }

  removeEvento(i: number) {
    this.eventos = this.eventos.filter((_, idx) => idx !== i);
    this.cdr.detectChanges();
  }

  addInputCtx() {
    const v = this.newInputCtx.trim().toLowerCase().replace(/\s+/g, '-');
    if (v && !this.input_contexts.includes(v)) { this.input_contexts = [...this.input_contexts, v]; }
    this.newInputCtx = '';
    this.cdr.detectChanges();
  }

  removeInputCtx(i: number) {
    this.input_contexts = this.input_contexts.filter((_, idx) => idx !== i);
    this.cdr.detectChanges();
  }

  addOutputCtx() {
    const v = this.newOutputCtx.trim().toLowerCase().replace(/\s+/g, '-');
    if (v && !this.output_contexts.find(c => c.name === v)) {
      this.output_contexts = [...this.output_contexts, { name: v, lifespan: this.newOutputLife }];
    }
    this.newOutputCtx = '';
    this.newOutputLife = 3;
    this.cdr.detectChanges();
  }

  removeOutputCtx(i: number) {
    this.output_contexts = this.output_contexts.filter((_, idx) => idx !== i);
    this.cdr.detectChanges();
  }

  addFrase() {
    const v = this.newFrase.trim();
    if (v && !this.frases_entrenamiento.includes(v)) {
      this.frases_entrenamiento = [...this.frases_entrenamiento, v];
    }
    this.newFrase = '';
    this.cdr.detectChanges();
  }

  removeFrase(i: number) {
    this.frases_entrenamiento = this.frases_entrenamiento.filter((_, idx) => idx !== i);
    this.cdr.detectChanges();
  }

  addRespuesta() {
    const v = this.newRespuesta.trim();
    console.log('[IntentForm] addRespuesta - newRespuesta:', JSON.stringify(v), '| respuestas antes:', this.respuestas.length);
    if (v) { this.respuestas = [...this.respuestas, v]; }
    this.newRespuesta = '';
    console.log('[IntentForm] addRespuesta - respuestas después:', this.respuestas);
    this.cdr.detectChanges();
  }

  removeRespuesta(i: number) {
    this.respuestas = this.respuestas.filter((_, idx) => idx !== i);
    this.cdr.detectChanges();
  }

  submit() {
    if (!this.nombre || !this.slug || !this.accion) {
      this.toast.warning('Faltan campos', 'Nombre, slug y acción son requeridos.');
      return;
    }
    if (this.newFrase.trim())     this.addFrase();
    if (this.newRespuesta.trim()) this.addRespuesta();
    if (this.newEvento.trim())    this.addEvento();
    if (this.newInputCtx.trim())  this.addInputCtx();
    if (this.newOutputCtx.trim()) this.addOutputCtx();

    const payload: IntentPayload = {
      nombre:              this.nombre,
      slug:                this.slug,
      dominio:             this.dominio,
      prioridad:           Number(this.prioridad),
      accion:              this.accion,
      activo:              Boolean(this.activo),
      orden:               Number(this.orden),
      eventos:             this.eventos,
      input_contexts:      this.input_contexts,
      output_contexts:     this.output_contexts,
      frases_entrenamiento: this.frases_entrenamiento,
      respuestas:          this.respuestas,
    };

    console.log('[IntentForm] submit payload:', JSON.stringify(payload, null, 2));

    this.saving.set(true);
    const req = this.isEdit
      ? this.svc.updateIntent(this.editId!, payload)
      : this.svc.createIntent(payload);

    req.subscribe({
      next: (r) => {
        console.log('[IntentForm] save success:', r);
        this.toast.success(this.isEdit ? 'Actualizado' : 'Creado', `Intent "${this.nombre}" guardado.`);
        this.saving.set(false);
        this.router.navigate(['/cenefco/intents']);
      },
      error: (err) => {
        console.error('[IntentForm] save error:', err);
        const msg = err?.error?.errors
          ? Object.values(err.error.errors).flat().join(' ')
          : err?.error?.message ?? 'Error al guardar.';
        this.toast.error('Error', msg);
        this.saving.set(false);
        this.cdr.detectChanges();
      },
    });
  }
}
