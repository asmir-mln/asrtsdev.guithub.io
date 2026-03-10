#!/usr/bin/env node
/**
 * AsArt'sDev — Validateur de site statique
 * Vérifie que les fichiers HTML principaux existent et que les liens internes sont valides.
 * Usage : node validate-site.js
 */

const fs = require('fs');
const path = require('path');

const ROOT = __dirname;

const REQUIRED_FILES = [
  'index.html',
  'home.html',
  'contact.html',
  'commande.html',
  'paiement.html',
  'confirmation-paiement.html',
  'donation.php',
  'musee.html',
  'bibliotheque.html',
  'image-protection.html',
  'style.css',
  'paiement.js',
];

let errors = 0;
let warnings = 0;

console.log('🔍 Validation du site AsArt\'sDev\n');

// 1. Vérifier les fichiers requis
console.log('── Fichiers requis ──');
REQUIRED_FILES.forEach(file => {
  const fullPath = path.join(ROOT, file);
  if (fs.existsSync(fullPath)) {
    console.log('  ✅ ' + file);
  } else {
    console.log('  ❌ MANQUANT: ' + file);
    errors++;
  }
});

// 2. Vérifier les liens internes dans tous les fichiers HTML
console.log('\n── Liens internes (fichiers HTML) ──');
const htmlFiles = fs.readdirSync(ROOT).filter(f => f.endsWith('.html'));

htmlFiles.forEach(file => {
  const content = fs.readFileSync(path.join(ROOT, file), 'utf8');

  // Extraire tous les href et src qui ne sont pas externes ou ancres
  const linkPattern = /(?:href|src)="([^"]+)"/g;
  let match;

  while ((match = linkPattern.exec(content)) !== null) {
    const ref = match[1];

    // Ignorer les liens externes, ancres, protocoles spéciaux et template literals JS
    if (
      ref.startsWith('http') ||
      ref.startsWith('#') ||
      ref.startsWith('mailto:') ||
      ref.startsWith('tel:') ||
      ref.startsWith('javascript:') ||
      ref.startsWith('vbscript:') ||
      ref.startsWith('data:') ||
      ref.includes('${')
    ) {
      continue;
    }

    // Extraire le chemin (sans query string ni ancre)
    const refPath = ref.split('?')[0].split('#')[0];
    if (!refPath) continue;

    const fullRef = path.join(ROOT, refPath);
    if (!fs.existsSync(fullRef)) {
      console.log('  ⚠️  ' + file + ' -> ' + ref);
      warnings++;
    }
  }
});

if (warnings === 0) {
  console.log('  ✅ Aucun lien interne cassé');
}

// 3. Résumé
console.log('\n── Résumé ──');
if (errors === 0 && warnings === 0) {
  console.log('✅ Validation réussie — le site est prêt pour le déploiement.\n');
  process.exit(0);
} else {
  if (errors > 0) {
    console.log('❌ ' + errors + ' fichier(s) requis manquant(s)');
  }
  if (warnings > 0) {
    console.log('⚠️  ' + warnings + ' lien(s) interne(s) introuvable(s)');
  }
  console.log('');
  process.exit(errors > 0 ? 1 : 0);
}
