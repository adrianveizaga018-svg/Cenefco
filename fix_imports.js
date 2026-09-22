const fs = require('fs');

// Fix curso-create.ts
const fileCreate = 'src/app/cursos/presentation/curso-create/curso-create.ts';
let tsCreate = fs.readFileSync(fileCreate, 'utf8');

tsCreate = tsCreate.replace(
  "import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';",
  "import { ReactiveFormsModule, FormBuilder, FormGroup, Validators, ValidatorFn, AbstractControl, ValidationErrors } from '@angular/forms';"
);

fs.writeFileSync(fileCreate, tsCreate);
console.log('Fixed forms import in curso-create.ts');

// Fix curso-edit.ts  
const fileEdit = 'src/app/cursos/presentation/curso-edit/curso-edit.ts';
let tsEdit = fs.readFileSync(fileEdit, 'utf8');

// Check what it looks like now
const idx = tsEdit.indexOf("from '@angular/forms'");
console.log('Edit forms import:', tsEdit.substring(idx - 100, idx + 50));
