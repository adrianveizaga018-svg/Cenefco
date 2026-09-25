const fs = require('fs');

const fixFormGroup = (file) => {
  let ts = fs.readFileSync(file, 'utf8');
  
  if (ts.includes('fechasValidator') && !ts.includes('{ validators: fechasValidator }')) {
    ts = ts.replace(
      'tareas_catalogo_ids:      [[] as number[]],\r\n    });',
      'tareas_catalogo_ids:      [[] as number[]],\r\n    }, { validators: fechasValidator });'
    );
    ts = ts.replace(
      'tareas_catalogo_ids:      [[] as number[]],\n    });',
      'tareas_catalogo_ids:      [[] as number[]],\n    }, { validators: fechasValidator });'
    );
    fs.writeFileSync(file, ts);
    console.log(`Updated ${file}`);
  }
};

fixFormGroup('src/app/cursos/presentation/curso-create/curso-create.ts');

const fileHtml = 'src/app/cursos/presentation/curso-create/curso-create.html';
let html = fs.readFileSync(fileHtml, 'utf8');
if (!html.includes('inscripcionTardia')) {
    console.log('HTML not updated yet, fixing...');
}
