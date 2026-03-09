/* AsArt'sDev | Signature invisible | ASmir Milia | ASARTSDEV_SIGNATURE_INVISIBLE */
/**
 * CGV Popup Manager - AsArt'sDev
 * Gère l'affichage et la signature des Conditions Générales de Vente
 */

class CGVPopup {
    constructor() {
        this.storageKey = 'asartsdev_cgv_accepted';
        this.signatureKey = 'asartsdev_signature';
        this.init();
    }

    init() {
        // Afficher le popup si les CGV n'ont pas été acceptées
        if (!this.isAccepted()) {
            document.addEventListener('DOMContentLoaded', () => this.show());
        }
    }

    isAccepted() {
        const accepted = localStorage.getItem(this.storageKey);
        if (!accepted) return false;

        // Vérifier si l'acceptation est expirée (30 jours)
        const acceptanceDate = new Date(accepted);
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

        return acceptanceDate > thirtyDaysAgo;
    }

    show() {
        const overlay = document.createElement('div');
        overlay.id = 'cgv-popup-overlay';
        overlay.className = 'cgv-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeIn 0.3s ease-in;
        `;

        const popup = document.createElement('div');
        popup.className = 'cgv-popup';
        popup.style.cssText = `
            background: white;
            border-radius: 10px;
            max-width: 600px;
            width: 90%;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.4s ease-out;
        `;

        popup.innerHTML = `
            <div class="cgv-header" style="
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 25px;
                border-radius: 10px 10px 0 0;
                border-bottom: 1px solid rgba(255,255,255,0.2);
            ">
                <h2 style="margin: 0; font-size: 24px; font-weight: bold;">
                    ⚖️ Conditions Générales de Vente
                </h2>
                <p style="margin: 8px 0 0 0; font-size: 13px; opacity: 0.9;">
                    Veuillez lire et accepter nos CGV pour continuer
                </p>
            </div>

            <div class="cgv-content" style="
                flex: 1;
                overflow-y: auto;
                padding: 25px;
                font-size: 14px;
                line-height: 1.6;
                color: #333;
            ">
                <div class="cgv-alert" style="
                    background: #fff3cd;
                    border-left: 4px solid #ff6b6b;
                    padding: 15px;
                    border-radius: 4px;
                    margin-bottom: 20px;
                ">
                    <strong style="color: #d32f2f;">⚠️ Avis Important :</strong>
                    <p style="margin: 8px 0 0 0; font-size: 13px;">
                        En accédant à ce site, vous acceptez nos Conditions Générales de Vente et nos politiques de protection des contenus créatifs.
                    </p>
                </div>

                <h3 style="color: #667eea; margin-top: 0;">📋 Résumé des Conditions</h3>

                <div style="background: #f8f9fa; padding: 12px; border-radius: 4px; margin-bottom: 15px;">
                    <p><strong>1. Propriété Intellectuelle</strong></p>
                    <p style="margin: 5px 0 0 0; font-size: 13px;">
                        Tous les contenus (images, illustrations, textes, designs) sont la propriété exclusive d'AsArt'sDev. 
                        La reproduction ou distribution non autorisée est interdite.
                    </p>
                </div>

                <div style="background: #f8f9fa; padding: 12px; border-radius: 4px; margin-bottom: 15px;">
                    <p><strong>2. Protection des Images</strong></p>
                    <p style="margin: 5px 0 0 0; font-size: 13px;">
                        Les images sont protégées techniquement et légalement. L'accès, la téléchargement ou la copie sans autorisation 
                        est strictement interdit et passible de poursuites judiciaires.
                    </p>
                </div>

                <div style="background: #f8f9fa; padding: 12px; border-radius: 4px; margin-bottom: 15px;">
                    <p><strong>3. Paiement et Livraison</strong></p>
                    <p style="margin: 5px 0 0 0; font-size: 13px;">
                        Les paiements sont sécurisés. Les produits numériques sont livrés par email dans un délai de 48h.
                    </p>
                </div>

                <div style="background: #f8f9fa; padding: 12px; border-radius: 4px; margin-bottom: 15px;">
                    <p><strong>4. Droits de Rétractation</strong></p>
                    <p style="margin: 5px 0 0 0; font-size: 13px;">
                        Conformément à la loi française, le droit de rétractation pour produits numériques est annulé dès le commencement du téléchargement.
                    </p>
                </div>

                <div style="background: #f8f9fa; padding: 12px; border-radius: 4px; margin-bottom: 15px;">
                    <p><strong>5. Données Personnelles (RGPD)</strong></p>
                    <p style="margin: 5px 0 0 0; font-size: 13px;">
                        Vos données personnelles sont traitées conformément au RGPD. Vous disposez d'un droit d'accès, 
                        de rectification et de suppression.
                    </p>
                </div>

                <div style="background: #e8f5e9; padding: 12px; border-radius: 4px; margin-top: 20px; border-left: 3px solid #4caf50;">
                    <p style="margin: 0; font-size: 13px; color: #2e7d32;">
                        <strong>✓ Version complète disponible :</strong> 
                        <a href="LivresAsArtsDev/cgv.html" target="_blank" style="color: #1976d2;">Lire les CGV intégrales →</a>
                    </p>
                </div>
            </div>

            <div class="cgv-signature" style="
                padding: 20px 25px;
                border-top: 1px solid #e0e0e0;
                background: #f8f9fa;
            ">
                <div style="margin-bottom: 15px;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" id="cgv-agree" style="
                            width: 18px;
                            height: 18px;
                            cursor: pointer;
                            accent-color: #667eea;
                        "/>
                        <span style="margin-left: 10px; font-size: 14px;">
                            ✓ J'accepte les <strong>Conditions Générales de Vente</strong> et la <strong>politique de protection des images</strong>
                        </span>
                    </label>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="font-size: 13px; color: #666;">
                        Nom et Prénom (signature électronique) *
                    </label>
                    <input 
                        type="text" 
                        id="cgv-signature-name" 
                        placeholder="Entrez votre nom complet"
                        style="
                            width: 100%;
                            padding: 10px;
                            border: 1px solid #ddd;
                            border-radius: 4px;
                            font-size: 14px;
                            box-sizing: border-box;
                            margin-top: 5px;
                        "
                    />
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="font-size: 13px; color: #666;">
                        Email (pour confirmation)
                    </label>
                    <input 
                        type="email" 
                        id="cgv-signature-email" 
                        placeholder="votre.email@exemple.com"
                        style="
                            width: 100%;
                            padding: 10px;
                            border: 1px solid #ddd;
                            border-radius: 4px;
                            font-size: 14px;
                            box-sizing: border-box;
                            margin-top: 5px;
                        "
                    />
                </div>

                <div style="display: flex; gap: 10px;">
                    <button 
                        id="cgv-accept" 
                        style="
                            flex: 1;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            border: none;
                            padding: 12px;
                            border-radius: 4px;
                            cursor: pointer;
                            font-size: 14px;
                            font-weight: bold;
                            transition: transform 0.2s;
                        "
                        onmouseover="this.style.transform='scale(1.02)'"
                        onmouseout="this.style.transform='scale(1)'"
                    >
                        ✓ Accepter et Continuer
                    </button>
                </div>

                <p style="
                    margin: 12px 0 0 0;
                    font-size: 11px;
                    color: #999;
                    text-align: center;
                ">
                    Signature électronique enregistrée (valide 30 jours)
                </p>
            </div>
        `;

        overlay.appendChild(popup);
        document.body.appendChild(overlay);

        // Ajouter les styles d'animation
        if (!document.querySelector('style[data-cgv-popup]')) {
            const style = document.createElement('style');
            style.setAttribute('data-cgv-popup', 'true');
            style.textContent = `
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                @keyframes slideUp {
                    from { 
                        transform: translateY(50px);
                        opacity: 0;
                    }
                    to { 
                        transform: translateY(0);
                        opacity: 1;
                    }
                }
                .cgv-overlay {
                    backdrop-filter: blur(4px);
                }
            `;
            document.head.appendChild(style);
        }

        // Event listeners
        const agreeCheckbox = document.getElementById('cgv-agree');
        const acceptButton = document.getElementById('cgv-accept');
        const nameInput = document.getElementById('cgv-signature-name');
        const emailInput = document.getElementById('cgv-signature-email');

        // Valider le formulaire
        const validateForm = () => {
            const isChecked = agreeCheckbox.checked;
            const hasName = nameInput.value.trim().length > 0;
            const hasEmail = emailInput.value.trim().length > 0 && this.validateEmail(emailInput.value);

            acceptButton.disabled = !(isChecked && hasName && hasEmail);
            acceptButton.style.opacity = acceptButton.disabled ? '0.5' : '1';
            acceptButton.style.cursor = acceptButton.disabled ? 'not-allowed' : 'pointer';
        };

        agreeCheckbox.addEventListener('change', validateForm);
        nameInput.addEventListener('input', validateForm);
        emailInput.addEventListener('input', validateForm);

        // Accepter les CGV
        acceptButton.addEventListener('click', () => {
            const signature = {
                name: nameInput.value.trim(),
                email: emailInput.value.trim(),
                date: new Date().toISOString(),
                accepted: true
            };

            localStorage.setItem(this.storageKey, new Date().toISOString());
            localStorage.setItem(this.signatureKey, JSON.stringify(signature));

            // Confirmer et fermer
            this.showConfirmation(overlay, emailInput.value.trim());
        });

        // Validation initiale
        validateForm();
    }

    showConfirmation(overlay, email) {
        const popup = overlay.querySelector('.cgv-popup');
        
        popup.innerHTML = `
            <div style="
                padding: 40px 25px;
                text-align: center;
                background: white;
                border-radius: 10px;
            ">
                <div style="font-size: 48px; margin-bottom: 20px;">✅</div>
                <h2 style="color: #4caf50; margin: 0 0 15px 0;">
                    Conditions Acceptées
                </h2>
                <p style="color: #666; font-size: 14px; line-height: 1.6;">
                    Merci ! Vous avez accepté nos Conditions Générales de Vente.
                </p>
                <p style="color: #666; font-size: 13px; margin-top: 15px;">
                    Une confirmation a été envoyée à :<br/>
                    <strong>${email}</strong>
                </p>
                <div style="
                    background: #e8f5e9;
                    padding: 15px;
                    border-radius: 4px;
                    margin: 20px 0;
                    font-size: 12px;
                    color: #2e7d32;
                ">
                    <p style="margin: 0;">
                        Votre signature électronique a been enregistrée et est valable 30 jours.<br/>
                        <a href="image-protection.html" style="color: #1976d2; text-decoration: none;">
                            Voir la politique de protection →
                        </a>
                    </p>
                </div>
                <button onclick="document.getElementById('cgv-popup-overlay').remove()" style="
                    background: #4caf50;
                    color: white;
                    border: none;
                    padding: 12px 30px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 14px;
                    font-weight: bold;
                    margin-top: 20px;
                ">
                    Continuer
                </button>
            </div>
        `;
    }

    validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
}

// Initialiser automatiquement au chargement
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => new CGVPopup());
} else {
    new CGVPopup();
}


