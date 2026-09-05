import makeWASocket, {
  useMultiFileAuthState,
  DisconnectReason,
  fetchLatestBaileysVersion,
  makeCacheableSignalKeyStore,
  isJidBroadcast,
} from '@whiskeysockets/baileys';
import { Boom } from '@hapi/boom';
import pino from 'pino';
import { rmSync, existsSync } from 'fs';
import { handleMessage } from './chatbot.js';

const AUTH_DIR = './auth_info_baileys';

function clearAuthFiles() {
  if (existsSync(AUTH_DIR)) {
    rmSync(AUTH_DIR, { recursive: true, force: true });
    console.log('[WhatsApp] Sesión limpiada — se generará nuevo QR.');
  }
}

const logger = pino({ level: 'silent' });

let sock = null;
let qrCode = null;
let connectionState = 'close';
let ioInstance = null;

export function setIo(io) {
  ioInstance = io;
}

export function getStatus() {
  return { state: connectionState, hasQr: !!qrCode };
}

export function getQr() {
  return qrCode;
}

export function getSocket() {
  return sock;
}

export async function sendMessage(phone, message) {
  if (!sock || connectionState !== 'open') {
    throw new Error('WhatsApp no está conectado');
  }
  const jid = formatJid(phone);
  await sock.sendMessage(jid, { text: message });
}

export async function sendMediaMessage(phone, type, buffer, mimetype, caption, filename) {
  if (!sock || connectionState !== 'open') {
    throw new Error('WhatsApp no está conectado');
  }
  const jid = formatJid(phone);
  if (type === 'image') {
    await sock.sendMessage(jid, { image: buffer, caption });
  } else if (type === 'document') {
    await sock.sendMessage(jid, { document: buffer, mimetype, fileName: filename, caption });
  }
}

export async function disconnect() {
  if (sock) {
    await sock.logout();
    sock = null;
    qrCode = null;
    connectionState = 'close';
    emitStatus();
  }
}

export async function startBot() {
  const { state, saveCreds } = await useMultiFileAuthState('./auth_info_baileys');
  const { version } = await fetchLatestBaileysVersion();

  sock = makeWASocket({
    version,
    logger,
    auth: {
      creds: state.creds,
      keys: makeCacheableSignalKeyStore(state.keys, logger),
    },
    printQRInTerminal: false,
    generateHighQualityLinkPreview: false,
    getMessage: async () => undefined,
  });

  sock.ev.on('creds.update', saveCreds);

  sock.ev.on('connection.update', async (update) => {
    const { connection, lastDisconnect, qr } = update;

    if (qr) {
      qrCode = qr;
      connectionState = 'connecting';
      emitStatus();
      emitQr(qr);
    }

    if (connection === 'close') {
      qrCode = null;
      connectionState = 'close';
      emitStatus();

      const statusCode = new Boom(lastDisconnect?.error)?.output?.statusCode;
      console.log('[WhatsApp] Conexión cerrada. Código:', statusCode);

      if (statusCode === DisconnectReason.loggedOut || statusCode === 401) {
        clearAuthFiles();
        setTimeout(() => startBot(), 2000);
      } else {
        setTimeout(() => startBot(), 3000);
      }
    }

    if (connection === 'open') {
      qrCode = null;
      connectionState = 'open';
      emitStatus();
      console.log('[WhatsApp] ¡Conectado!');
    }
  });

  sock.ev.on('messages.upsert', async ({ messages, type }) => {
    if (type !== 'notify') return;

    for (const msg of messages) {
      if (msg.key.fromMe) continue;
      if (isJidBroadcast(msg.key.remoteJid)) continue;
      if (!msg.message) continue;

      try {
        await handleMessage(sock, msg);
      } catch (err) {
        console.error('[WhatsApp] Error al procesar mensaje:', err.message);
      }
    }
  });
}

function formatJid(phone) {
  if (phone.includes('@')) return phone;
  return `${phone.replace(/\D/g, '')}@s.whatsapp.net`;
}

function emitStatus() {
  ioInstance?.emit('whatsapp:status', getStatus());
}

function emitQr(qr) {
  ioInstance?.emit('whatsapp:qr', { qr });
}
