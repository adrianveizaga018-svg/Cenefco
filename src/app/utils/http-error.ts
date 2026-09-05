import { HttpErrorResponse } from '@angular/common/http';

function serverMessage(err: HttpErrorResponse): string | undefined {
  return err.error?.message ?? err.error?.error;
}

export function extractErrorMessage(err: HttpErrorResponse, fallback = 'Ocurrió un error inesperado. Intente nuevamente.'): string {
  if (err.status === 422) {
    if (err.error?.errors) {
      const firstKey = Object.keys(err.error.errors)[0];
      const serverMsg: string = err.error.errors[firstKey]?.[0] ?? '';
      return translateValidationMessage(firstKey, serverMsg);
    }
    return serverMessage(err) ?? 'Los datos ingresados no son válidos.';
  }

  if (err.status === 409) return serverMessage(err) ?? 'Ya existe un registro con esos datos.';
  if (err.status === 403) return 'No tienes permiso para realizar esta acción.';
  if (err.status === 404) return 'El registro no fue encontrado.';
  if (err.status === 0)   return 'No se pudo conectar con el servidor. Verifica tu conexión.';
  if (err.status >= 500)  return 'Error interno del servidor. Contacta al administrador.';

  return serverMessage(err) ?? fallback;
}

function translateValidationMessage(field: string, serverMsg: string): string {
  if (serverMsg.includes('already been taken') || serverMsg.includes('already exists')) {
    const fieldNames: Record<string, string> = {
      plataforma:    'plataforma',
      email:         'correo electrónico',
      nombre:        'nombre',
      nombre_cuenta: 'nombre de cuenta',
      url:           'URL',
      slug:          'identificador',
      nit:           'NIT',
      ci:            'carnet de identidad',
    };
    const label = fieldNames[field] ?? field;
    return `Ya existe un registro con ese ${label}. No se permiten duplicados.`;
  }

  if (serverMsg.includes('required')) {
    return `El campo "${field}" es obligatorio.`;
  }

  if (serverMsg.includes('must not be greater than') || serverMsg.includes('max')) {
    return `El campo "${field}" excede el límite de caracteres permitido.`;
  }

  if (serverMsg.includes('must be at least')) {
    return `El campo "${field}" no puede ser negativo.`;
  }

  if (serverMsg.includes('must be a valid') || serverMsg.includes('invalid')) {
    return `El formato del campo "${field}" no es válido.`;
  }

  return serverMsg || `Error en el campo "${field}".`;
}
