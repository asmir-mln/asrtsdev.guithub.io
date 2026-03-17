/* AsArt'sDev | Signature invisible | ASmir Milia | ASARTSDEV_SIGNATURE_INVISIBLE */
// Script de paiement avec Stripe pour AsArt'sDev

let stripe = null;
let cardElement = null;
let orderInfo = null;

function showGlobalError(message) {
    const messageDiv = document.getElementById('payment-message');
    if (!messageDiv) {
        alert(message);
        return;
    }
    messageDiv.textContent = message;
    messageDiv.classList.remove('hidden', 'success', 'error');
    messageDiv.classList.add('error');
}

function disablePaymentForm() {
    const submitButton = document.getElementById('submit-button');
    if (submitButton) {
        submitButton.disabled = true;
    }
}

async function fetchStripePublicKey() {
    const response = await fetch('api/public-config.php', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    });

    const data = await response.json();
    if (!response.ok || data.error || !data.stripe || !data.stripe.public_key) {
        throw new Error(data.error || 'Configuration Stripe publique introuvable.');
    }

    return data.stripe.public_key;
}

function mountStripeCardElement() {
    const elements = stripe.elements();
    cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#e74c3c',
                iconColor: '#e74c3c'
            }
        }
    });

    if (document.getElementById('card-element')) {
        cardElement.mount('#card-element');
    } else {
        throw new Error('Element #card-element manquant.');
    }

    cardElement.on('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });
}

// Récupérer les détails de la commande depuis localStorage ou URL
function getOrderDetails() {
    const cartData = localStorage.getItem('cartData');
    if (cartData) {
        try {
            const cart = JSON.parse(cartData);
            if (Array.isArray(cart.items) && cart.items.length > 0) {
                const subtotal = cart.summary?.subtotal ?? cart.items.reduce((sum, item) => sum + ((item.prix || 0) * (item.quantite || 1)), 0);
                const discount = cart.summary?.discount ?? subtotal * 0.05;
                const shipping = cart.summary?.shipping ?? (cart.items.some(item => item.physical) ? 4.90 : 0);
                const serviceFee = cart.summary?.serviceFee ?? subtotal * 0.015;
                const total = cart.summary?.total ?? (subtotal - discount + shipping + serviceFee);

                return {
                    produit: 'Panier AsArt\'sDev',
                    prix: Number((subtotal / Math.max(cart.items.reduce((q, item) => q + (item.quantite || 1), 0), 1)).toFixed(2)),
                    quantite: cart.items.reduce((sum, item) => sum + (item.quantite || 1), 0),
                    nom: '',
                    email: '',
                    items: cart.items,
                    subtotal: Number(subtotal.toFixed(2)),
                    discount: Number(discount.toFixed(2)),
                    shipping: Number(shipping.toFixed(2)),
                    serviceFee: Number(serviceFee.toFixed(2)),
                    total: Number(total.toFixed(2))
                };
            }
        } catch (error) {
            console.error('Panier invalide:', error);
        }
    }

    // Récupérer depuis localStorage
    const orderData = localStorage.getItem('orderData');
    if (orderData) {
        const parsed = JSON.parse(orderData);
        return {
            ...parsed,
            subtotal: parsed.subtotal ?? (parsed.prix * parsed.quantite),
            discount: parsed.remise ?? ((parsed.prix * parsed.quantite) * 0.05),
            shipping: parsed.frais_envoi ?? 0,
            serviceFee: parsed.frais_service ?? 0,
            total: parsed.total ?? ((parsed.prix * parsed.quantite) - ((parsed.prix * parsed.quantite) * 0.05))
        };
    }
    
    // Ou depuis les paramètres URL
    const urlParams = new URLSearchParams(window.location.search);
    return {
        produit: urlParams.get('produit') || 'Max et Milla - version finale enfant',
        prix: parseFloat(urlParams.get('prix')) || 15.00,
        quantite: parseInt(urlParams.get('quantite')) || 1,
        nom: urlParams.get('nom') || '',
        email: urlParams.get('email') || ''
    };
}

