import { Routes } from '@angular/router';

export const ESTUDIANTE_ROUTES: Routes = [
  {
    path: 'dashboard',
    loadComponent: () =>
      import('./presentation/estudiante-dashboard/estudiante-dashboard').then(
        (m) => m.EstudianteDashboardComponent
      ),
    data: { title: 'Mi Panel' },
  },
  {
    path: 'mis-cursos',
    loadComponent: () =>
      import('./presentation/mis-cursos/mis-cursos').then(
        (m) => m.MisCursosComponent
      ),
    data: { title: 'Mis Cursos' },
  },
  {
    path: 'mis-certificados',
    loadComponent: () =>
      import('./presentation/mis-certificados/mis-certificados').then(
        (m) => m.MisCertificadosComponent
      ),
    data: { title: 'Mis Certificados' },
  },
  {
    path: 'mis-pagos',
    loadComponent: () =>
      import('./presentation/mis-pagos/mis-pagos').then(
        (m) => m.MisPagosEstudianteComponent
      ),
    data: { title: 'Mis Pagos' },
  },
  {
    path: 'catalogo',
    loadComponent: () =>
      import('./presentation/oferta-academica/oferta-academica').then(
        (m) => m.OfertaAcademicaComponent
      ),
    data: { title: 'Catálogo de Cursos' },
  },
];
