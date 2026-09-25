const fs = require('fs');
const file = 'src/app/cursos/presentation/curso-edit/curso-edit.html';
let content = fs.readFileSync(file, 'utf8');

// Remove the old icon-based duplicate blocks (old version with ng-icon)
const oldBlock = `\n              @if (form.errors?.['inscripcionTardia']) {\n                <p class="mt-2 text-sm text-danger font-medium"><ng-icon name="lucideAlertCircle" class="size-4 inline align-text-bottom mr-1"></ng-icon> Las inscripciones no pueden empezar después del inicio de actividades.</p>\n              }\n              @if (form.errors?.['finTemprano']) {\n                <p class="mt-2 text-sm text-danger font-medium"><ng-icon name="lucideAlertCircle" class="size-4 inline align-text-bottom mr-1"></ng-icon> La finalización del curso no puede ser antes de su inicio.</p>\n              }`;

// Find all occurrences
const count = content.split("@if (form.errors?.['inscripcionTardia'])").length - 1;
console.log('Occurrences of inscripcionTardia:', count);

if (count > 1) {
  // Keep only the first one - remove duplicates
  // Find the second occurrence and remove it
  const firstIdx = content.indexOf("@if (form.errors?.['inscripcionTardia'])");
  const secondIdx = content.indexOf("@if (form.errors?.['inscripcionTardia'])", firstIdx + 10);
  if (secondIdx > -1) {
    // Find the end of the second block (after finTemprano block closes)
    const endSearch = content.indexOf("}", content.indexOf("finTemprano", secondIdx) + 10);
    const toRemove = content.substring(secondIdx - 16, endSearch + 1); // -16 for the @if space before
    console.log('Removing:', toRemove.substring(0, 80) + '...');
    content = content.replace(toRemove, '');
    fs.writeFileSync(file, content, 'utf8');
    console.log('Fixed duplicate');
  }
} else {
  console.log('No duplicate found');
}
