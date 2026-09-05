import makeWASocket, {
  useMultiFileAuthState,
  DisconnectReason,
  fetchLatestBaileysVersion,
  makeCacheableSignalKeyStore,
  isJidBroadcast,
} from '@whiskeysockets/baileys';
import { Boom } from '@hapi/boom';
import pino from 'pino';
import { mkdirSync, rmSync, existsSync } from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { handleMessage } from './chatbot.js';
import { getPool } from './store.js';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const AUTH_BASE  = path.join(__dirname, '..', 'auth_cuentas');
const logger     = pino({ level: 'silent' });

const instances = new Map();
let ioInstance  = null;

export function setIoManager(io) { ioInstance = io; }
export function getInstances()   { return instances; }
export function getInstance(id)  { return instances.get(id); }

function emitCuentaStatus(id) {
  const inst = instances.get(id);
  ioInstance?.emit('cuenta:status', {
    id,
    status: inst?.status ?? 'close',
    phone:  inst?.phone  ?? null,
    hasQr:  inst?.status === 'qr',
  });
}

export async function connectCuenta(id, nombre) {
  const existing = instances.get(id);
  if (existing && ['open', 'connecting', 'qr'].includes(existing.status)) return;

  const authDir = path.join(AUTH_BASE, id);
  mkdirSync(authDir, { recursive: true });

  const { state, saveCreds }  = await useMultiFileAuthState(authDir);
  const { version }           = await fetchLatestBaileysVersion();

  const sock = makeWASocket({
    version,
    logger,
    auth: {
      creds: state.creds,
      keys:  makeCacheableSignalKeyStore(state.keys, logger),
    },
    printQRInTerminal:          false,
    generateHighQualityLinkPreview: false,
    getMessage:                 async () => undefined,
    browser:                    ['CENEFCO Bot', 'Chrome', '1.0'],
  });

  instances.set(id, { sock, status: 'connecting', qr: null, phone: null, nombre, reconnecting: false, connectingTimer: null });

  // Si después de 40s sigue en 'connecting' sin QR ni conexión, forzar reconexión limpia
  const connectingTimeout = setTimeout(() => {
    const inst = instances.get(id);
    if (inst && inst.status === 'connecting') {
      console.log(`[Manager] ${nombre} timeout en connecting. Reconectando…`);
      inst.reconnecting = true;
      instances.delete(id);
      try { inst.sock.ws?.close(); inst.sock.ev?.removeAllListeners(); } catch (_) {}
      setTimeout(() => connectCuenta(id, nombre), 2000);
    }
  }, 40000);
  instances.get(id).connectingTimer = connectingTimeout;

  sock.ev.on('creds.update', saveCreds);

  sock.ev.on('connection.update', async (update) => {
    const { connection, lastDisconnect, qr } = update;
    const inst = instances.get(id);
    if (!inst) return;

    if (qr) {
      inst.qr     = qr;
      inst.status = 'qr';
      if (inst.connectingTimer) { clearTimeout(inst.connectingTimer); inst.connectingTimer = null; }
      emitCuentaStatus(id);
    }

    if (connection === 'open') {
      inst.status      = 'open';
      if (inst.connectingTimer) { clearTimeout(inst.connectingTimer); inst.connectingTimer = null; }
      inst.qr          = null;
      inst.reconnecting = false;
      inst.phone        = sock.user?.id?.split(':')[0] ?? null;
      emitCuentaStatus(id);
      try {
        await getPool().query(
          'UPDATE whatsapp_cuentas SET phone = $1, estado = $2 WHERE id = $3',
          [inst.phone, 'activo', id]
        );
      } catch (_) {}
      console.log(`[Manager] ✓ ${nombre} conectado — ${inst.phone}`);
    }

    if (connection === 'close') {
      inst.status = 'close';
      inst.qr     = null;
      emitCuentaStatus(id);

      const code     = new Boom(lastDisconnect?.error)?.output?.statusCode;
      const loggedOut = code === DisconnectReason.loggedOut || code === 401;

      if (loggedOut) {
        // Sesión expirada en servidor — limpiar auth local y reconectar para generar QR
        console.log(`[Manager] ${nombre} sesión expirada. Limpiando auth y generando QR…`);
        instances.delete(id);
        const authDir = path.join(AUTH_BASE, id);
        if (existsSync(authDir)) {
          try { rmSync(authDir, { recursive: true, force: true }); } catch (_) {}
        }
        try {
          await getPool().query(
            'UPDATE whatsapp_cuentas SET phone = NULL WHERE id = $1',
            [id]
          );
        } catch (_) {}
        // Reconectar en 2s para generar QR nuevo
        setTimeout(() => connectCuenta(id, nombre), 2000);
      } else if (!inst.reconnecting) {
        inst.reconnecting = true;
        console.log(`[Manager] ${nombre} perdió conexión. Reconectando en 5s…`);
        setTimeout(() => connectCuenta(id, nombre), 5000);
      }
    }
  });

  // Captura el número real cuando Baileys resuelve un LID (@lid → número real)
  sock.ev.on('contacts.upsert', async (contacts) => {
    for (const contact of contacts) {
      if (!contact.id || !contact.notify) continue;
      // contact.id puede ser "591730530453@s.whatsapp.net" o "111609187950844@lid"
      // contact.notify es el pushName; lo que nos interesa es encontrar si hay un LID
      // y mapear al número real via contact.id que a veces es el número real
      const jid = contact.id;
      if (!jid.endsWith('@s.whatsapp.net')) continue;
      const realPhone = jid.split('@')[0];
      if (!realPhone) continue;
      try {
        await getPool().query(
          `UPDATE whatsapp_conversaciones
           SET phone_display = $1
           WHERE phone_display IS NULL
             AND (phone = $2 OR nombre = $3)`,
          [`+${realPhone}`, jid, contact.notify ?? '']
        );
      } catch (_) {}
    }
  });

  sock.ev.on('messages.upsert', async ({ messages, type }) => {
    if (type !== 'notify') return;
    for (const msg of messages) {
      if (msg.key.fromMe)                      continue;
      if (isJidBroadcast(msg.key.remoteJid))   continue;
      if (!msg.message)                        continue;
      try {
        await handleMessage(sock, msg);
      } catch (err) {
        console.error(`[Manager] Error en ${nombre}:`, err.message);
      }
    }
  });
}

