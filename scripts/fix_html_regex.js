const fs = require('fs');
const file = 'src/app/cursos/presentation/curso-edit/curso-edit.html';
let content = fs.readFileSync(file, 'utf8');

const regex = /(formControlName="inicio_inscripciones">[\s\n\r]*<\/div>[\s\n\r]*<\/div>)/;
const replacement = `$1\n            @if (form.errors?.['inscripcionTardia']) {\n              <p class="mt-2 text-danger text-sm font-medium">Error: El inicio de inscripciones no puede ser posterior al inicio de actividades.</p>\n            }\n            @if (form.errors?.['finTemprano']) {\n              <p class="mt-2 text-danger text-sm font-medium">Error: La fecha de finalización no puede ser anterior al inicio de actividades.</p>\n            }`;

if (regex.test(content)) {
  content = content.replace(regex, replacement);
  fs.writeFileSync(file, content, 'utf8');
  console.log('Fixed html');
} else {
  console.log('Not found');
}

