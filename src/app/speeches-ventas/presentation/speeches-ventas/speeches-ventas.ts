import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { FormsModule } from '@angular/forms';
import { SlicePipe } from '@angular/common';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { Pagination } from '../../../common/components/pagination/pagination';
import { SpeechVentasService } from '../../application/services/speech-ventas.service';
import { SpeechVentas } from '../../domain/models/speech-ventas.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { WhatsappBotBanner } from '../../../whatsapp/components/whatsapp-bot-banner/whatsapp-bot-banner';

@Component({
  selector: 'app-speeches-ventas',
  standalone: true,
  imports: [NgIcon, RouterLink, FormsModule, SlicePipe, PageTitle, Pagination, WhatsappBotBanner],
  templateUrl: './speeches-ventas.html',
})
export class SpeechesVentas implements OnInit {
  private service = inject(SpeechVentasService);
  private toast   = inject(ToastService);
  private cdr     = inject(ChangeDetectorRef);

  items      = signal<SpeechVentas[]>([]);
  total      = signal(0);
  loading    = signal(true);
  categorias = signal<string[]>([]);

  query      = '';
  categoria  = '';
  activo     = '';
  pageIndex  = signal(1);
  pageSize   = 20;

  eliminandoId = signal<number | null>(null);

  ngOnInit(): void {
    this.service.getCategorias().subscribe({
      next: cats => { this.categorias.set(cats); this.cdr.detectChanges(); },
      error: () => {},
    });
    this.cargar();
  }

  cargar(): void {
    this.loading.set(true);
    this.service.getAll({
      pageIndex:  this.pageIndex(),
      pageSize:   this.pageSize,
      query:      this.query || undefined,
      categoria:  this.categoria || undefined,
      activo:     this.activo !== '' ? this.activo : undefined,
    }).subscribe({
      next: res => {
        this.items.set(res.data);
        this.total.set(res.total);
        this.loading.set(false);
        this.cdr.detectChanges();
      },
      error: (err) => {
        console.error('[SpeechesVentas] error al cargar:', err);
        this.loading.set(false);
        this.toast.error('Error', 'No se pudieron cargar los speeches.');
        this.cdr.detectChanges();
      },
    });
  }

  buscar(): void {
    this.pageIndex.set(1);
    this.cargar();
  }

  limpiar(): void {
    this.query     = '';
    this.categoria = '';
    this.activo    = '';
    this.pageIndex.set(1);
    this.cargar();
  }

  onPageChange(p: number): void {
    this.pageIndex.set(p);
    this.cargar();
  }

  toggle(item: SpeechVentas): void {
    this.service.toggleActivo(item.id).subscribe({
      next: updated => {
        this.items.update(list => list.map(i => i.id === updated.id ? updated : i));
        this.cdr.detectChanges();
      },
      error: () => this.toast.error('Error', 'No se pudo cambiar el estado.'),
    });
  }

  eliminar(item: SpeechVentas): void {
    if (!confirm(`¿Eliminar "${item.titulo}"?`)) return;
    this.eliminandoId.set(item.id);
    this.service.delete(item.id).subscribe({
      next: () => {
        this.items.update(list => list.filter(i => i.id !== item.id));
        this.total.update(t => t - 1);
        this.eliminandoId.set(null);
        this.toast.success('Eliminado', 'Speech eliminado correctamente.');
        this.cdr.detectChanges();
      },
      error: () => {
        this.eliminandoId.set(null);
        this.toast.error('Error', 'No se pudo eliminar.');
        this.cdr.detectChanges();
      },
    });
  }

  categoriaColor(cat: string | null): string {
    const map: Record<string, string> = {
      'objeciones':    'bg-red-100 text-red-700',
      'cierre':        'bg-green-100 text-green-700',
      'seguimiento':   'bg-blue-100 text-blue-700',
      'presentacion':  'bg-purple-100 text-purple-700',
      'beneficios':    'bg-amber-100 text-amber-700',
      'bienvenida':    'bg-teal-100 text-teal-700',
      'reactivacion':  'bg-orange-100 text-orange-700',
    };
    return cat ? (map[cat.toLowerCase()] ?? 'bg-default-100 text-default-600') : 'bg-default-100 text-default-500';
  }

  badgeClass(activo: boolean): string {
    return activo ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger';
  }
}
