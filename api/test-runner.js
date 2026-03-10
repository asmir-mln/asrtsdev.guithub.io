#!/usr/bin/env node
/**
 * AsArt'sDev - Lanceur de tests intégré
 * Lance automatiquement le serveur PHP, exécute les tests, puis arrête le serveur.
 * Usage: node api/test-runner.js
 */

const { spawn, execSync } = require('child_process');
const http = require('http');
const path = require('path');

const PORT = 8000;
const HOST = 'localhost';
const ROOT = path.resolve(__dirname, '..');
const WAIT_TIMEOUT_MS = 10000;
const POLL_INTERVAL_MS = 200;
const SHUTDOWN_DELAY_MS = 300;

const colors = {
    reset: '\x1b[0m',
    green: '\x1b[32m',
    yellow: '\x1b[33m',
    red: '\x1b[31m',
    cyan: '\x1b[36m',
    bold: '\x1b[1m'
};

function log(message, color = 'reset') {
    console.log(colors[color] + message + colors.reset);
}

/**
 * Attend que le serveur HTTP soit prêt sur HOST:PORT.
 */
function waitForServer(timeout) {
    return new Promise((resolve, reject) => {
        const start = Date.now();

        function probe() {
            const req = http.request({ hostname: HOST, port: PORT, path: '/', method: 'HEAD' }, () => {
                resolve();
            });
            req.on('error', () => {
                if (Date.now() - start >= timeout) {
                    reject(new Error(`Le serveur n'a pas démarré dans le délai imparti de ${timeout}ms.`));
                } else {
                    setTimeout(probe, POLL_INTERVAL_MS);
                }
            });
            req.end();
        }

        probe();
    });
}

async function main() {
    log('\n🚀 AsArt\'sDev — Lanceur de tests intégré', 'bold');
    log(`   Répertoire racine : ${ROOT}`, 'cyan');

    // Démarrer le serveur PHP en arrière-plan
    log('\n▶  Démarrage du serveur PHP sur http://localhost:8000 ...', 'yellow');
    const server = spawn('php', ['-S', `${HOST}:${PORT}`, '-t', ROOT], {
        cwd: ROOT,
        stdio: ['ignore', 'pipe', 'pipe']
    });

    server.stderr.on('data', (data) => {
        // Le serveur PHP écrit ses logs de démarrage sur stderr.
        // On affiche uniquement les lignes d'erreur fatale pour faciliter le diagnostic.
        const line = data.toString();
        if (/error|fatal/i.test(line) && !/^PHP \d/.test(line)) {
            process.stderr.write(line);
        }
    });

    let exitCode = 0;

    try {
        // Attendre que le serveur accepte des connexions
        await waitForServer(WAIT_TIMEOUT_MS);
        log('✅ Serveur prêt.\n', 'green');

        // --- Test 1 : créer une commande ---
        log('➕ Test 1 : Création d\'une commande de test...', 'bold');
        try {
            execSync(`node ${path.join(__dirname, 'cli-orders.js')} create`, { stdio: 'inherit' });
        } catch {
            log('❌ Test 1 échoué.', 'red');
            exitCode = 1;
        }

        // --- Test 2 : lister les commandes ---
        log('\n📋 Test 2 : Liste des commandes...', 'bold');
        try {
            execSync(`node ${path.join(__dirname, 'cli-orders.js')} list`, { stdio: 'inherit' });
        } catch {
            log('❌ Test 2 échoué.', 'red');
            exitCode = 1;
        }

    } catch (err) {
        log(`\n❌ Erreur : ${err.message}`, 'red');
        exitCode = 1;
    } finally {
        // Arrêter proprement le serveur PHP
        log('\n⏹  Arrêt du serveur PHP...', 'yellow');
        server.kill('SIGTERM');
        await new Promise(resolve => setTimeout(resolve, SHUTDOWN_DELAY_MS));
        log(exitCode === 0 ? '✅ Tests terminés avec succès.\n' : '❌ Certains tests ont échoué.\n',
            exitCode === 0 ? 'green' : 'red');
    }

    process.exit(exitCode);
}

main();
