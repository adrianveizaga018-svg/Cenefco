import { Component, EventEmitter, Input, Output, signal, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';

@Component({
  selector: 'app-curso-imagenes',
  standalone: true,
  imports: [NgIcon],
  templateUrl: './curso-imagenes.html',
})
export class CursoImagenes {
  private http = inject(HttpClient);

  @Input() set urls(val: string[] | null) { this._urls.set(val ?? []); }
  @Output() urlsChange = new EventEmitter<string[]>();

  _urls    = signal<string[]>([]);
  uploading = signal(false);
  error     = signal('');

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    const files = Array.from(input.files ?? []);
    if (!files.length) return;
    input.value = '';

    this.uploading.set(true);
    this.error.set('');

    let pending = files.length;
    files.forEach(file => {
      const form = new FormData();
      form.append('file', file);
      this.http.post<{ url: string }>('/api/v1/upload/image', form).subscribe({
        next: (res) => {
          this._urls.update(list => [...list, res.url]);
          this.urlsChange.emit(this._urls());
          if (--pending === 0) this.uploading.set(false);
        },
        error: () => {
          this.error.set('No se pudo subir una o más imágenes.');
          if (--pending === 0) this.uploading.set(false);
        },
      });
    });
  }

  remove(index: number): void {
    this._urls.update(list => list.filter((_, i) => i !== index));
    this.urlsChange.emit(this._urls());
  }
}
