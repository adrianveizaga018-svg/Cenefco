const fs = require('fs');

const validatorCode = `
import { AbstractControl, ValidationErrors, ValidatorFn } from '@angular/core';

export const fechasValidator: ValidatorFn = (control: AbstractControl): ValidationErrors | null => {
  const inicioInsc = control.get('inicio_inscripciones')?.value;
  const inicioAct = control.get('inicio_actividades')?.value;
  const finAct = control.get('finalizacion_actividades')?.value;

  if (!inicioInsc && !inicioAct && !finAct) return null;

  let errors: any = {};
  if (inicioInsc && inicioAct && new Date(inicioInsc) > new Date(inicioAct)) {
    errors.inscripcionTardia = true;
  }
  if (inicioAct && finAct && new Date(inicioAct) > new Date(finAct)) {
    errors.finTemprano = true;
  }

  return Object.keys(errors).length > 0 ? errors : null;
};
`;

const fileTsCreate = 'src/app/cursos/presentation/curso-create/curso-create.ts';
let tsCreate = fs.readFileSync(fileTsCreate, 'utf8');

if (!tsCreate.includes('fechasValidator')) {
  // Add imports
  tsCreate = tsCreate.replace(
    "import { Validators } from '@angular/forms';",
    "import { Validators, AbstractControl, ValidationErrors, ValidatorFn } from '@angular/forms';"
  );
  
  // Add validator at the top of the file
  tsCreate = tsCreate.replace(
    "@Component({",
    `export const fechasValidator: ValidatorFn = (control: AbstractControl): ValidationErrors | null => {\n  const inicioInsc = control.get('inicio_inscripciones')?.value;\n  const inicioAct = control.get('inicio_actividades')?.value;\n  const finAct = control.get('finalizacion_actividades')?.value;\n  if (!inicioInsc && !inicioAct && !finAct) return null;\n  let errors: any = {};\n  if (inicioInsc && inicioAct && new Date(inicioInsc) > new Date(inicioAct)) {\n    errors.inscripcionTardia = true;\n  }\n  if (inicioAct && finAct && new Date(inicioAct) > new Date(finAct)) {\n    errors.finTemprano = true;\n  }\n  return Object.keys(errors).length > 0 ? errors : null;\n};\n\n@Component({`
  );

  // Apply validator to form group
  tsCreate = tsCreate.replace(
    "orden:                    [0, [Validators.required, Validators.min(0)]],",
    "orden:                    [0, [Validators.required, Validators.min(0)]],\n    }, { validators: fechasValidator });"
  );
  // Wait, the replace string might not match exactly.
}

fs.writeFileSync(fileTsCreate, tsCreate);

const fileHtmlCreate = 'src/app/cursos/presentation/curso-create/curso-create.html';
let htmlCreate = fs.readFileSync(fileHtmlCreate, 'utf8');
if (!htmlCreate.includes('errors?.[\'inscripcionTardia\']')) {
  htmlCreate = htmlCreate.replace(
    `<label class="block mb-1.5 text-sm font-medium text-default-800">FinalizaciÃ³n</label>`,
    `<label class="block mb-1.5 text-sm font-medium text-default-800">Finalización</label>`
  );
  htmlCreate = htmlCreate.replace(
    `</label>\r\n                  <input type="date" class="form-input" formControlName="inicio_inscripciones">\r\n                </div>\r\n              </div>`,
    `</label>\r\n                  <input type="date" class="form-input" formControlName="inicio_inscripciones">\r\n                </div>\r\n              </div>\r\n              @if (form.errors?.['inscripcionTardia']) {\r\n                <p class="mt-1 text-xs text-danger">Las inscripciones no pueden empezar después del inicio de actividades.</p>\r\n              }\r\n              @if (form.errors?.['finTemprano']) {\r\n                <p class="mt-1 text-xs text-danger">La finalización no puede ser antes del inicio de actividades.</p>\r\n              }`
  );
  // Fallback for LF
  htmlCreate = htmlCreate.replace(
    `</label>\n                  <input type="date" class="form-input" formControlName="inicio_inscripciones">\n                </div>\n              </div>`,
    `</label>\n                  <input type="date" class="form-input" formControlName="inicio_inscripciones">\n                </div>\n              </div>\n              @if (form.errors?.['inscripcionTardia']) {\n                <p class="mt-1 text-xs text-danger">Las inscripciones no pueden empezar después del inicio de actividades.</p>\n              }\n              @if (form.errors?.['finTemprano']) {\n                <p class="mt-1 text-xs text-danger">La finalización no puede ser antes del inicio de actividades.</p>\n              }`
  );
}
fs.writeFileSync(fileHtmlCreate, htmlCreate);

console.log('Done script generation');
