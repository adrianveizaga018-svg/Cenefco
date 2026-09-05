import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { WhatsappAsesor, WhatsappConversacion, WhatsappMensaje, WhatsappEtiqueta, BaileysStatus, ChatbotStats, WhatsappCuenta, NluIntent, NluTestResult, NluContextRef, IntentRecord, IntentPayload } from '../../domain/models/whatsapp.model';

@Injectable({ providedIn: 'root' })
export class WhatsappService {
  private http = inject(HttpClient);

  getConversaciones(params: {
    pageIndex?: number;
    pageSize?: number;
    query?: string;
    estado?: string;
    etiqueta_id?: string | number;
  }): Observable<{ data: WhatsappConversacion[]; total: number }> {
    let p = new HttpParams()
      .set('pageIndex', params.pageIndex ?? 1)
      .set('pageSize', params.pageSize ?? 15);

    if (params.query)       p = p.set('query', params.query);
    if (params.estado)      p = p.set('estado', params.estado);
    if (params.etiqueta_id) p = p.set('etiqueta_id', String(params.etiqueta_id));

    return this.http.get<{ data: WhatsappConversacion[]; total: number }>(
      '/api/v1/whatsapp/conversaciones', { params: p }
    );
  }

  getMensajes(conversacionId: number): Observable<{ data: WhatsappMensaje[]; phone: string; phone_display: string | null; nombre: string | null; estado: string; asesor_id: number | null; asesor: WhatsappAsesor | null }> {
    return this.http.get<any>(
      `/api/v1/whatsapp/conversaciones/${conversacionId}/mensajes`
    );
  }

  marcarAtendido(conversacionId: number): Observable<{ message: string }> {
    return this.http.post<{ message: string }>(
      `/api/v1/whatsapp/conversaciones/${conversacionId}/atendido`, {}
    );
  }

  getEtiquetas(): Observable<{ data: WhatsappEtiqueta[] }> {
    return this.http.get<{ data: WhatsappEtiqueta[] }>('/api/v1/whatsapp/etiquetas');
  }

  crearEtiqueta(nombre: string, color: string): Observable<WhatsappEtiqueta> {
    return this.http.post<WhatsappEtiqueta>('/api/v1/whatsapp/etiquetas', { nombre, color });
  }

  actualizarEtiqueta(id: number, nombre: string, color: string): Observable<WhatsappEtiqueta> {
    return this.http.put<WhatsappEtiqueta>(`/api/v1/whatsapp/etiquetas/${id}`, { nombre, color });
  }

  eliminarEtiqueta(id: number): Observable<void> {
    return this.http.delete<void>(`/api/v1/whatsapp/etiquetas/${id}`);
  }

  asignarEtiquetas(conversacionId: number, etiquetaIds: number[]): Observable<{ etiquetas: WhatsappEtiqueta[] }> {
    return this.http.post<{ etiquetas: WhatsappEtiqueta[] }>(
      `/api/v1/whatsapp/conversaciones/${conversacionId}/etiquetas`,
      { etiqueta_ids: etiquetaIds }
    );
  }

  getTodosPhones(): Observable<{ phones: string[] }> {
    return this.http.get<{ phones: string[] }>('/api/v1/whatsapp/phones');
  }

  enviar(phone: string, mensaje: string): Observable<any> {
    return this.http.post('/api/v1/whatsapp/enviar', { phone, mensaje });
  }

  enviarMasivo(phones: string[], mensaje: string): Observable<{
    exitosos: number;
    fallidos: number;
    detalle_fallidos: { phone: string; error: string }[];
  }> {
    return this.http.post<any>('/api/v1/whatsapp/enviar-masivo', { phones, mensaje });
  }

  enviarMedia(phones: string[], tipo: 'image' | 'document', archivo: File, caption = '', filename = ''): Observable<{
    exitosos: number;
    fallidos: number;
    detalle_fallidos: { phone: string; error: string }[];
  }> {
    const form = new FormData();
    phones.forEach(p => form.append('phones[]', p));
    form.append('tipo', tipo);
    form.append('archivo', archivo, archivo.name);
    if (caption)  form.append('caption', caption);
    if (filename) form.append('filename', filename);
    return this.http.post<any>('/api/v1/whatsapp/enviar-media', form);
  }

  enviarPlantilla(phone: string, plantilla: string, params: Record<string, string>): Observable<any> {
    return this.http.post('/api/v1/whatsapp/plantilla', { phone, plantilla, params });
  }

  private readonly botBase = '/whatsapp-bot';

  baileysStatus(): Observable<BaileysStatus> {
    return this.http.get<BaileysStatus>(`${this.botBase}/status`);
  }

  baileysQr(): Observable<{ qr: string }> {
    return this.http.get<{ qr: string }>(`${this.botBase}/qr`);
  }

  baileysDesconectar(): Observable<{ message: string }> {
    return this.http.post<{ message: string }>(`${this.botBase}/disconnect`, {});
  }

  botProcessStatus(): Observable<{ running: boolean; pid: string | null }> {
    return this.http.get<{ running: boolean; pid: string | null }>('/api/v1/whatsapp/bot/process-status');
  }

