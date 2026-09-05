import { Component, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { WhatsappService } from '../../../whatsapp/application/services/whatsapp.service';
import { PageTitle } from '../../../common/components/page-title/page-title';

type Modo = 'individual' | 'masivo';

@Component({
  selector: 'app-whatsapp-enviar',
  imports: [FormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './whatsapp-enviar.html',
  styles: ``
})
export class WhatsappEnviar {
  private service = inject(WhatsappService);

  modo: Modo   = 'individual';
  phone        = '';
  phonesTexto  = '';
  mensaje      = '';

  loading        = signal(false);
  cargandoPhones = signal(false);
  success = signal('');
  error   = signal('');

  resultadoMasivo = signal<{
    exitosos: number;
    fallidos: number;
    detalle_fallidos: { phone: string; error: string }[];
  } | null>(null);

  enviar(): void {
    this.success.set('');
    this.error.set('');
    this.resultadoMasivo.set(null);

    if (!this.mensaje.trim()) {
      this.error.set('El mensaje no puede estar vacío.');
      return;
    }

    if (this.modo === 'individual') {
      this.enviarIndividual();
    } else {
      this.enviarMasivo();
    }
  }

  private enviarIndividual(): void {
    if (!this.phone.trim()) {
      this.error.set('Ingresa el número de teléfono.');
      return;
    }

    this.loading.set(true);
    this.service.enviar(this.phone.trim(), this.mensaje).subscribe({
      next: () => {
        this.loading.set(false);
        this.success.set('Mensaje enviado correctamente.');
        this.reset();
      },
      error: (err) => {
        this.loading.set(false);
        this.error.set(err?.error?.message ?? 'Error al enviar el mensaje.');
      },
    });
  }

  private enviarMasivo(): void {
    const phones = this.phonesTexto
      .split('\n')
      .map(p => p.trim())
      .filter(p => p.length > 0);

    if (phones.length === 0) {
      this.error.set('Ingresa al menos un número de teléfono.');
      return;
    }

    this.loading.set(true);
    this.service.enviarMasivo(phones, this.mensaje).subscribe({
      next: (res) => {
        this.loading.set(false);
        this.resultadoMasivo.set(res);
        this.reset();
      },
      error: (err) => {
        this.loading.set(false);
        this.error.set(err?.error?.message ?? 'Error al enviar los mensajes.');
      },
    });
  }

  contarPhones(): number {
    return this.phonesTexto.split('\n').filter(p => p.trim().length > 0).length;
  }

  cargarTodosPhones(): void {
    this.cargandoPhones.set(true);
    this.service.getTodosPhones().subscribe({
      next: (res) => {
        this.phonesTexto = res.phones.join('\n');
        this.modo = 'masivo';
        this.cargandoPhones.set(false);
      },
      error: () => {
        this.error.set('No se pudieron cargar los números.');
        this.cargandoPhones.set(false);
      },
    });
  }

  private reset(): void {
    this.phone       = '';
    this.phonesTexto = '';
    this.mensaje     = '';
  }
}
