#!/usr/bin/env node
/**
 * AsArt'sDev — Lanceur de tests
 * Démarre le serveur PHP, exécute les tests CLI, puis arrête le serveur.
 */

const { spawn } = require('child_process');
const path = require('path');
const rootDir = path.join(__dirname, '..');

function wait(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function runCli(command) {
    return new Promise((resolve, reject) => {
        const proc = spawn(
            process.execPath,
            [path.join(__dirname, 'cli-orders.js'), command],
            { stdio: 'inherit' }
        );
        proc.on('close', code => {
            if (code === 0) resolve();
            else reject(new Error(`cli-orders.js ${command} a échoué (code ${code})`));
        });
    });
}

async function main() {
    const server = spawn('php', ['-S', 'localhost:8000', '-t', rootDir], {
        stdio: 'ignore',
        detached: false
    });

    server.on('error', err => {
        console.error('❌ Impossible de démarrer le serveur PHP:', err.message);
        process.exit(1);
    });

    // Attendre que le serveur soit prêt
    await wait(1000);

    let exitCode = 0;
    try {
        await runCli('create');
        await runCli('list');
    } catch (err) {
        console.error(err.message);
        exitCode = 1;
    }

    try {
        server.kill();
    } catch (_) {}

    process.exit(exitCode);
}

main();
