const fs = require('fs');

// Read file as binary buffer, then find and replace the bad bytes
const file = 'src/app/cursos/presentation/curso-create/curso-create.html';
let content = fs.readFileSync(file, 'latin1'); // read as latin1 so we see raw bytes

// The remaining issue: C3 41 = 'Ã' + 'A' (latin1 0x41) -> should be 'Á' (C3 81 in UTF-8)
// But the file is UTF-8, so 'Ãrea' = bytes C3 83 72 65 61 -> wrong
// Let's just do a string search on the raw Latin-1 read
content = content.split('\xC3\x83\x72\x65\x61').join('\xC3\x81\x72\x65\x61'); // not right either

// Actually let's just do it directly since we know the visible string is "Ãrea"
// In Latin-1: Ã = C3, r = 72, e = 65, a = 61  
// But what we need is Á = C3 81 in UTF-8, followed by rea
// So the raw bytes in the file for "Ãrea" are: C3 83 72 65 61 (Ã in UTF-8 is C3 83, then 'rea')
// Correct: C3 81 72 65 61 (Á in UTF-8 is C3 81, then 'rea')

let buf = fs.readFileSync(file);
let hex = buf.toString('hex');
// C3 83 72 65 61 = Ãrea -> C3 81 72 65 61 = Área
hex = hex.split('c3837265').join('c3817265');
// Same for the label: Ãrea\n -> Área\n  
fs.writeFileSync(file, Buffer.from(hex, 'hex'));

// Verify
let fixed = fs.readFileSync(file, 'utf8');
const count = (fixed.match(/Ãrea/g) || []).length;
console.log('Remaining "Ãrea" occurrences:', count);
console.log('Has "Área":', fixed.includes('Área'));
