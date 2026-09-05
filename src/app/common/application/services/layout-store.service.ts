import { Injectable, signal } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

export type LayoutState = {
  direction: 'ltr' | 'rtl';
  theme: 'light' | 'dark' | 'system';
  sidenav: {
    color: 'light' | 'dark';
    size: 'default' | 'hover' | 'hover-active' | 'sm' | 'md' | 'mobile' | 'hidden' | 'offcanvas';
  };
};

const STORAGE_KEY = '__TAILWIK_ANGULAR_CONFIG__';

const defaultState: LayoutState = {
  direction: 'ltr',
  theme: 'light',
  sidenav: {
    color: 'light',
    size: 'default',
  },
};

@Injectable({ providedIn: 'root' })
export class LayoutService {
  state = signal<LayoutState>(this.loadInitialState());
  private html = document.documentElement;

  private layoutStateSubject = new BehaviorSubject<LayoutState>(this.state());
  layoutState$ = this.layoutStateSubject.asObservable();

  constructor() {
    this.applyAllAttributes();
    this.initWindowSize();
  }

  private loadInitialState(): LayoutState {
    try {
      const stored = localStorage.getItem(STORAGE_KEY);
      return stored ? JSON.parse(stored) : defaultState;
    } catch {
      return defaultState;
    }
  }

  private persist() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(this.state()));
    this.layoutStateSubject.next(this.state());
  }

  get theme() {
    return this.state().theme;
  }

  get sidenavSize() {
    return this.state().sidenav.size;
  }

  private setHtmlAttr(attr: string, value: string) {
    this.html.setAttribute(attr, value);
  }

  private persistToStorage() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(this.state()));
  }

  private getSystemTheme(): 'light' | 'dark' {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  private applyAllAttributes() {
    const s = this.state();
    this.html.dir = s.direction;
    this.setHtmlAttr('data-theme', s.theme === 'system' ? this.getSystemTheme() : s.theme);
    this.setHtmlAttr('data-sidenav-color', s.sidenav.color);
    this.setHtmlAttr('data-sidenav-size', s.sidenav.size);
  }

  setDirection(direction: LayoutState['direction']) {
    this.html.dir = direction;
    this.state.update(s => ({ ...s, direction }));
    this.persist();
  }

  setTheme(theme: LayoutState['theme']) {
    const applied = theme === 'system' ? this.getSystemTheme() : theme;
    this.setHtmlAttr('data-theme', applied);
    this.state.update(s => ({ ...s, theme }));
    this.persist();
  }

  setSidenavColor(color: LayoutState['sidenav']['color']) {
    this.setHtmlAttr('data-sidenav-color', color);
    this.state.update(s => ({ ...s, sidenav: { ...s.sidenav, color } }));
    this.persist();
  }

  setSidenavSize(size: LayoutState['sidenav']['size'], persist = true): void {
    this.setHtmlAttr('data-sidenav-size', size);
    if (persist) {
      this.state.update(s => ({
        ...s,
        sidenav: { ...s.sidenav, size },
      }));
      this.persistToStorage();
    }
    this.layoutStateSubject.next({ ...this.state(), sidenav: { ...this.state().sidenav, size } });
  }

  reset() {
    this.state.set(defaultState);
    this.applyAllAttributes();
    this.persist();
  }

  toggleSidebar() {
    const current = this.html.getAttribute('data-sidenav-size') as LayoutState['sidenav']['size'];
    const configSize = this.state().sidenav.size;

    if (current === 'offcanvas') {
      this.showBackdrop();
    } else if (configSize === 'md') {
      this.setSidenavSize(current === 'sm' ? 'md' : 'sm', false);
    } else if (configSize === 'hidden') {
      this.setSidenavSize(current === 'default' ? 'hidden' : 'default', false);
    } else {
      this.setSidenavSize(current === 'sm' ? 'default' : 'sm', true);
    }

    this.html.classList.toggle('sidenav-enable');
  }

  showBackdrop() {
    const backdrop = document.createElement('div');
    backdrop.id = 'custom-backdrop';
    backdrop.className = 'transition duration fixed inset-0 bg-default-900/50 z-40';
    document.body.appendChild(backdrop);

    backdrop.addEventListener('click', () => {
      this.html.classList.remove('sidenav-enable');
      this.hideBackdrop();
    });
  }

  hideBackdrop() {
    const backdrop = document.getElementById('custom-backdrop');
    if (backdrop) {
      document.body.removeChild(backdrop);
    }
  }

  private adjustLayout() {
    const width = window.innerWidth;
    const size = this.state().sidenav.size;

    if (width <= 1140) {
      this.setSidenavSize('offcanvas', false);
    } else {
      this.setSidenavSize(size);
    }
  }

  private initWindowSize() {
    window.addEventListener('resize', () => this.adjustLayout());
    this.adjustLayout();
  }
}
