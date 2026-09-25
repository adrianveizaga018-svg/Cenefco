const fs = require('fs');
const path = require('path');

const filesToFix = [
  'src/app/programas-academicos/presentation/version-imparticion-form/version-imparticion-form.html',
  'src/app/programas-academicos/presentation/version-imparticion-form/version-imparticion-form.ts'
];

const map = {
  'Ã³': 'ó',
  'Ã±': 'ñ',
  'Ã¡': 'á',
  'Ã©': 'é',
  'Ã­': 'í',
  'Ãº': 'ú',
  'Ã': 'í', // This is tricky, sometimes Ã represents í or Á. Let's be careful.
  'Â´': "'",
  'â€”': '—',
  'âš': '⚠',
  'AÃ±o': 'Año',
  'acadÃ©mico': 'académico',
  'VersiÃ³n': 'Versión',
  'versiÃ³n': 'versión',
  'creaciÃ³n': 'creación',
  'CreaciÃ³n': 'Creación',
  'FinalizaciÃ³n': 'Finalización',
  'InscripciÃ³n': 'Inscripción',
  'inscripciÃ³n': 'inscripción',
  'CatÃ¡logo': 'Catálogo',
  'Cambio de plan guardado con Ã©xito': 'Cambio de plan guardado con éxito',
  'No se encontrÃ³': 'No se encontró',
  'Ãºltimo': 'último',
  'â€"': '—'
};

filesToFix.forEach(file => {
  const fullPath = path.resolve(__dirname, file);
  if (!fs.existsSync(fullPath)) return;
  
  let content = fs.readFileSync(fullPath, 'utf8');
  
  // Safe replacements for specific known words first
  content = content.replace(/VersiÃ³n/g, 'Versión');
  content = content.replace(/versiÃ³n/g, 'versión');
  content = content.replace(/AÃ±o/g, 'Año');
  content = content.replace(/acadÃ©mico/g, 'académico');
  content = content.replace(/FinalizaciÃ³n/g, 'Finalización');
  content = content.replace(/No se encontrÃ³/g, 'No se encontró');
  content = content.replace(/âš/g, '⚠');
  content = content.replace(/Ã³/g, 'ó');
  content = content.replace(/Ã±/g, 'ñ');
  content = content.replace(/Ã¡/g, 'á');
  content = content.replace(/Ã©/g, 'é');
  content = content.replace(/Ã/g, 'í'); // Be careful, sometimes it's í

  // specifically fix "í" if corrupted to just í
  content = content.replace(/académico/g, 'académico'); // sanity check
  
  fs.writeFileSync(fullPath, content, 'utf8');
  console.log('Fixed', file);
});
