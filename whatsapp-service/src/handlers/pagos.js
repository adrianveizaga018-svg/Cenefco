import { setState, getPool } from '../store.js';
import { sendText, getBot, phone } from '../utils/wa.js';

export async function showPagosInfo(sock, jid) {
  const BOT = await getBot();
  const db = getPool();

  const { rows: fechasPago } = await db.query(
    `SELECT fp.descripcion, fp.monto, fp.fecha_vencimiento, fp.estado
     FROM t_fechapago fp
     WHERE fp.estado = 1
       AND (fp.fecha_vencimiento IS NULL OR fp.fecha_vencimiento >= CURRENT_DATE)
     ORDER BY fp.fecha_vencimiento ASC
     LIMIT 5`
  ).catch(() => ({ rows: [] }));

  let texto = `💳 *Información de Pagos — CENEFCO*\n\n`;

  if (fechasPago.length > 0) {
    texto += `*Próximas fechas de pago:*\n`;
    for (const fp of fechasPago) {
      texto += `• ${fp.descripcion || 'Cuota'}`;
      if (fp.monto) texto += ` — Bs. ${parseFloat(fp.monto).toFixed(2)}`;
      if (fp.fecha_vencimiento) {
        const fecha = new Date(fp.fecha_vencimiento).toLocaleDateString('es-BO');
        texto += ` | Vence: ${fecha}`;
      }
      texto += '\n';
    }
    texto += '\n';
  }

  texto += `*Formas de pago:*\n`;
  texto += `💵 Pago en efectivo — Oficinas CENEFCO\n`;
  texto += `🏦 Depósito o transferencia bancaria\n`;
  texto += `💳 Pago en línea: ${BOT.web}/pagos\n\n`;
  texto += `*Horario de caja:*\n`;
  texto += `${BOT.horarios}\n\n`;
  texto += `📞 Consultas: ${BOT.telefono}\n`;
  texto += `📧 ${BOT.email}\n\n`;
  texto += `Escribe *0* para volver al menú principal.`;

  await sendText(sock, jid, texto);
  await setState(phone(jid), 'menu');
}
