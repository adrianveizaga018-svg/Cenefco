import {
  Component,
  Input,
  forwardRef,
  signal,
  computed,
  ElementRef,
  HostListener,
  ViewChild,
  OnChanges,
  SimpleChanges,
  AfterViewInit,
  inject,
} from '@angular/core';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';

export interface SelectOption {
  value: any;
  label: string;
  image?: string;
}

@Component({
  selector: 'app-searchable-select',
  templateUrl: './searchable-select.html',
  providers: [
    {
      provide: NG_VALUE_ACCESSOR,
      useExisting: forwardRef(() => SearchableSelect),
      multi: true,
    },
  ],
})
export class SearchableSelect implements ControlValueAccessor, OnChanges, AfterViewInit {
  @Input() options: SelectOption[] = [];
  @Input() placeholder = 'Seleccione una opción';
  @Input() clearable = true;

  @ViewChild('inputEl') inputEl!: ElementRef<HTMLInputElement>;

  isOpen    = signal(false);
  searchText = signal('');
  selectedValue = signal<any>(null);
  isDisabled = signal(false);
  hasSelection = computed(() => this.selectedValue() !== null && this.selectedValue() !== undefined);

  filteredOptions = computed(() => {
    const q = this.searchText().toLowerCase().trim();
    if (!q) return this.options;
    return this.options.filter((o) => o.label.toLowerCase().includes(q));
  });

  selectedLabel = computed(() =>
    this.options.find((o) => o.value === this.selectedValue())?.label ?? null
  );

  private onChange: (value: any) => void = () => {};
  private onTouched: () => void = () => {};

  private el = inject(ElementRef);

  ngAfterViewInit(): void {
    this.syncInputToLabel();
  }

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['options']) {
      this.syncInputToLabel();
    }
  }

  @HostListener('document:click', ['$event'])
  onDocumentClick(event: MouseEvent): void {
    if (!this.el.nativeElement.contains(event.target)) {
      this.close();
    }
  }

  onInputClick(): void {
    if (this.isDisabled()) return;
    if (!this.isOpen()) {
      this.isOpen.set(true);
      this.searchText.set('');
      if (this.inputEl) {
        this.inputEl.nativeElement.value = '';
        this.inputEl.nativeElement.focus();
      }
    }
  }

  onInputChange(value: string): void {
    this.searchText.set(value);
    if (!this.isOpen()) this.isOpen.set(true);
  }

  close(): void {
    if (!this.isOpen()) return;
    this.isOpen.set(false);
    this.searchText.set('');
    this.onTouched();
    this.syncInputToLabel();
  }

  select(option: SelectOption, event: MouseEvent): void {
    event.preventDefault();
    event.stopPropagation();
    this.selectedValue.set(option.value);
    this.onChange(option.value);
    this.isOpen.set(false);
    this.searchText.set('');
    this.onTouched();
    if (this.inputEl) {
      this.inputEl.nativeElement.value = option.label;
    }
  }

  clear(event: MouseEvent): void {
    event.preventDefault();
    event.stopPropagation();
    this.selectedValue.set(null);
    this.onChange(null);
    this.searchText.set('');
    if (this.inputEl) {
      this.inputEl.nativeElement.value = '';
      if (this.isOpen()) this.inputEl.nativeElement.focus();
    }
  }

  isSelected(option: SelectOption): boolean {
    return this.selectedValue() === option.value;
  }

  private syncInputToLabel(): void {
    if (this.inputEl) {
      this.inputEl.nativeElement.value = this.selectedLabel() ?? '';
    }
  }

  writeValue(value: any): void {
    this.selectedValue.set(value ?? null);
    if (this.inputEl) this.syncInputToLabel();
  }
  registerOnChange(fn: (v: any) => void): void { this.onChange = fn; }
  registerOnTouched(fn: () => void): void { this.onTouched = fn; }
  setDisabledState(isDisabled: boolean): void { this.isDisabled.set(isDisabled); }
}
