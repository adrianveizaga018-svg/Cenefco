export function removeAccents(text) {
  return text.normalize('NFD').replace(/[̀-ͯ]/g, '');
}

const SMS_NORM = [
  [/\bq\b/g, 'que'], [/\bk\b/g, 'que'], [/\bx\b/g, 'por'],
  [/\bxq\b|\bxk\b|\bpq\b/g, 'porque'], [/\btbn?\b|\btmb\b/g, 'tambien'],
  [/\bola\b/g, 'hola'], [/\bke\b|\bki\b/g, 'que'],
  [/\bkmo\b/g, 'como'], [/\bkien\b/g, 'quien'], [/\baki\b/g, 'aqui'],
  [/\bgrax?\b|\bgrac\b/g, 'gracias'], [/\bpf\b|\bxfa\b/g, 'por favor'],
  [/\bmsj\b/g, 'mensaje'], [/\btel\b(?!efono)/g, 'telefono'],
  [/\bdto\b/g, 'departamento'], [/\bxfavor\b/g, 'por favor'],
  [/\bporfa\b/g, 'por favor'], [/(\w)\1{2,}/g, '$1$1'],
];

export function normalizeText(text) {
  let t = removeAccents(text.toLowerCase().trim());
  for (const [pat, rep] of SMS_NORM) t = t.replace(pat, rep);
  return t;
}
