const fs = require('fs');
const f = 'src/app/programas-academicos/presentation/version-imparticion-form/version-imparticion-form.html';
let c = fs.readFileSync(f, 'utf8');
c = c.replace(/â€”/g, '—');
fs.writeFileSync(f, c);
