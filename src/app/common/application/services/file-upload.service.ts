import { Injectable, inject } from '@angular/core'
import { HttpClient, HttpErrorResponse } from '@angular/common/http'
import { Observable } from 'rxjs'
import { extractErrorMessage } from '../../../utils/http-error'
import { ToastService } from './toast.service'
import { signal, Signal } from '@angular/core'

export interface UploadResult {
    url: string
}

@Injectable({ providedIn: 'root' })
export class FileUploadService {
    private http  = inject(HttpClient)
    private toast = inject(ToastService)

    private readonly imageUrl = '/api/v1/upload/image'
    private readonly fileUrl  = '/api/v1/upload/file'

    uploadImage(file: File): Observable<UploadResult> {
        return this.http.post<UploadResult>(this.imageUrl, this.toFormData(file))
    }

    uploadFile(file: File): Observable<UploadResult> {
        return this.http.post<UploadResult>(this.fileUrl, this.toFormData(file))
    }

    handleImageSelect(
        event: Event,
        opts: {
            preview:   ReturnType<typeof signal<string | null>>
            uploading: ReturnType<typeof signal<boolean>>
            onSuccess: (url: string) => void
            fallbackMsg?: string
        }
    ): void {
        const input = event.target as HTMLInputElement
        const file  = input.files?.[0]
        if (!file) return

        const reader = new FileReader()
        reader.onload = (e) => opts.preview.set(e.target?.result as string)
        reader.readAsDataURL(file)

        opts.uploading.set(true)
        this.uploadImage(file).subscribe({
            next: (res) => {
                opts.onSuccess(res.url)
                opts.uploading.set(false)
            },
            error: (err: HttpErrorResponse) => {
                this.toast.error('Error', extractErrorMessage(err, opts.fallbackMsg ?? 'No se pudo subir la imagen'))
                opts.preview.set(null)
                opts.uploading.set(false)
                input.value = ''
            },
        })
    }

    handleFileSelect(
        event: Event,
        opts: {
            uploading:  ReturnType<typeof signal<boolean>>
            fileName?:  ReturnType<typeof signal<string | null>>
            onSuccess:  (url: string) => void
            fallbackMsg?: string
        }
    ): void {
        const input = event.target as HTMLInputElement
        const file  = input.files?.[0]
        if (!file) return

        if (opts.fileName) opts.fileName.set(file.name)
        opts.uploading.set(true)

        this.uploadFile(file).subscribe({
            next: (res) => {
                opts.onSuccess(res.url)
                opts.uploading.set(false)
            },
            error: (err: HttpErrorResponse) => {
                this.toast.error('Error', extractErrorMessage(err, opts.fallbackMsg ?? 'No se pudo subir el archivo'))
                if (opts.fileName) opts.fileName.set(null)
                opts.uploading.set(false)
                input.value = ''
            },
        })
    }

    private toFormData(file: File): FormData {
        const fd = new FormData()
        fd.append('file', file)
        return fd
    }
}
