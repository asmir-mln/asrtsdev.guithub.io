#!/usr/bin/env node
/**
 * AsArt'sDev CLI - Gestion des commandes
 * Usage: node cli-orders.js [commande] [options]
 * Open-source - MIT License
 */

const https = require('https');
const http = require('http');
const fs = require('fs');
const path = require('path');

const API_URL = 'http://localhost:8000/api/orders.php';
const API_KEY = 'ASARTSDEV_SECRET_2026';

// Couleurs console
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

// Fonction pour faire des requêtes HTTP
function request(method, url, data = null) {
    return new Promise((resolve, reject) => {
        const urlObj = new URL(url);
        const options = {
            hostname: urlObj.hostname,
            port: urlObj.port || 80,
            path: urlObj.pathname + urlObj.search,
            method: method,
            headers: {
                'Content-Type': 'application/json'
            }
        };

        const req = http.request(options, (res) => {
            let body = '';
            res.on('data', chunk => body += chunk);
            res.on('end', () => {
                try {
                    resolve(JSON.parse(body));
                } catch (e) {
                    resolve(body);
                }
            });
        });

        req.on('error', reject);
        
        if (data) {
            req.write(JSON.stringify(data));
        }
        
        req.end();
    });
}

// Commandes CLI
const commands = {
    // Lister toutes les commandes
    async list() {
        log('\n📋 Liste des commandes:', 'bold');
        
        try {
            const response = await request('GET', `${API_URL}?api_key=${API_KEY}`);
            
            if (!response.orders || response.orders.length === 0) {
                log('Aucune commande trouvée.', 'yellow');
                return;
            }
            
            response.orders.forEach((order, index) => {
                log(`\n${index + 1}. ${order.id}`, 'cyan');
                log(`   Client: ${order.nom} (${order.email})`);
                log(`   Produit: ${order.produit}`);
                log(`   Prix: ${order.prix}`);
                log(`   Statut: ${order.statut}`);
                log(`   Paiement reçu: ${order.paiement_recu ? '✅ Oui' : '❌ Non'}`);
                log(`   Date: ${order.date_creation}`);
            });
            
        } catch (error) {
            log('❌ Erreur lors de la récupération des commandes', 'red');
            console.error(error);
        }
    },
    
    // Créer une nouvelle commande de test
    async create() {
        log('\n➕ Création d\'une commande de test...', 'bold');
        
        const testOrder = {
            nom: 'Test Client',
            email: 'test@example.com',
            telephone: '0123456789',
            produit: 'Max et Milla',
            prix: '15,00 €',
            message: 'Commande test via CLI'
        };
        
        try {
            const response = await request('POST', API_URL, testOrder);
            
            if (response.success) {
                log('✅ Commande créée avec succès!', 'green');
                log(`ID: ${response.order.id}`, 'cyan');
            } else {
                log('❌ Erreur lors de la création', 'red');
            }
            
        } catch (error) {
            log('❌ Erreur lors de la création de la commande', 'red');
            console.error(error);
        }
    },
    
    // Marquer une commande comme payée
    async paid(orderId) {
        if (!orderId) {
            log('❌ Usage: node cli-orders.js paid <ORDER_ID>', 'red');
            return;
        }
        
        log(`\n💳 Marquage commande ${orderId} comme payée...`, 'bold');
        
        try {
            const response = await request('PUT', API_URL, {
                id: orderId,
                api_key: API_KEY,
                statut: 'Payé',
                paiement_recu: true
            });
            
            if (response.success) {
                log('✅ Commande mise à jour!', 'green');
            } else {
                log('❌ Erreur lors de la mise à jour', 'red');
            }
            
        } catch (error) {
            log('❌ Erreur lors de la mise à jour', 'red');
            console.error(error);
        }
    },
    
    // Afficher l'aide
    help() {
        log('\n📖 AsArt\'sDev CLI - Commandes disponibles:', 'bold');
        log('\nnode cli-orders.js list          - Lister toutes les commandes', 'cyan');
        log('node cli-orders.js create        - Créer une commande test', 'cyan');
        log('node cli-orders.js paid <ID>     - Marquer une commande comme payée', 'cyan');
        log('node cli-orders.js help          - Afficher cette aide', 'cyan');
        log('\n');
    }
};

// Exécution
const [,, command, ...args] = process.argv;

if (!command || !commands[command]) {
    commands.help();
} else {
    commands[command](...args).catch(err => {
        log('❌ Erreur:', 'red');
        console.error(err);
    });
}
