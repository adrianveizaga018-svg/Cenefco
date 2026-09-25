const fs = require('fs');

const file = 'src/app/cursos/presentation/curso-create/curso-create.ts';
let ts = fs.readFileSync(file, 'utf8');

// Fix the filter: use loose comparison (== instead of ===) to handle both number and string
ts = ts.replace(
  'this.todosLosPlanes.set(lista.filter((p: any) => p.estado === 1));',
  'this.todosLosPlanes.set(lista.filter((p: any) => p.estado == 1 || p.estado === "activo" || p.estado === true));'
);

fs.writeFileSync(file, ts);
console.log('Fixed filter in curso-create.ts');
