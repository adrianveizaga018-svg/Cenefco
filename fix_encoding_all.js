const fs = require('fs');
const path = require('path');

// Mapa de caracteres mal codificados (Latin-1 leído como UTF-8 corrupto) -> correcto
const fixes = [
  ['Ã³', 'ó'], ['Ã©', 'é'], ['Ã¡', 'á'], ['Ã­', 'í'], ['Ãº', 'ú'],
  ['Ã±', 'ñ'], ['Ã"', 'Ó'], ['Ã‰', 'É'], ['Ã', 'Á'], ['Ã­', 'Í'],
  ['Ãš', 'Ú'], ['Ã'', 'Ñ'], ['Ã¼', 'ü'], ['Ã¤', 'ä'], ['â€™', "'"],
  ['â€œ', '"'], ['â€', '"'], ['Â¿', '¿'], ['Â¡', '¡'], ['Ã€', 'À'],
  ['Ã¨', 'è'], ['Ã²', 'ò'], ['Ã¹', 'ù'], ['Ã¢', 'â'], ['Ã®', 'î'],
  ['Ã´', 'ô'], ['Ã»', 'û'], ['Ã«', 'ë'], ['Ã¯', 'ï'], ['Ãµ', 'õ'],
  ['Ã£', 'ã'], ['Ã ', 'à'], ['Ã§', 'ç'], ['Ã‡', 'Ç'],
];

const targetFiles = [
  'src/app/cursos/presentation/curso-create/curso-create.html',
  'src/app/cursos/presentation/curso-create/curso-create.ts',
  'src/app/cursos/presentation/curso-edit/curso-edit.html',
  'src/app/cursos/presentation/curso-edit/curso-edit.ts',
  'src/app/cursos/presentation/cursos/cursos.html',
  'src/app/cursos/presentation/cursos/cursos.ts',
];

let totalFixed = 0;

for (const filePath of targetFiles) {
  if (!fs.existsSync(filePath)) continue;
  
  let content = fs.readFileSync(filePath, 'utf8');
  let original = content;
  
  for (const [bad, good] of fixes) {
    while (content.includes(bad)) {
      content = content.split(bad).join(good);
    }
  }
  
  if (content !== original) {
    fs.writeFileSync(filePath, content, 'utf8');
    const count = (original.match(new RegExp(fixes.map(([b]) => b.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')).join('|'), 'g')) || []).length;
    console.log(`✓ Fixed ${filePath.split('/').pop()}`);
    totalFixed++;
  } else {
    console.log(`  No issues found in ${filePath.split('/').pop()}`);
  }
}

console.log(`\nDone. Fixed ${totalFixed} file(s).`);