  botStart(): Observable<{ message: string; running: boolean }> {
    return this.http.post<{ message: string; running: boolean }>('/api/v1/whatsapp/bot/start', {});
  }

  botStop(): Observable<{ message: string; running: boolean }> {
    return this.http.post<{ message: string; running: boolean }>('/api/v1/whatsapp/bot/stop', {});
  }

  botLogs(): Observable<{ logs: string }> {
    return this.http.get<{ logs: string }>('/api/v1/whatsapp/bot/logs');
  }

  botFlushCache(): Observable<{ message: string }> {
    return this.http.post<{ message: string }>('/api/v1/whatsapp/bot/flush-cache', {});
  }

  botConnectActive(): Observable<{ message: string }> {
    return this.http.post<{ message: string }>('/api/v1/whatsapp/bot/connect-active', {});
  }

  botDisconnect(): Observable<{ message: string }> {
    return this.http.post<{ message: string }>('/api/v1/whatsapp/bot/disconnect', {});
  }

  getChatbotStats(): Observable<ChatbotStats> {
    return this.http.get<ChatbotStats>(`${this.botBase}/stats`);
  }

  getCuentas(): Observable<{ data: WhatsappCuenta[] }> {
    return this.http.get<{ data: WhatsappCuenta[] }>(`${this.botBase}/cuentas`);
  }

  createCuenta(nombre: string): Observable<WhatsappCuenta> {
    return this.http.post<WhatsappCuenta>(`${this.botBase}/cuentas`, { nombre });
  }

  deleteCuenta(id: string): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.botBase}/cuentas/${id}`);
  }

  getCuentaStatus(id: string): Observable<{ status: string; hasQr: boolean; phone: string | null }> {
    return this.http.get<any>(`${this.botBase}/cuentas/${id}/status`);
  }

  getCuentaQr(id: string): Observable<{ qr: string }> {
    return this.http.get<{ qr: string }>(`${this.botBase}/cuentas/${id}/qr`);
  }

  connectCuenta(id: string): Observable<{ message: string }> {
    return this.http.post<{ message: string }>(`${this.botBase}/cuentas/${id}/connect`, {});
  }

  disconnectCuenta(id: string): Observable<{ message: string }> {
    return this.http.post<{ message: string }>(`${this.botBase}/cuentas/${id}/disconnect`, {});
  }



  getAsesores(): Observable<{ data: WhatsappAsesor[] }> {
    return this.http.get<{ data: WhatsappAsesor[] }>('/api/v1/asesores');
  }

  createAsesor(data: Partial<WhatsappAsesor>): Observable<WhatsappAsesor> {
    return this.http.post<WhatsappAsesor>('/api/v1/asesores', data);
  }

  updateAsesor(id: number, data: Partial<WhatsappAsesor>): Observable<WhatsappAsesor> {
    return this.http.put<WhatsappAsesor>(`/api/v1/asesores/${id}`, data);
  }

  deleteAsesor(id: number): Observable<void> {
    return this.http.delete<void>(`/api/v1/asesores/${id}`);
  }

  getIntents(): Observable<{ intents: NluIntent[] }> {
    return this.http.get<{ data: IntentRecord[] }>('/api/v1/intents').pipe(
      map(r => ({
        intents: r.data.map(i => ({
          name:            i.nombre,
          slug:            i.slug,
          domain:          i.dominio,
          priority:        i.prioridad,
          events:          i.eventos ?? [],
          inputContexts:   i.input_contexts ?? [],
          outputContexts:  i.output_contexts ?? [],
          trainingCount:   (i.frases_entrenamiento ?? []).length,
          trainingPhrases: i.frases_entrenamiento ?? [],
          responsesCount:  (i.respuestas ?? []).length,
          responses:       i.respuestas ?? [],
          action:          i.accion,
        })),
      }))
    );
  }

  testNlu(text: string, contextStack: NluContextRef[] = []): Observable<NluTestResult> {
    return this.http.post<NluTestResult>(`${this.botBase}/test-nlu`, { text, context_stack: contextStack });
  }

  private readonly intentBase = '/api/v1/intents';

  getIntentList(params: { query?: string; dominio?: string } = {}): Observable<{ data: IntentRecord[]; total: number }> {
    let p = new HttpParams();
    if (params.query)   p = p.set('query', params.query);
    if (params.dominio) p = p.set('dominio', params.dominio);
    return this.http.get<{ data: IntentRecord[]; total: number }>(this.intentBase, { params: p });
  }

  getIntentById(id: number): Observable<IntentRecord> {
    return this.http.get<IntentRecord>(`${this.intentBase}/${id}`);
  }

  createIntent(payload: IntentPayload): Observable<IntentRecord> {
    return this.http.post<IntentRecord>(this.intentBase, payload);
  }

  updateIntent(id: number, payload: IntentPayload): Observable<IntentRecord> {
    return this.http.put<IntentRecord>(`${this.intentBase}/${id}`, payload);
  }

  deleteIntent(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.intentBase}/${id}`);
  }

  toggleIntent(id: number): Observable<{ activo: boolean }> {
    return this.http.patch<{ activo: boolean }>(`${this.intentBase}/${id}/toggle`, {});
  }
}
