import { ChangeDetectorRef, Component, inject } from '@angular/core';
import { DROPZONE_CONFIG, DropzoneConfigInterface, DropzoneModule, } from 'ngx-dropzone-wrapper';
import { NgIcon } from "@ng-icons/core";
import { formatFileSize } from '../../../utils/file-utils';
import { RouterLink } from '@angular/router';

type UploadedFile = {
  name: string;
  size: number;
  type: string;
  dataURL?: string;
  loading?: boolean
};

const DEFAULT_DROPZONE_CONFIG: DropzoneConfigInterface = {
  url: 'https://httpbin.org/post',
  maxFilesize: 50,
  acceptedFiles: 'image/*',
};

@Component({
  selector: 'app-file-uploader',
  imports: [DropzoneModule, NgIcon, RouterLink],
  templateUrl: './file-uploader.html',
  providers: [
    {
      provide: DROPZONE_CONFIG,
      useValue: DEFAULT_DROPZONE_CONFIG,
    },
  ],
  styles: ``
})
export class FileUploader {
  formatFileSize = formatFileSize
  uploadedFiles: UploadedFile[] = [];

  dropzoneConfig: DropzoneConfigInterface = {
    url: 'https://httpbin.org/post',
    maxFilesize: 50,
    addRemoveLinks: true,
    clickable: '.dropzone2',
    previewsContainer: false,
    dictDefaultMessage: '',
  };

  private cdr = inject(ChangeDetectorRef);

  onFileAdded(file: any) {
    const reader = new FileReader();
    reader.onload = (e: ProgressEvent<FileReader>) => {
      const dataUrl = e.target?.result as string;
      this.uploadedFiles.push({
        name: file.name,
        size: file.size,
        type: file.type,
        dataURL: dataUrl,
      });

      this.cdr.detectChanges();
    };
    reader.readAsDataURL(file);
  }

  removeFile(index: number) {
    this.uploadedFiles.splice(index, 1);
    this.cdr.detectChanges();
  }
}
