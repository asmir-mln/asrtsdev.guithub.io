/* AsArt'sDev | Signature invisible | ASmir Milia | ASARTSDEV_SIGNATURE_INVISIBLE */
// Script de paiement avec Stripe pour AsArt'sDev

// ⚠️ IMPORTANT : Remplacez cette clé par votre clé publique Stripe
const STRIPE_PUBLIC_KEY = 'pk_test_VOTRE_CLE_PUBLIQUE_STRIPE';

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

if (typeof Stripe === 'undefined') {
    showGlobalError('Stripe.js n\'est pas chargé. Vérifiez votre connexion et le script Stripe.');
    disablePaymentForm();
    throw new Error('Stripe.js manquant');
}

if (!STRIPE_PUBLIC_KEY || STRIPE_PUBLIC_KEY.includes('VOTRE_CLE')) {
    showGlobalError('Clé publique Stripe non configurée. Mettez à jour STRIPE_PUBLIC_KEY dans paiement.js.');
    disablePaymentForm();
    throw new Error('Clé Stripe publique non configurée');
}

// Initialiser Stripe
const stripe = Stripe(STRIPE_PUBLIC_KEY);
const elements = stripe.elements();

// Créer l'élément de carte
const cardElement = elements.create('card', {
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

// Monter l'élément de carte dans le DOM
if (document.getElementById('card-element')) {
    cardElement.mount('#card-element');
} else {
    showGlobalError('Élément de carte introuvable (#card-element).');
    disablePaymentForm();
    throw new Error('Container #card-element manquant');
}

// Gérer les erreurs de saisie en temps réel
cardElement.on('change', function(event) {
    const displayError = document.getElementById('card-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
});

// Récupérer les détails de la commande depuis localStorage ou URL
function getOrderDetails() {
    // Récupérer depuis localStorage
    const orderData = localStorage.getItem('orderData');
    if (orderData) {
        return JSON.parse(orderData);
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
    
    // Calculer sous-total, remise et total final
    const subtotal = orderDetails.prix * orderDetails.quantite;
    const discount = subtotal * 0.05;
    const total = subtotal - discount;
    
    // Afficher les détails
    orderDetailsDiv.innerHTML = `
        <div class="order-item">
            <div class="item-info">
                <h3>${orderDetails.produit}</h3>
                <p>Quantité : ${orderDetails.quantite}</p>
                <p>Prix unitaire : ${orderDetails.prix.toFixed(2)} €</p>
            </div>
            <div class="item-price">${subtotal.toFixed(2)} €</div>
        </div>
    `;
    
    if (subtotalAmountDiv) subtotalAmountDiv.textContent = `${subtotal.toFixed(2)} €`;
    if (discountAmountDiv) discountAmountDiv.textContent = `-${discount.toFixed(2)} €`;
    totalAmountDiv.textContent = `${total.toFixed(2)} €`;
    
    return {
        ...orderDetails,
        subtotal: parseFloat(subtotal.toFixed(2)),
        discount: parseFloat(discount.toFixed(2)),
        total: parseFloat(total.toFixed(2))
    };
}

// Afficher le récapitulatif au chargement de la page
const orderInfo = displayOrderSummary();

// Gérer la soumission du formulaire
const form = document.getElementById('payment-form');
if (!form) {
    showGlobalError('Formulaire de paiement introuvable (#payment-form).');
    throw new Error('Formulaire #payment-form manquant');
}

form.addEventListener('submit', async function(event) {
    event.preventDefault();
    
    setLoading(true);
    
    const cardholderName = document.getElementById('cardholder-name').value;
    const email = document.getElementById('email').value;
    
    try {
        // 1. Créer un Payment Intent côté serveur
        const response = await fetch('api/create-payment-intent.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                amount: Math.round(orderInfo.total * 100), // Montant en centimes
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
        
        // 2. Confirmer le paiement avec Stripe
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
            // Paiement réussi !
            showMessage('Paiement effectué avec succès ! Redirection...', 'success');
            
            // Sauvegarder les infos de la commande
            localStorage.setItem('paymentSuccess', JSON.stringify({
                paymentIntentId: paymentIntent.id,
                amount: orderInfo.total,
                produit: orderInfo.produit,
                email: email,
                date: new Date().toISOString()
            }));
            
            // Rediriger vers la page de confirmation
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
    cardElement.clear();
});


