import { Injectable, inject } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import Swal from 'sweetalert2';
import { firstValueFrom } from 'rxjs';
import { FichaEstudianteService } from './ficha-estudiante.service';
import { extractErrorMessage } from '../../../utils/http-error';

/**
 * Diálogo único para emitir el certificado de UNA inscripción.
 * Lo usan la ficha del estudiante y el detalle de inscripción.
 * Solo ofrece las plantillas asignadas al programa; la API vuelve a validar el pago.
 */
@Injectable({ providedIn: 'root' })
export class EmisionCertificadoService {
  private fichas = inject(FichaEstudianteService);

  /** Devuelve true si se emitió un certificado. */
  async emitir(idIns: number, nombreEstudiante: string): Promise<boolean> {
    Swal.fire({ title: 'Revisando...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });

    let opciones;
    try {
      opciones = await firstValueFrom(this.fichas.getOpcionesCertificado(idIns));
    } catch (err) {
      Swal.fire('Error', extractErrorMessage(err as HttpErrorResponse, 'No se pudo revisar la inscripción.'), 'error');
      return false;
    }

    const yaEmitido = opciones.emitidos[0];
    if (yaEmitido) {
      const r = await Swal.fire({
        icon: 'info',
        title: 'Certificado ya emitido',
        html: `<p class="text-sm text-gray-700">${esc(nombreEstudiante)} ya tiene su certificado de este programa.</p>
               <p class="text-xs text-gray-500 mt-2">Código: <strong>${esc(yaEmitido.codigo_verificacion)}</strong></p>`,
        showCancelButton: !!yaEmitido.archivo_url,
        confirmButtonText: yaEmitido.archivo_url ? 'Abrir certificado' : 'Entendido',
        cancelButtonText: 'Cerrar',
        confirmButtonColor: '#0B4258',
      });
      if (r.isConfirmed && yaEmitido.archivo_url) window.open(yaEmitido.archivo_url, '_blank', 'noopener');
      return false;
    }

    if (!opciones.puede_emitir) {
      Swal.fire({ icon: 'warning', title: 'No se puede emitir todavía', text: opciones.motivo ?? '', confirmButtonColor: '#0B4258' });
      return false;
    }

    const unica = opciones.plantillas.length === 1;
    const r = await Swal.fire({
      icon: 'question',
      title: '¿Emitir certificado?',
      html: `<p class="text-sm text-gray-700 mb-3">Se generará el certificado de <strong>${esc(nombreEstudiante)}</strong>.</p>
             <p class="text-xs text-green-700 mb-3">✓ Pago completo</p>
             <div class="text-left">
               <label for="plantilla-cert" class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Certificado</label>
               <select id="plantilla-cert" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" ${unica ? 'disabled' : ''}>
                 ${opciones.plantillas.map(p => `<option value="${p.id}">${esc(p.nombre)}</option>`).join('')}
               </select>
             </div>`,
      showCancelButton: true,
      confirmButtonText: 'Emitir certificado',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#0B4258',
      preConfirm: () => Number((document.getElementById('plantilla-cert') as HTMLSelectElement | null)?.value),
    });
    if (!r.isConfirmed || !r.value) return false;

    Swal.fire({ title: 'Generando certificado...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });
    try {
      const res = await firstValueFrom(this.fichas.emitirCertificado(idIns, r.value));
      const archivo = res.certificado?.archivo_url;
      const fin = await Swal.fire({
        icon: 'success',
        title: '¡Certificado emitido!',
        html: `<p class="text-sm text-gray-700">Código de verificación: <strong>${esc(res.certificado?.codigo_verificacion ?? '')}</strong></p>`,
        showCancelButton: !!archivo,
        confirmButtonText: archivo ? 'Abrir para imprimir' : 'Listo',
        cancelButtonText: 'Cerrar',
        confirmButtonColor: '#0B4258',
      });
      if (fin.isConfirmed && archivo) window.open(archivo, '_blank', 'noopener');
      return true;
    } catch (err) {
      Swal.fire('No se emitió', extractErrorMessage(err as HttpErrorResponse, 'No se pudo emitir el certificado.'), 'error');
      return false;
    }
  }
}

const esc = (texto: string): string =>
  texto.replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]!));
