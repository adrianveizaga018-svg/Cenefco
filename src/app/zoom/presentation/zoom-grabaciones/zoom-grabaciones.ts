import { Component, inject, signal, OnInit } from '@angular/core';
import { NgIcon } from '@ng-icons/core';
import { DatePipe } from '@angular/common';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { ZoomService } from '../../application/services/zoom.service';
import { ZoomCuenta, ZoomGrabacion } from '../../domain/models/zoom.model';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import * as XLSX from 'xlsx';

@Component({ selector: 'app-zoom-grabaciones', imports: [NgIcon, PageTitle, RouterLink, DatePipe, FormsModule], templateUrl: './zoom-grabaciones.html' })
export class ZoomGrabaciones implements OnInit {
  private service = inject(ZoomService);
  private toast   = inject(ToastService);

  cuentas        = signal<ZoomCuenta[]>([]);
  cuentaSelId    = signal<number | null>(null);
  grabaciones    = signal<ZoomGrabacion[]>([]);
  loading        = signal(false);
  loadingCuentas = signal(true);
  error          = signal('');

  ngOnInit(): void {
    this.service.getCuentas().subscribe({
      next: (res) => {
        this.cuentas.set(res.data);
        this.loadingCuentas.set(false);
        const pred = res.data.find(c => c.predeterminada) ?? res.data[0];
        if (pred) { this.cuentaSelId.set(pred.id); this.cargar(pred.id); }
      },
      error: () => this.loadingCuentas.set(false)
    });
  }

  onCuentaChange(idStr: string): void {
    const id = Number(idStr);
    this.cuentaSelId.set(id);
    this.cargar(id);
  }

  cargar(cuentaId: number): void {
    this.loading.set(true);
    this.error.set('');
    this.service.getGrabaciones(cuentaId).subscribe({
      next: (res) => { this.grabaciones.set(res.recordings ?? []); this.loading.set(false); },
      error: (err) => { this.error.set(err?.error?.message ?? 'No se pudo conectar con Zoom'); this.loading.set(false); }
    });
  }

  get cuentaActual(): ZoomCuenta | undefined {
    return this.cuentas().find(c => c.id === this.cuentaSelId());
  }

  copiarLink(url: string | null): void {
    if (!url) return;
    navigator.clipboard.writeText(url).then(() => this.toast.success('Copiado', 'Enlace copiado al portapapeles'));
  }

