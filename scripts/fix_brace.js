const fs = require('fs');
const file = 'src/app/cursos/presentation/curso-edit/curso-edit.html';
let content = fs.readFileSync(file, 'utf8');

const regex = /(@if\s*\(\s*form\.errors\?\.\['finTemprano'\]\s*\)\s*\{[\s\S]*?<\/p>\s*)(\s*<\/div>)/;

if (regex.test(content)) {
  content = content.replace(regex, "$1}\n$2");
  fs.writeFileSync(file, content, 'utf8');
  console.log('Fixed syntax error in curso-edit.html');
} else {
  console.log('Not found');
}
