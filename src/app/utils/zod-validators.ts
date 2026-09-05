import { AbstractControl, ValidationErrors, ValidatorFn } from '@angular/forms';
import { z } from 'zod';

export function zodValidator(schema: z.ZodSchema): ValidatorFn {
  return (control: AbstractControl): ValidationErrors | null => {
    if (!control.value) {
      return null;
    }

    const result = schema.safeParse(control.value);
    if (result.success) {
      return null;
    }

    const firstError = result.error.issues[0];
    return { zod: firstError?.message || 'Validación fallida' };
  };
}

export const ZodValidators = {
  phone: zodValidator(
    z.string()
      .regex(/^[0-9+\-\s()]*$/, 'Solo números, +, -, espacios y paréntesis')
      .min(7, 'Mínimo 7 caracteres')
      .max(20, 'Máximo 20 caracteres')
  ),

  mobile: zodValidator(
    z.string()
      .regex(/^[0-9+\-\s()]*$/, 'Solo números, +, -, espacios y paréntesis')
      .min(8, 'Mínimo 8 caracteres')
      .max(20, 'Máximo 20 caracteres')
  ),

  email: zodValidator(
    z.string().email('Correo electrónico inválido')
  )
};