export async function disconnectCuenta(id) {
  const inst = instances.get(id);
  if (!inst) return;

  const wasOpen = inst.status === 'open';
  inst.reconnecting = true; // prevent auto-reconnect loop
  instances.delete(id); // remove before async ops so event handlers bail early

  try {
    if (wasOpen) {
      await inst.sock.logout();
    } else {
      inst.sock.ws?.close();
      inst.sock.ev?.removeAllListeners();
    }
  } catch (_) {}

  try {
    await getPool().query(
      'UPDATE whatsapp_cuentas SET estado = $1, phone = NULL WHERE id = $2',
      ['inactivo', id]
    );
  } catch (_) {}
}

// Desvincular sesión y reconectar para generar nuevo QR (sin marcar como inactivo)
// Si id es null, opera sobre todas las cuentas en DB
export async function disconnectAndReconnect(id) {
  // Detener instancia en memoria si existe
  const inst = id ? instances.get(id) : null;
  if (inst) {
    inst.reconnecting = true;
    instances.delete(id);
    try {
      inst.sock.ws?.close();
      inst.sock.ev?.removeAllListeners();
    } catch (_) {}
  }

  // Obtener cuentas a reconectar
  let cuentas = [];
  if (id) {
    const result = await getPool().query(
      'SELECT id, nombre FROM whatsapp_cuentas WHERE id = $1', [id]
    ).catch(() => ({ rows: [] }));
    cuentas = result?.rows ?? [];
  } else {
    const result = await getPool().query(
      'SELECT id, nombre FROM whatsapp_cuentas'
    ).catch(() => ({ rows: [] }));
    cuentas = result?.rows ?? [];
  }

  for (const cuenta of cuentas) {
    // Borrar carpeta de sesión para forzar QR nuevo
    const authDir = path.join(AUTH_BASE, cuenta.id);
    if (existsSync(authDir)) {
      try { rmSync(authDir, { recursive: true, force: true }); } catch (_) {}
    }

    // Reactivar cuenta y limpiar phone en DB
    try {
      await getPool().query(
        'UPDATE whatsapp_cuentas SET phone = NULL, estado = $1 WHERE id = $2',
        ['activo', cuenta.id]
      );
    } catch (_) {}

    // Parar cualquier instancia remanente con este id
    const rem = instances.get(cuenta.id);
    if (rem) {
      rem.reconnecting = true;
      instances.delete(cuenta.id);
      try { rem.sock.ws?.close(); rem.sock.ev?.removeAllListeners(); } catch (_) {}
    }

    console.log(`[Manager] Reconectando ${cuenta.nombre} para nuevo QR…`);
    await connectCuenta(cuenta.id, cuenta.nombre);
  }
}

export async function sendFromCuenta(id, toPhone, text) {
  const inst = instances.get(id);
  if (!inst || inst.status !== 'open') throw new Error(`Cuenta ${id} no está conectada`);
  const jid = toPhone.includes('@') ? toPhone : `${toPhone.replace(/\D/g, '')}@s.whatsapp.net`;
  await inst.sock.sendMessage(jid, { text });
}

export async function initAllCuentas() {
  try {
    const { rows } = await getPool().query(
      'SELECT id, nombre FROM whatsapp_cuentas WHERE estado = $1',
      ['activo']
    );
    for (const row of rows) {
      console.log(`[Manager] Reconectando: ${row.nombre}`);
      await connectCuenta(row.id, row.nombre);
    }
  } catch (err) {
    console.error('[Manager] Error al inicializar cuentas:', err.message);
  }
}