// Afficher le récapitulatif de la commande
function displayOrderSummary() {
    const orderDetails = getOrderDetails();
    const orderDetailsDiv = document.getElementById('order-details');
    const totalAmountDiv = document.getElementById('total-amount');
    const subtotalAmountDiv = document.getElementById('subtotal-amount');
    const discountAmountDiv = document.getElementById('discount-amount');
    const emailInput = document.getElementById('email');
    
    // Pré-remplir l'email si disponible
    if (orderDetails.email) {
        emailInput.value = orderDetails.email;
    }
    
    // Calculer sous-total, remise, frais et total final
    const subtotal = Number(orderDetails.subtotal ?? (orderDetails.prix * orderDetails.quantite));
    const discount = Number(orderDetails.discount ?? (subtotal * 0.05));
    const shipping = Number(orderDetails.shipping ?? 0);
    const serviceFee = Number(orderDetails.serviceFee ?? 0);
    const total = Number(orderDetails.total ?? (subtotal - discount + shipping + serviceFee));
    
    // Afficher les détails
    let itemsHtml = '';
    if (Array.isArray(orderDetails.items) && orderDetails.items.length > 0) {
        itemsHtml = orderDetails.items.map((item) => {
            const lineTotal = (item.prix || 0) * (item.quantite || 1);
            return `
                <div class="order-item">
                    <div class="item-info">
                        <h3>${item.produit}</h3>
                        <p>Quantité : ${item.quantite}</p>
                        <p>Prix unitaire : ${(item.prix || 0).toFixed(2)} €</p>
                    </div>
                    <div class="item-price">${lineTotal.toFixed(2)} €</div>
                </div>
            `;
        }).join('');
    } else {
        itemsHtml = `
            <div class="order-item">
                <div class="item-info">
                    <h3>${orderDetails.produit}</h3>
                    <p>Quantité : ${orderDetails.quantite}</p>
                    <p>Prix unitaire : ${Number(orderDetails.prix).toFixed(2)} €</p>
                </div>
                <div class="item-price">${subtotal.toFixed(2)} €</div>
            </div>
        `;
    }

    orderDetailsDiv.innerHTML = `
        ${itemsHtml}
        <div class="order-item" style="opacity:0.9; border-top:1px solid rgba(0,0,0,0.08); margin-top:8px; padding-top:8px;">
            <div class="item-info"><p>Frais d'envoi</p></div>
            <div class="item-price">${shipping.toFixed(2)} €</div>
        </div>
        <div class="order-item" style="opacity:0.9;">
            <div class="item-info"><p>Frais de service</p></div>
            <div class="item-price">${serviceFee.toFixed(2)} €</div>
        </div>
    `;
    
    if (subtotalAmountDiv) subtotalAmountDiv.textContent = `${subtotal.toFixed(2)} €`;
    if (discountAmountDiv) discountAmountDiv.textContent = `-${discount.toFixed(2)} €`;
    totalAmountDiv.textContent = `${total.toFixed(2)} €`;
    
    return {
        ...orderDetails,
        subtotal: parseFloat(subtotal.toFixed(2)),
        discount: parseFloat(discount.toFixed(2)),
        shipping: parseFloat(shipping.toFixed(2)),
        serviceFee: parseFloat(serviceFee.toFixed(2)),
        total: parseFloat(total.toFixed(2))
    };
}

function bindPaymentForm() {
    const form = document.getElementById('payment-form');
    if (!form) {
        throw new Error('Formulaire #payment-form manquant');
    }

    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        setLoading(true);

        const cardholderName = document.getElementById('cardholder-name').value;
        const email = document.getElementById('email').value;

        try {
            const response = await fetch('api/create-payment-intent.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    amount: Math.round(orderInfo.total * 100),
                    currency: 'eur',
                    description: `Commande: ${orderInfo.produit}`,
                    metadata: {
                        produit: orderInfo.produit,
                        quantite: orderInfo.quantite,
                        email: email,
                        nom: cardholderName
                    }
                }),
            });

            const { clientSecret, error: backendError } = await response.json();

            if (backendError) {
                showMessage(backendError, 'error');
                setLoading(false);
                return;
            }

            const { error: stripeError, paymentIntent } = await stripe.confirmCardPayment(
                clientSecret,
                {
                    payment_method: {
                        card: cardElement,
                        billing_details: {
                            name: cardholderName,
                            email: email
                        }
                    }
                }
            );

            if (stripeError) {
                showMessage(stripeError.message, 'error');
                setLoading(false);
            } else if (paymentIntent.status === 'succeeded') {
                showMessage('Paiement effectue avec succes ! Redirection...', 'success');

                localStorage.setItem('paymentSuccess', JSON.stringify({
                    paymentIntentId: paymentIntent.id,
                    amount: orderInfo.total,
                    produit: orderInfo.produit,
                    email: email,
                    date: new Date().toISOString()
                }));

                setTimeout(() => {
                    window.location.href = 'confirmation-paiement.html';
                }, 2000);
            }

        } catch (error) {
            console.error('Erreur:', error);
            showMessage('Une erreur est survenue lors du traitement du paiement.', 'error');
            setLoading(false);
        }
    });
}

// Afficher un message
function showMessage(messageText, type = 'error') {
    const messageDiv = document.getElementById('payment-message');
    messageDiv.textContent = messageText;
    messageDiv.classList.remove('hidden', 'success', 'error');
    messageDiv.classList.add(type);
}

// Gérer l'état de chargement
function setLoading(isLoading) {
    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const spinner = document.getElementById('spinner');
    
    if (isLoading) {
        submitButton.disabled = true;
        buttonText.textContent = 'Traitement en cours...';
        spinner.classList.remove('hidden');
    } else {
        submitButton.disabled = false;
        buttonText.textContent = 'Valider l\'achat';
        spinner.classList.add('hidden');
    }
}

// Sécurité : effacer les données sensibles au déchargement de la page
window.addEventListener('beforeunload', function() {
    if (cardElement) {
        cardElement.clear();
    }
});

async function initPaymentPage() {
    try {
        if (typeof Stripe === 'undefined') {
            throw new Error('Stripe.js non charge.');
        }

        const publicKey = await fetchStripePublicKey();
        stripe = Stripe(publicKey);

        orderInfo = displayOrderSummary();
        mountStripeCardElement();
        bindPaymentForm();
    } catch (error) {
        showGlobalError(error.message || 'Configuration paiement indisponible.');
        disablePaymentForm();
        console.error(error);
    }
}

initPaymentPage();


