import { Router }  from 'express';
import QRCode       from 'qrcode';
import { randomUUID } from 'crypto';
import {
  getInstances, getInstance,
  connectCuenta, disconnectCuenta,
  sendFromCuenta,
} from '../manager.js';
import {
  getCuentas, getCuentaById,
  createCuenta, deleteCuenta,
} from '../store.js';

const router = Router();

router.get('/', async (_req, res) => {
  try {
    const cuentas = await getCuentas();
    const data    = cuentas.map(c => {
      const inst = getInstance(c.id);
      return {
        id:         c.id,
        nombre:     c.nombre,
        phone:      inst?.phone ?? c.phone ?? null,
        status:     inst?.status ?? 'close',
        hasQr:      inst?.status === 'qr',
        estado:     c.estado,
        created_at: c.created_at,
      };
    });
    res.json({ data });
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

router.post('/', async (req, res) => {
  const { nombre } = req.body;
  if (!nombre?.trim()) return res.status(422).json({ message: 'El nombre es requerido' });
  try {
    const id = randomUUID();
    await createCuenta(id, nombre.trim());
    res.status(201).json({ id, nombre: nombre.trim(), status: 'close', hasQr: false });
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

router.delete('/:id', async (req, res) => {
  try {
    await disconnectCuenta(req.params.id);
    await deleteCuenta(req.params.id);
    res.json({ message: 'Cuenta eliminada' });
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

router.get('/:id/status', (req, res) => {
  const inst = getInstance(req.params.id);
  res.json({
    status: inst?.status ?? 'close',
    hasQr:  inst?.status === 'qr',
    phone:  inst?.phone  ?? null,
  });
});

router.get('/:id/qr', async (req, res) => {
  const inst = getInstance(req.params.id);
  if (!inst?.qr) return res.status(404).json({ message: 'Sin QR disponible' });
  try {
    const dataUrl = await QRCode.toDataURL(inst.qr, { width: 300, margin: 2 });
    res.json({ qr: dataUrl });
  } catch {
    res.status(500).json({ message: 'Error al generar el QR' });
  }
});

router.post('/:id/connect', async (req, res) => {
  try {
    const cuenta = await getCuentaById(req.params.id);
    if (!cuenta) return res.status(404).json({ message: 'Cuenta no encontrada' });
    await connectCuenta(cuenta.id, cuenta.nombre);
    res.json({ message: 'Conectando…' });
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

router.post('/:id/disconnect', async (req, res) => {
  try {
    await disconnectCuenta(req.params.id);
    res.json({ message: 'Desconectado' });
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

router.post('/:id/send', async (req, res) => {
  const { phone, mensaje } = req.body;
  if (!phone || !mensaje) return res.status(422).json({ message: 'Se requiere phone y mensaje' });
  try {
    await sendFromCuenta(req.params.id, phone, mensaje);
    res.json({ message: 'Mensaje enviado' });
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

export default router;
