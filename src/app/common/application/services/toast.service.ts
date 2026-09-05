import { Injectable, signal } from '@angular/core';

export type ToastType = 'success' | 'error' | 'warning' | 'info';

export interface Toast {
  id: string;
  type: ToastType;
  title: string;
  message?: string;
  duration: number;
}

@Injectable({ providedIn: 'root' })
export class ToastService {
  toasts = signal<Toast[]>([]);

  success(title: string, message?: string, duration = 4000): void {
    this.add({ type: 'success', title, message, duration });
  }

  error(title: string, message?: string, duration = 5000): void {
    this.add({ type: 'error', title, message, duration });
  }

  warning(title: string, message?: string, duration = 4000): void {
    this.add({ type: 'warning', title, message, duration });
  }

  info(title: string, message?: string, duration = 4000): void {
    this.add({ type: 'info', title, message, duration });
  }

  dismiss(id: string): void {
    this.toasts.update(list => list.filter(t => t.id !== id));
  }

  private generateId(): string {
    if (typeof crypto !== 'undefined' && crypto.randomUUID) {
      return crypto.randomUUID();
    }
    return Math.random().toString(36).slice(2) + Date.now().toString(36);
  }

  private add(toast: Omit<Toast, 'id'>): void {
    const id = this.generateId();
    this.toasts.update(list => [...list, { ...toast, id }]);
    setTimeout(() => this.dismiss(id), toast.duration);
  }
}
