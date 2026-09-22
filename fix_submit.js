const fs = require('fs');
const file = 'src/app/cursos/presentation/curso-create/curso-create.ts';
let ts = fs.readFileSync(file, 'utf8');

// Use line-number based replacement  
// Lines 185-203 are the old onSubmit
const lines = ts.split('\n');

const newSubmitLines = [
  '  onSubmit(): void {',
  "    this.form.markAllAsTouched();",
  "    if (this.form.errors?.['inscripcionTardia']) {",
  "      this.toast.error('Fechas inválidas', 'Las inscripciones no pueden comenzar después del inicio de actividades.');",
  "      return;",
  "    }",
  "    if (this.form.errors?.['finTemprano']) {",
  "      this.toast.error('Fechas inválidas', 'La fecha de finalización no puede ser anterior al inicio de actividades.');",
  "      return;",
  "    }",
  "    if (this.form.invalid) return;",
  "    if (this.uploadingImg() || this.uploadingPdf()) return;",
  "    for (const [campo, editor] of Object.entries(this.ckEditors)) {",
  "      this.form.get(campo)?.setValue(editor.getData());",
  "    }",
  "",
  "    this.submitting.set(true);",
  "    this.cursoService.create(this.form.value).subscribe({",
  "      next: (curso: any) => {",
  "        const idPrograma = curso.id_programa;",
  "        const planes = this.planesSeleccionados();",
  "        if (planes.length > 0 && idPrograma) {",
  "          this.http.post('/api/v1/programas-academicos/' + idPrograma + '/planes', { planes }).subscribe({",
  "            next: () => {}, error: () => {}",
  "          });",
  "        }",
  "        this.toast.success('¡Creado!', 'El programa fue registrado exitosamente.');",
  "        this.router.navigate(['/cenefco/cursos']);",
  "      },",
  "      error: (err: HttpErrorResponse) => {",
  "        this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar el curso'));",
  "        this.submitting.set(false);",
  "      }",
  "    });",
  "  }",
];

// Find onSubmit line (index 184 = line 185 in 1-based)
const onSubmitIdx = lines.findIndex(l => l.includes('onSubmit(): void'));
const endIdx = lines.findIndex((l, i) => i > onSubmitIdx + 1 && l.trim() === '}' && !l.trim().startsWith('//'));

console.log(`Found onSubmit at line ${onSubmitIdx + 1}, closing brace at line ${endIdx + 1}`);
console.log('Lines to replace:', endIdx - onSubmitIdx + 1);

// Replace lines onSubmitIdx to endIdx inclusive
lines.splice(onSubmitIdx, endIdx - onSubmitIdx + 1, ...newSubmitLines);

fs.writeFileSync(file, lines.join('\n'));
console.log('Done');
