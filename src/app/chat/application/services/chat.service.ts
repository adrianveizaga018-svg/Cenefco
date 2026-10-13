import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';

export type TipoConversacion = 'directo' | 'grupo' | 'anuncios';

export interface Conversacion {
  id: number;
  tipo: TipoConversacion;
  nombre: string;
  descripcion: string | null;
  otro_usuario_id: number | null;
  ultimo_mensaje: { texto: string; autor: string | null; fecha: string } | null;
  no_leidos: number;
  puede_publicar: boolean;
  puede_gestionar: boolean;
}

export interface MensajeChat {
  id: number;
  usuario_id: number;
  autor: string;
  es_mio: boolean;
  cuerpo: string | null;
  eliminado: boolean;
  adjunto: { nombre: string; tipo: string | null; peso: number; es_imagen: boolean } | null;
  responde_a: { id: number; autor: string; cuerpo: string } | null;
  menciones: number[];
  created_at: string;
}

export interface Participante { id: number; nombre: string; rol: 'admin' | 'miembro'; ultimo_leido_id: number; }
export interface PersonaChat { id: number; nombre: string; avatar_url: string | null; }

export interface HiloChat {
  conversacion: { id: number; tipo: TipoConversacion; nombre: string | null; descripcion: string | null; puede_publicar: boolean };
  mensajes: MensajeChat[];
  hay_mas: boolean;
  participantes: Participante[];
}

export interface ResultadoBusqueda {
  id: number;
  conversacion_id: number;
  cuerpo: string | null;
  adjunto_nombre: string | null;
  created_at: string;
  tipo: TipoConversacion;
  conversacion: string | null;
  autor: string;
}

/** Chat interno del personal. */
@Injectable({ providedIn: 'root' })
export class ChatService {
  private http = inject(HttpClient);
  private base = '/api/v1/chat';

  conversaciones(): Observable<{ data: Conversacion[]; administra: boolean }> {
    return this.http.get<{ data: Conversacion[]; administra: boolean }>(`${this.base}/conversaciones`);
  }

  noLeidos(): Observable<{ no_leidos: number }> {
    return this.http.get<{ no_leidos: number }>(`${this.base}/no-leidos`);
  }

  directorio(q = ''): Observable<PersonaChat[]> {
    return this.http.get<PersonaChat[]>(`${this.base}/directorio`, { params: q ? new HttpParams().set('q', q) : undefined });
  }

  abrirDirecto(usuarioId: number): Observable<{ id: number }> {
    return this.http.post<{ id: number }>(`${this.base}/directo`, { usuario_id: usuarioId });
  }

  crearGrupo(nombre: string, descripcion: string | null, miembros: number[]): Observable<{ id: number }> {
    return this.http.post<{ id: number }>(`${this.base}/grupos`, { nombre, descripcion, miembros });
  }

  actualizarGrupo(id: number, datos: { nombre?: string; descripcion?: string | null; agregar?: number[]; quitar?: number[]; archivada?: boolean }): Observable<{ message: string }> {
    return this.http.patch<{ message: string }>(`${this.base}/grupos/${id}`, datos);
  }

  mensajes(id: number, opciones: { antesDe?: number; despuesDe?: number } = {}): Observable<HiloChat> {
    let params = new HttpParams();
    if (opciones.antesDe) params = params.set('antes_de', opciones.antesDe);
    if (opciones.despuesDe) params = params.set('despues_de', opciones.despuesDe);
    return this.http.get<HiloChat>(`${this.base}/conversaciones/${id}/mensajes`, { params });
  }

  enviar(id: number, datos: FormData): Observable<MensajeChat> {
    return this.http.post<MensajeChat>(`${this.base}/conversaciones/${id}/mensajes`, datos);
  }

  marcarLeido(id: number, ultimoId: number): Observable<{ ok: boolean }> {
    return this.http.post<{ ok: boolean }>(`${this.base}/conversaciones/${id}/leido`, { ultimo_id: ultimoId });
  }

  eliminar(idMensaje: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.base}/mensajes/${idMensaje}`);
  }

  /** El adjunto se pide con la sesión: no hay enlaces públicos a los archivos del chat. */
  adjunto(idMensaje: number): Observable<Blob> {
    return this.http.get(`${this.base}/mensajes/${idMensaje}/adjunto`, { responseType: 'blob' });
  }

  buscar(q: string): Observable<{ data: ResultadoBusqueda[] }> {
    return this.http.get<{ data: ResultadoBusqueda[] }>(`${this.base}/buscar`, { params: new HttpParams().set('q', q) });
  }
}
