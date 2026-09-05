import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { GeneralSettings } from '../../domain/models/configuracion.model';

@Injectable({ providedIn: 'root' })
export class ConfiguracionService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/settings';

  getSettings(): Observable<GeneralSettings> {
    return this.http.get<GeneralSettings>(this.baseUrl);
  }

  updateSettings(data: Partial<GeneralSettings>): Observable<{ message: string; settings: GeneralSettings }> {
    return this.http.put<{ message: string; settings: GeneralSettings }>(this.baseUrl, data);
  }
}
