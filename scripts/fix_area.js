const fs = require('fs');
const files = [
  'src/app/cursos/presentation/curso-create/curso-create.html',
];

for (const f of files) {
  let c = fs.readFileSync(f, 'utf8');
  c = c.split('Ãrea').join('Área');
  fs.writeFileSync(f, c, 'utf8');
  console.log('Done: ' + f.split('/').pop());
}
