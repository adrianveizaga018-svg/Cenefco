import { Directive, ElementRef, HostListener, Input } from '@angular/core';

type RestrictType = 'phone' | 'numeric' | 'alphanumeric' | 'alpha' | 'email';

@Directive({
  selector: '[appInputRestrict]',
  standalone: true
})
export class InputRestrictDirective {
  @Input() appInputRestrict: RestrictType | RegExp = 'alphanumeric';

  private patterns: Record<RestrictType, RegExp> = {
    phone: /[^0-9+\-\s()]/g,
    numeric: /[^0-9]/g,
    alphanumeric: /[^a-zA-Z0-9]/g,
    alpha: /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,
    email: /[^a-zA-Z0-9@._\-]/g
  };

  constructor(private el: ElementRef<HTMLInputElement>) {}

  @HostListener('input', ['$event'])
  onInput(event: Event): void {
    const input = this.el.nativeElement;
    const value = input.value;

    const pattern = typeof this.appInputRestrict === 'string'
      ? this.patterns[this.appInputRestrict]
      : this.appInputRestrict;

    const sanitized = value.replace(pattern, '');

    if (value !== sanitized) {
      input.value = sanitized;
      input.dispatchEvent(new Event('input', { bubbles: true }));
    }
  }
}
