/* AsArt'sDev | Signature invisible | ASmir Milia | ASARTSDEV_SIGNATURE_INVISIBLE */
// Script pour la page de commande
// AsArt'sDev

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('#formulaire form');
    const DISCOUNT_RATE = 0.05;
    const SHIPPING_FEE = 4.90;
    const SERVICE_FEE_RATE = 0.015;

    const productCatalog = {
        'max-milla': { label: 'Max et Milla - version finale enfant', price: 15.00, physical: true },
        'biographie-cycle': { label: 'Max, Mila et le Perroquet - La fin du cycle des 10 ans', price: 24.90, physical: true },
        'autobiographie-adulte': { label: 'Les trois vies d\'Asmir - La fin du cycle des dix ans', price: 39.99, physical: true },
        'don': { label: 'Don libre (montant au choix)', price: 0, physical: false }
    };

    function getCart() {
        const raw = localStorage.getItem('cartData');
        if (!raw) {
            return { items: [] };
        }

        try {
            const parsed = JSON.parse(raw);
            if (!parsed.items || !Array.isArray(parsed.items)) {
                return { items: [] };
            }
            return parsed;
        } catch (error) {
            return { items: [] };
        }
    }

    function saveCart(cart) {
        localStorage.setItem('cartData', JSON.stringify(cart));
    }

    function addOrAccumulateItem(cart, item) {
        const existing = cart.items.find((it) => it.id === item.id);
        if (existing) {
            existing.quantite += item.quantite;
        } else {
            cart.items.push(item);
        }
    }

    function computeTotals(cart) {
        const subtotal = cart.items.reduce((sum, item) => sum + (item.prix * item.quantite), 0);
        const discount = subtotal * DISCOUNT_RATE;
        const hasPhysical = cart.items.some((item) => item.physical);
        const shipping = hasPhysical ? SHIPPING_FEE : 0;
        const serviceFee = subtotal > 0 ? subtotal * SERVICE_FEE_RATE : 0;
        const total = subtotal - discount + shipping + serviceFee;

        return {
            subtotal: Number(subtotal.toFixed(2)),
            discount: Number(discount.toFixed(2)),
            shipping: Number(shipping.toFixed(2)),
            serviceFee: Number(serviceFee.toFixed(2)),
            total: Number(total.toFixed(2))
        };
    }

    async function registerOrder(orderPayload) {
        try {
            await fetch('api/orders.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(orderPayload)
            });
        } catch (error) {
            // La redirection paiement reste prioritaire même si l'API est indisponible.
            console.error('Enregistrement API indisponible:', error);
        }
    }
    
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Récupérer les données du formulaire
            const formData = new FormData(form);
            const produitSelect = document.getElementById('produit');
            const produitKey = formData.get('produit');
            const product = productCatalog[produitKey] || { label: produitKey || 'Produit', price: 0, physical: false };
            const prix = product.price;
            
            const quantite = parseInt(formData.get('quantite')) || 1;
            const cart = getCart();

            addOrAccumulateItem(cart, {
                id: produitKey,
                produit: product.label,
                prix: prix,
                quantite: quantite,
                physical: !!product.physical
            });

            const totals = computeTotals(cart);

            cart.summary = totals;
            saveCart(cart);
            
            // Préparer les données de commande
            const orderData = {
                produit: product.label,
                prix: prix.toFixed(2) + ' €',
                quantite: quantite,
                nom: formData.get('nom'),
                email: formData.get('email'),
                telephone: formData.get('telephone'),
                message: formData.get('message'),
                panier: cart.items,
                subtotal: totals.subtotal,
                remise: totals.discount,
                frais_envoi: totals.shipping,
                frais_service: totals.serviceFee,
                total: totals.total,
                statut: 'En attente de paiement',
                confirmation_instantanee: true
            };
            
            // Sauvegarder dans localStorage
            localStorage.setItem('orderData', JSON.stringify(orderData));
            await registerOrder(orderData);
            
            // Rediriger vers la page de paiement
            if (totals.total > 0) {
                window.location.href = 'paiement.html';
            } else {
                // Pour les créations personnalisées (devis), envoyer par email
                alert('Merci pour votre demande de devis. Nous vous contacterons rapidement.');
                // TODO: Envoyer le formulaire par email
            }
        });
    }
});


