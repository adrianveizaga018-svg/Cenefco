const fs = require('fs');
const fileTS = 'src/app/cursos/presentation/cursos/cursos.ts';
let ts = fs.readFileSync(fileTS, 'utf8');

const oldTS = `  deleteCurso(id: number): void {
    Swal.fire({
      title: '¿Eliminar curso?',
      text: 'Esta acción no se puede deshacer',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(result => {
      if (result.isConfirmed) {
        this.cursoService.delete(id).subscribe({
          next: () => {
            this.toast.success('¡Eliminado!', 'El curso ha sido eliminado');
            this.refreshTrigger.update(n => n + 1);
          },
          error: (err: HttpErrorResponse) => this.toast.error('Error', extractErrorMessage(err, 'No se pudo eliminar el curso'))
        });
      }
    });
  }`;

const newTS = `  desactivarCurso(curso: any): void {
    const estaActivo = curso.estado_web === 'publicado';
    const nuevoEstado = estaActivo ? 'inactivo' : 'publicado';
    const titulo = estaActivo ? '¿Desactivar curso?' : '¿Activar curso?';
    const texto = estaActivo 
      ? 'El curso ya no será visible para los estudiantes en la web.'
      : 'El curso volverá a ser visible para todos.';

    Swal.fire({
      title: titulo,
      text: texto,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: estaActivo ? '#d33' : '#10b981',
      cancelButtonColor: '#6b7280',
      confirmButtonText: estaActivo ? 'Sí, desactivar' : 'Sí, publicar',
      cancelButtonText: 'Cancelar'
    }).then(result => {
      if (result.isConfirmed) {
        this.cursoService.update(curso.id_programa, { estado_web: nuevoEstado }).subscribe({
          next: () => {
            this.toast.success('¡Actualizado!', 'El estado del curso ha cambiado');
            this.refreshTrigger.update(n => n + 1);
          },
          error: (err: HttpErrorResponse) => this.toast.error('Error', extractErrorMessage(err, 'No se pudo cambiar el estado'))
        });
      }
    });
  }`;

function escapeRegExp(string) { return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }

const oldRegex = new RegExp(escapeRegExp(oldTS).replace(/\\r\\n|\\n/g, '\\r?\\n'), 'g');
ts = ts.replace(oldRegex, newTS);
fs.writeFileSync(fileTS, ts);

const fileHTML = 'src/app/cursos/presentation/cursos/cursos.html';
let html = fs.readFileSync(fileHTML, 'utf8');

const oldHTML = `                              @if (puedeEliminar) {
                                <button
                                  type="button"
                                  class="btn size-7.5 bg-danger/10 hover:bg-danger text-danger hover:text-white"
                                  title="Eliminar"
                                  (click)="deleteCurso(curso.id_programa)">
                                  <div class="size-4"><ng-icon name="lucideTrash2"></ng-icon></div>
                                </button>
                              }`;

const newHTML = `                              @if (puedeEliminar) {
                                <button
                                  type="button"
                                  class="btn size-7.5 bg-warning/10 hover:bg-warning text-warning hover:text-white"
                                  [title]="curso.estado_web === 'publicado' ? 'Desactivar curso' : 'Publicar curso'"
                                  (click)="desactivarCurso(curso)">
                                  <div class="size-4"><ng-icon [name]="curso.estado_web === 'publicado' ? 'lucideEyeOff' : 'lucideEye'"></ng-icon></div>
                                </button>
                              }`;

const oldHtmlRegex = new RegExp(escapeRegExp(oldHTML).replace(/\\r\\n|\\n/g, '\\r?\\n'), 'g');
html = html.replace(oldHtmlRegex, newHTML);
fs.writeFileSync(fileHTML, html);
console.log('Done replacement Node script');
