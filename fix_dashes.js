const fs = require('fs');
const path = require('path');
function walk(dir) {
  let results = [];
  const list = fs.readdirSync(dir);
  list.forEach(file => {
    file = dir + '/' + file;
    const stat = fs.statSync(file);
    if (stat && stat.isDirectory()) { 
      results = results.concat(walk(file));
    } else { 
      if (file.endsWith('.html') || file.endsWith('.ts')) results.push(file);
    }
  });
  return results;
}

const files = walk('src/app/cursos');
for (const f of files) {
  let c = fs.readFileSync(f, 'utf8');
  let orig = c;
  c = c.split('â€”').join('—');
  c = c.split('â€“').join('–');
  c = c.split('â€œ').join('“');
  c = c.split('â€').join('”');
  if (c !== orig) {
    fs.writeFileSync(f, c, 'utf8');
    console.log('Fixed:', f);
  }
}