  private sanitizar(s: string): string {
    return s.replace(/[<>:"/\\|?*\x00-\x1F]/g, '_').replace(/\s+/g, '_').substring(0, 80);
  }

  descargarScriptUniversal(): void {
    const script = `#!/usr/bin/env python3
# ═══════════════════════════════════════════════════════════════════════════════
#  DESCARGADOR MASIVO DE GRABACIONES ZOOM — desde CSV exportado de Zoom
# ═══════════════════════════════════════════════════════════════════════════════
#
#  USO:
#    1. Exporta el CSV desde zoom.us → Recordings & Transcripts → Export
#    2. Pon las credenciales de la cuenta Zoom abajo (ACCOUNT_ID, etc.)
#    3. Ejecuta: python3 zoom_bulk_downloader.py grabaciones.csv
#
#  Requisitos:
#    pip install requests
#
#  Resultado:
#    Carpeta  Grabaciones_Zoom/<Nombre_Curso>/<fecha>_mp4.mp4
#    Archivo  descarga_log_FECHA.txt
# ═══════════════════════════════════════════════════════════════════════════════

import os, sys, csv, time, re
from datetime import datetime

try:
    import requests
except ImportError:
    print("Instala requests: pip install requests")
    sys.exit(1)

# ── CREDENCIALES (rellena con tu cuenta Zoom) ─────────────────────────────────
ACCOUNT_ID    = "TU_ACCOUNT_ID"
CLIENT_ID     = "TU_CLIENT_ID"
CLIENT_SECRET = "TU_CLIENT_SECRET"
# ─────────────────────────────────────────────────────────────────────────────

CARPETA_BASE = "Grabaciones_Zoom"
INICIO       = datetime.now()
LOG_FILE     = f"descarga_log_{INICIO.strftime('%Y%m%d_%H%M%S')}.txt"
_log         = []

def log(msg, consola=True):
    ts = datetime.now().strftime("%H:%M:%S")
    linea = f"[{ts}] {msg}"
    _log.append(linea)
    if consola: print(msg)

def sanitizar(s):
    s = re.sub(r'[<>:"/\\\\|?*\\x00-\\x1F]', '_', s)
    s = re.sub(r'\\s+', '_', s.strip())
    return s[:80]

# ── Auth Zoom ─────────────────────────────────────────────────────────────────
def get_token():
    import base64
    creds = base64.b64encode(f"{CLIENT_ID}:{CLIENT_SECRET}".encode()).decode()
    r = requests.post(
        f"https://zoom.us/oauth/token?grant_type=account_credentials&account_id={ACCOUNT_ID}",
        headers={"Authorization": f"Basic {creds}"}
    )
    if not r.ok:
        print(f"\\n❌ Error de autenticación Zoom: {r.text}")
        print("   Revisa ACCOUNT_ID, CLIENT_ID y CLIENT_SECRET en este script.")
        sys.exit(1)
    return r.json()["access_token"]

# ── Obtener grabaciones de un meeting ─────────────────────────────────────────
def get_recordings(token, meeting_id):
    mid = meeting_id.replace(" ", "")
    r = requests.get(
        f"https://api.zoom.us/v2/meetings/{mid}/recordings",
        headers={"Authorization": f"Bearer {token}"}
    )
    if r.status_code == 404:
        return []
    r.raise_for_status()
    data = r.json()
    return [
        f for f in data.get("recording_files", [])
        if f.get("file_type") == "MP4" and f.get("download_url")
    ]

# ── Descargar archivo ─────────────────────────────────────────────────────────
def descargar(token, url, ruta):
    r = requests.get(url + f"?access_token={token}", stream=True, timeout=180)
    if not r.ok:
        return False, f"HTTP {r.status_code}"
    total = int(r.headers.get("content-length", 0))
    done  = 0
    with open(ruta, "wb") as f:
        for chunk in r.iter_content(65536):
            f.write(chunk)
            done += len(chunk)
            pct = int(done * 100 / total) if total else 0
            print(f"  ↓ {pct:3d}%", end="\\r")
    mb = os.path.getsize(ruta) / 1_048_576
    log(f"  ✓ {mb:.1f} MB  →  {ruta}")
    return True, None

# ── Leer CSV de Zoom ──────────────────────────────────────────────────────────
def leer_csv(path):
    registros = []
    with open(path, newline="", encoding="utf-8-sig") as f:
        reader = csv.DictReader(f)
        for row in reader:
            tema = (row.get("Tema de la reunión") or row.get("Meeting Topic", "")).strip()
            mid  = (row.get("ID de la reunión") or row.get("Meeting ID", "")).strip()
            fecha_str = (row.get("Hora de inicio de la reunión") or row.get("Start Time", "")).strip()
            if tema and mid:
                # Extraer solo la fecha YYYY-MM-DD
                m = re.search(r"(\\d{1,2}) (\\w+) (\\d{4})", fecha_str)
                if m:
                    meses = {"ene":"01","feb":"02","mar":"03","abr":"04","may":"05","jun":"06",
                             "jul":"07","ago":"08","sep":"09","oct":"10","nov":"11","dic":"12"}
                    d, mes, y = m.groups()
                    mm = meses.get(mes[:3].lower(), "00")
                    fecha = f"{y}-{mm}-{int(d):02d}"
                else:
                    fecha = "sin-fecha"
                registros.append({"tema": tema, "id": mid, "fecha": fecha})
    return registros

# ── MAIN ──────────────────────────────────────────────────────────────────────
if len(sys.argv) < 2:
    print("Uso: python3 zoom_bulk_downloader.py archivo_zoom.csv")
    sys.exit(1)

csv_path = sys.argv[1]
if not os.path.exists(csv_path):
    print(f"Archivo no encontrado: {csv_path}")
    sys.exit(1)

registros = leer_csv(csv_path)
log(f"CSV leído: {len(registros)} reuniones encontradas")
print(f"\\n🔑 Autenticando con Zoom...")
token = get_token()
log("Autenticación OK")

ok = omitidos = errores = 0
errores_detalle = []

print(f"\\n📥 Procesando {len(registros)} reuniones...\\n")

for i, rec in enumerate(registros, 1):
    carpeta_nombre = sanitizar(rec["tema"])
    print(f"[{i}/{len(registros)}] {rec['tema'][:70]}")
    log(f"[{i}/{len(registros)}] Meeting ID: {rec['id']} | {rec['tema'][:60]}", consola=False)

    try:
        archivos = get_recordings(token, rec["id"])
    except Exception as e:
        msg = f"Error al consultar API: {e}"
        log(f"  ✗ {msg}")
        errores += 1
        errores_detalle.append(f"[{i}] {rec['tema'][:50]} — {msg}")
        continue

    if not archivos:
        log(f"  ⚠ Sin grabaciones MP4 disponibles")
        omitidos += 1
        continue

    carpeta = os.path.join(CARPETA_BASE, carpeta_nombre)
    os.makedirs(carpeta, exist_ok=True)

    for j, archivo in enumerate(archivos, 1):
        sufijo = f"_parte{j}" if len(archivos) > 1 else ""
        nombre = f"{rec['fecha']}{sufijo}_mp4.mp4"
        ruta   = os.path.join(carpeta, nombre)
        log(f"  → {nombre}  ({archivo.get('file_size', 0) / 1_048_576:.1f} MB)", consola=False)

        if os.path.exists(ruta):
            log(f"  ⏩ Ya existe, omitiendo")
            omitidos += 1
            continue

        print(f"  ↓ Descargando: {nombre}")
        exito, err = descargar(token, archivo["download_url"], ruta)
        if exito:
            ok += 1
        else:
            errores += 1
            errores_detalle.append(f"[{i}] {rec['tema'][:50]} / {nombre} — {err}")
            log(f"  ✗ Error: {err}")

    time.sleep(0.5)
    print()

# ── Guardar log ───────────────────────────────────────────────────────────────
fin = datetime.now()
duracion = str(fin - INICIO).split(".")[0]

with open(LOG_FILE, "w", encoding="utf-8") as f:
    f.write("=" * 65 + "\\n")
    f.write("  REPORTE DE DESCARGA MASIVA — ZOOM\\n")
    f.write(f"  CSV fuente : {csv_path}\\n")
    f.write(f"  Inicio     : {INICIO.strftime('%Y-%m-%d %H:%M:%S')}\\n")
    f.write(f"  Fin        : {fin.strftime('%Y-%m-%d %H:%M:%S')}\\n")
    f.write(f"  Duración   : {duracion}\\n")
    f.write("=" * 65 + "\\n\\n")
    f.write("RESUMEN\\n")
    f.write(f"  Reuniones en CSV : {len(registros)}\\n")
    f.write(f"  Descargados      : {ok}\\n")
    f.write(f"  Omitidos         : {omitidos}  (ya existían o sin MP4)\\n")
    f.write(f"  Con error        : {errores}\\n")
    f.write(f"  Carpeta          : {os.path.abspath(CARPETA_BASE)}\\n\\n")
    if errores_detalle:
        f.write("ERRORES\\n" + "-" * 65 + "\\n")
        for e in errores_detalle:
            f.write(f"  {e}\\n")
        f.write("\\n")
    f.write("LOG COMPLETO\\n" + "-" * 65 + "\\n")
    for l in _log:
        f.write(l + "\\n")

print("─" * 60)
print(f"✅ Descargados : {ok}")
print(f"⏩ Omitidos    : {omitidos}")
if errores: print(f"❌ Errores     : {errores}")
print(f"📄 Log         : {LOG_FILE}")
print(f"📂 Carpeta     : ./{CARPETA_BASE}/")
`;

    const blob = new Blob([script], { type: 'text/plain;charset=utf-8' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = 'zoom_bulk_downloader.py';
    a.click();
    URL.revokeObjectURL(url);
    this.toast.success('Script descargado', 'Edita las credenciales y ejecuta: python3 zoom_bulk_downloader.py archivo.csv');
  }

  generarScript(): void {
    const cuenta = this.cuentaActual?.nombre ?? 'Zoom';
    const items  = this.grabaciones().filter(g => g.link_descarga && g.tipo_archivo === 'MP4');
    if (!items.length) { this.toast.error('Sin datos', 'No hay grabaciones con enlace de descarga'); return; }

    const lineas = items.map(g => {
      const carpeta = this.sanitizar(g.curso);
      const fecha   = g.fecha ? g.fecha.slice(0, 10) : 'sin-fecha';
      const ext     = g.tipo_archivo === 'MP4' ? 'mp4' : g.tipo_archivo === 'TRANSCRIPT' ? 'txt' : 'bin';
      const archivo = `${fecha}_${g.tipo_archivo.toLowerCase()}.${ext}`;
      return `    {"carpeta": "${carpeta}", "archivo": "${archivo}", "url": "${g.link_descarga}", "mb": ${g.tamanio_mb}}`;
    }).join(',\n');

    const script = `#!/usr/bin/env python3
# ─────────────────────────────────────────────────────────────────────────────
#  Script de descarga de grabaciones Zoom — ${cuenta}
#  Generado: ${new Date().toLocaleString('es-BO')}
#  ⚠️  Los enlaces de Zoom expiran en horas. Ejecuta este script lo antes posible.
#
#  Requisitos: pip install requests
#  Uso:        python3 descargar_grabaciones.py
#  Resultado:  carpeta Grabaciones_Zoom/ con subcarpetas por curso
#              + archivo descarga_log_FECHA.txt con el resumen completo
# ─────────────────────────────────────────────────────────────────────────────

import os, sys, time
from datetime import datetime
try:
    import requests
except ImportError:
    print("Instala requests: pip install requests")
    sys.exit(1)

CARPETA_BASE = "Grabaciones_Zoom"
INICIO       = datetime.now()
ARCHIVO_LOG  = f"descarga_log_{INICIO.strftime('%Y%m%d_%H%M%S')}.txt"

grabaciones = [
${lineas}
]

# ── Logger ────────────────────────────────────────────────────────────────────
_log_lineas = []

def log(msg, consola=True):
    ts = datetime.now().strftime("%H:%M:%S")
    linea = f"[{ts}] {msg}"
    _log_lineas.append(linea)
    if consola:
        print(msg)

def guardar_log(ok, omitidos, errores, detalles_error):
    fin = datetime.now()
    duracion = str(fin - INICIO).split(".")[0]
    with open(ARCHIVO_LOG, "w", encoding="utf-8") as f:
        f.write("=" * 65 + "\\n")
        f.write(f"  REPORTE DE DESCARGA DE GRABACIONES ZOOM\\n")
        f.write(f"  Cuenta  : ${cuenta}\\n")
        f.write(f"  Inicio  : {INICIO.strftime('%Y-%m-%d %H:%M:%S')}\\n")
        f.write(f"  Fin     : {fin.strftime('%Y-%m-%d %H:%M:%S')}\\n")
        f.write(f"  Duración: {duracion}\\n")
        f.write("=" * 65 + "\\n\\n")
        f.write(f"RESUMEN\\n")
        f.write(f"  Total archivos : {len(grabaciones)}\\n")
        f.write(f"  Descargados    : {ok}\\n")
        f.write(f"  Omitidos       : {omitidos}  (ya existían)\\n")
        f.write(f"  Con error      : {errores}\\n")
        f.write(f"  Carpeta base   : {os.path.abspath(CARPETA_BASE)}\\n\\n")
        if detalles_error:
            f.write("ERRORES DETALLADOS\\n")
            f.write("-" * 65 + "\\n")
            for e in detalles_error:
                f.write(f"  {e}\\n")
            f.write("\\n")
        f.write("LOG COMPLETO\\n")
        f.write("-" * 65 + "\\n")
        for l in _log_lineas:
            f.write(l + "\\n")
    print(f"\\n📄 Log guardado en: {ARCHIVO_LOG}")

# ── Descarga ──────────────────────────────────────────────────────────────────
def descargar(url, ruta, mb):
    try:
        r = requests.get(url, stream=True, timeout=120)
        if r.status_code != 200:
            msg = f"Error HTTP {r.status_code}"
            log(f"  ✗ {msg}")
            return False, msg
        total = int(r.headers.get("content-length", int(mb) * 1_048_576))
        descargado = 0
        with open(ruta, "wb") as f:
            for chunk in r.iter_content(chunk_size=65536):
                f.write(chunk)
                descargado += len(chunk)
                pct = min(100, int(descargado * 100 / total)) if total else 0
                print(f"  ↓ {pct:3d}%", end="\\r")
        tam = os.path.getsize(ruta) / 1_048_576
        log(f"  ✓ {tam:.1f} MB guardados en: {ruta}")
        return True, None
    except Exception as e:
        log(f"  ✗ Excepción: {e}")
        return False, str(e)

# ── Main ──────────────────────────────────────────────────────────────────────
log(f"Inicio de descarga: {INICIO.strftime('%Y-%m-%d %H:%M:%S')}", consola=False)
log(f"Cuenta: ${cuenta}", consola=False)
log(f"Total de archivos: {len(grabaciones)}", consola=False)
print(f"\\n📁 Descargando {len(grabaciones)} archivo(s) → ./{CARPETA_BASE}/\\n")

ok = omitidos = errores = 0
detalles_error = []

for i, g in enumerate(grabaciones, 1):
    carpeta = os.path.join(CARPETA_BASE, g["carpeta"])
    os.makedirs(carpeta, exist_ok=True)
    ruta = os.path.join(carpeta, g["archivo"])
    ruta_abs = os.path.abspath(ruta)

    print(f"[{i}/{len(grabaciones)}] {g['carpeta']}")
    print(f"  → {g['archivo']}  ({g['mb']} MB)")
    log(f"[{i}/{len(grabaciones)}] Curso   : {g['carpeta']}", consola=False)
    log(f"  Archivo : {g['archivo']} ({g['mb']} MB)", consola=False)
    log(f"  Ruta    : {ruta_abs}", consola=False)

    if os.path.exists(ruta):
        print(f"  ⏩ Ya existe, omitiendo")
        log(f"  Estado  : OMITIDO (ya existe)", consola=False)
        omitidos += 1
    else:
        exito, error_msg = descargar(g["url"], ruta, g["mb"])
        if exito:
            log(f"  Estado  : OK", consola=False)
            ok += 1
        else:
            log(f"  Estado  : ERROR — {error_msg}", consola=False)
            detalles_error.append(f"[{i}] {g['carpeta']} / {g['archivo']} → {error_msg}")
            errores += 1
    log("", consola=False)
    print()
    time.sleep(0.3)

# ── Resumen final ─────────────────────────────────────────────────────────────
sep = "─" * 60
print(sep)
print(f"✅ Descargados : {ok}")
print(f"⏩ Omitidos    : {omitidos}  (ya existían)")
if errores:
    print(f"❌ Con error   : {errores}")
print(f"📂 Carpeta     : ./{CARPETA_BASE}/")

log(sep, consola=False)
log(f"Descargados : {ok}", consola=False)
log(f"Omitidos    : {omitidos}", consola=False)
log(f"Errores     : {errores}", consola=False)

guardar_log(ok, omitidos, errores, detalles_error)
`;

    const blob = new Blob([script], { type: 'text/plain;charset=utf-8' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = `descargar_grabaciones_${this.sanitizar(cuenta)}.py`;
    a.click();
    URL.revokeObjectURL(url);
    this.toast.success('Script descargado', '⚠️ Ejecútalo pronto — los enlaces expiran en horas');
  }

  badgeTipo(tipo: string): string {
    const map: Record<string, string> = { MP4: 'bg-primary/10 text-primary', TRANSCRIPT: 'bg-success/10 text-success', CHAT: 'bg-warning/10 text-warning' };
    return map[tipo] ?? 'bg-default-200 text-default-600';
  }

  private get filas(): Record<string, any>[] {
    const cuenta = this.cuentaActual?.nombre ?? 'Zoom';
    return this.grabaciones().map((g, i) => ({
      '#':            i + 1,
      'Cuenta':       cuenta,
      'Curso/Reunión': g.curso,
      'Fecha':        g.fecha ? new Date(g.fecha).toLocaleString('es-BO') : '',
      'Duración (min)': g.duracion_min,
      'Tipo':         g.tipo_archivo,
      'Tamaño (MB)':  g.tamanio_mb,
      'Enlace Descarga': g.link_descarga ?? '',
      'Enlace Reproducción': g.link_play ?? '',
    }));
  }

  exportarExcel(): void {
    const ws = XLSX.utils.json_to_sheet(this.filas);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Grabaciones');
    const cuenta = this.cuentaActual?.nombre ?? 'zoom';
    XLSX.writeFile(wb, `grabaciones-zoom-${cuenta.toLowerCase().replace(/\s+/g, '-')}-${new Date().toISOString().slice(0, 10)}.xlsx`);
    this.toast.success('Exportado', 'Archivo Excel descargado');
  }

  exportarCSV(): void {
    const filas = this.filas;
    if (!filas.length) return;
    const headers = Object.keys(filas[0]);
    const rows = filas.map(f => headers.map(h => `"${String(f[h]).replace(/"/g, '""')}"`).join(','));
    const csv = [headers.join(','), ...rows].join('\r\n');
    const blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    const cuenta = this.cuentaActual?.nombre ?? 'zoom';
    a.href     = url;
    a.download = `grabaciones-zoom-${cuenta.toLowerCase().replace(/\s+/g, '-')}-${new Date().toISOString().slice(0, 10)}.csv`;
    a.click();
    URL.revokeObjectURL(url);
    this.toast.success('Exportado', 'Archivo CSV descargado');
  }
}
