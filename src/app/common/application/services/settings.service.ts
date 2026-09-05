import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { CobroEstadoSettings, CobroEstadoSettingsResponse, GeneralSettings, SettingsResponse, UpdateSettingsRequest } from '../../domain/models/settings.model';

@Injectable({
  providedIn: 'root'
})
export class SettingsService {
  private readonly http = inject(HttpClient);
  private readonly apiUrl = '/api/v1/settings';

  getSettings(): Observable<GeneralSettings> {
    return this.http.get<GeneralSettings>(this.apiUrl);
  }

  updateSettings(settings: UpdateSettingsRequest): Observable<SettingsResponse> {
    const formData = new FormData();

    if (settings.site_name !== undefined)        formData.append('site_name', settings.site_name!);
    if (settings.contact_email !== undefined)    formData.append('contact_email', settings.contact_email!);
    if (settings.items_per_page !== undefined)   formData.append('items_per_page', String(settings.items_per_page));
    if (settings.site_active !== undefined)      formData.append('site_active', settings.site_active ? '1' : '0');
    if (settings.maintenance_mode !== undefined) formData.append('maintenance_mode', settings.maintenance_mode ? '1' : '0');
    if (settings.site_logo instanceof File)      formData.append('site_logo', settings.site_logo);

    return this.http.post<SettingsResponse>(`${this.apiUrl}?_method=PUT`, formData);
  }

  getCobroEstadoSettings(): Observable<CobroEstadoSettings> {
    return this.http.get<CobroEstadoSettings>(`${this.apiUrl}/cobro-estado`);
  }

  updateCobroEstadoSettings(settings: CobroEstadoSettings): Observable<CobroEstadoSettingsResponse> {
    return this.http.put<CobroEstadoSettingsResponse>(`${this.apiUrl}/cobro-estado`, settings);
  }
}
