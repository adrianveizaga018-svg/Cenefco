import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  CrearReunionPayload,
  CrearReunionResponse,
  ZoomMeeting,
  ZoomRecording,
} from '../../domain/models/zoom.model';

@Injectable({ providedIn: 'root' })
export class ZoomService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/zoom';

  listarReuniones(): Observable<{ meetings: ZoomMeeting[] }> {
    return this.http.get<{ meetings: ZoomMeeting[] }>(`${this.baseUrl}/meetings`);
  }

  crearReunion(data: CrearReunionPayload): Observable<CrearReunionResponse> {
    return this.http.post<CrearReunionResponse>(`${this.baseUrl}/meetings`, data);
  }

  grabaciones(): Observable<{ recordings: ZoomRecording[] }> {
    return this.http.get<{ recordings: ZoomRecording[] }>(`${this.baseUrl}/recordings`);
  }
}
