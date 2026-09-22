const fs = require('fs');
const file = 'src/app/cursos/presentation/curso-create/curso-create.html';
let content = fs.readFileSync(file, 'utf8');

const target = `formControlName="inicio_inscripciones">\n                </div>\n              </div>`;
const replacement = `formControlName="inicio_inscripciones">\n                </div>\n              </div>\n              @if (form.errors?.['inscripcionTardia']) {\n                <p class="mt-2 text-danger text-sm font-medium">Error: El inicio de inscripciones no puede ser posterior al inicio de actividades.</p>\n              }\n              @if (form.errors?.['finTemprano']) {\n                <p class="mt-2 text-danger text-sm font-medium">Error: La fecha de finalización no puede ser anterior al inicio de actividades.</p>\n              }`;

if (content.includes(target)) {
  fs.writeFileSync(file, content.split(target).join(replacement), 'utf8');
  console.log('Fixed LF');
} else {
  const targetCRLF = target.replace(/\n/g, '\r\n');
  const replacementCRLF = replacement.replace(/\n/g, '\r\n');
  if (content.includes(targetCRLF)) {
    fs.writeFileSync(file, content.split(targetCRLF).join(replacementCRLF), 'utf8');
    console.log('Fixed CRLF');
  } else {
    console.log('Not found');
  }
}
