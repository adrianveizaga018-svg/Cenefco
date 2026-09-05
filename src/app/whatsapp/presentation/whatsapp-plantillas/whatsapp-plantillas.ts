import { Component, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { WhatsappService } from '../../../whatsapp/application/services/whatsapp.service';
import { PageTitle } from '../../../common/components/page-title/page-title';

type PlantillaId = 'confirmacion' | 'entrega' | 'promocion';

@Component({
  selector: 'app-whatsapp-plantillas',
  imports: [FormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './whatsapp-plantillas.html',
  styles: ``
})
export class WhatsappPlantillas {
  private service = inject(WhatsappService);

  plantillaSeleccionada: PlantillaId = 'confirmacion';
  phone        = '';
  numeroPedido = '';
  total        = '';
  descuento    = '';
  fechaFin     = '';

  loading = signal(false);
  success = signal('');
  error   = signal('');

  readonly plantillas: { id: PlantillaId; label: string; desc: string; icon: string }[] = [
    {
      id: 'confirmacion',
      label: 'Confirmación de Pedido',
      desc: 'Notifica al cliente que su pedido fue recibido con el número y total.',
      icon: 'lucideCheckCircle',
    },
    {
      id: 'entrega',
      label: 'Estado de Entrega',
      desc: 'Informa al cliente que su pedido está en camino o fue entregado.',
      icon: 'lucideTruck',
    },
    {
      id: 'promocion',
      label: 'Promoción Especial',
      desc: 'Envía una oferta con descuento y fecha de vencimiento.',
      icon: 'lucideTag',
    },
  ];

  enviar(): void {
    if (!this.phone) { this.error.set('Ingresa el número de teléfono.'); return; }

    const params = this.buildParams();
    if (!params) return;

    this.loading.set(true);
    this.success.set('');
    this.error.set('');

    this.service.enviarPlantilla(this.phone, this.plantillaSeleccionada, params).subscribe({
      next: () => {
        this.loading.set(false);
        this.success.set('Plantilla enviada correctamente.');
        this.reset();
      },
      error: (err) => {
        this.loading.set(false);
        this.error.set(err?.error?.message ?? 'Error al enviar la plantilla.');
      },
    });
  }

  private buildParams(): Record<string, string> | null {
    switch (this.plantillaSeleccionada) {
      case 'confirmacion':
        if (!this.numeroPedido || !this.total) { this.error.set('Completa el número de pedido y total.'); return null; }
        return { numero_pedido: this.numeroPedido, total: this.total };
      case 'entrega':
        if (!this.numeroPedido) { this.error.set('Ingresa el número de pedido.'); return null; }
        return { numero_pedido: this.numeroPedido };
      case 'promocion':
        if (!this.descuento || !this.fechaFin) { this.error.set('Completa el descuento y la fecha de vencimiento.'); return null; }
        return { descuento: this.descuento, fecha_fin: this.fechaFin };
    }
  }

  private reset(): void {
    this.phone = '';
    this.numeroPedido = '';
    this.total = '';
    this.descuento = '';
    this.fechaFin = '';
  }
}
