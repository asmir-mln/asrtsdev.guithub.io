/**
 * Formulaire de Partenariat et Soutien AsArt'sDev
 * Formulaire modal réutilisable pour les demandes de partenariat
 */

(function() {
    // Créer le HTML du formulaire modal
    const formHTML = `
    <div id="partnershipModal" class="partnership-modal">
        <div class="partnership-modal-content">
            <span class="partnership-close">&times;</span>
            <h2>🤝 Formulaire de Partenariat</h2>
            <form id="partnershipForm" method="POST" action="https://formspree.io/f/xyzabc123">
                <div class="form-group">
                    <label for="partnerName">Nom complet / Organisme *</label>
                    <input type="text" id="partnerName" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="partnerEmail">Email *</label>
                    <input type="email" id="partnerEmail" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="partnerPhone">Téléphone</label>
                    <input type="tel" id="partnerPhone" name="phone">
                </div>
                
                <div class="form-group">
                    <label for="partnerType">Type de partenariat *</label>
                    <select id="partnerType" name="partnership_type" required>
                        <option value="">-- Sélectionner --</option>
                        <option value="investisseur_prive">Investisseur privé</option>
                        <option value="investisseur_public">Partenaire public</option>
                        <option value="fournisseur">Fournisseur technologique</option>
                        <option value="mecenat">Mécénat / Soutien associatif</option>
                        <option value="academique">Partenaire académique</option>
                        <option value="international">Partenaire international</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="partnerMessage">Message / Propositions *</label>
                    <textarea id="partnerMessage" name="message" rows="5" placeholder="Décrivez votre intérêt et vos propositions..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="confidentialite" required>
                        J'accepte la politique de confidentialité
                    </label>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-submit">Envoyer ma demande</button>
                    <p style="margin-top: 10px; font-size: 12px; color: #666;">
                        Nous reviendrons vers vous dans les 5 jours ouvrables.
                    </p>
                </div>
            </form>
        </div>
    </div>
    `;

    // Créer le CSS du formulaire
    const formCSS = `
    .partnership-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .partnership-modal-content {
        background-color: #fefefe;
        margin: 10% auto;
        padding: 40px;
        border-radius: 12px;
        width: 90%;
        max-width: 600px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        animation: slideDown 0.3s ease;
    }
    
    @keyframes slideDown {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .partnership-modal-content h2 {
        color: #d32f2f;
        margin-bottom: 25px;
        font-size: 24px;
    }
    
    .partnership-close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        line-height: 20px;
    }
    
    .partnership-close:hover {
        color: #000;
    }
    
    .form-group {
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
    }
    
    .form-group label {
        margin-bottom: 5px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }
    
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="tel"],
    .form-group select,
    .form-group textarea {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-family: inherit;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }
    
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #d32f2f;
        box-shadow: 0 0 5px rgba(211,47,47,0.2);
    }
    
    .form-group label input[type="checkbox"] {
        margin-right: 8px;
        cursor: pointer;
    }
    
    .btn-submit {
        background-color: #d32f2f;
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        font-size: 16px;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .btn-submit:hover {
        background-color: #a82020;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(211,47,47,0.3);
    }
    
    .btn-submit:active {
        transform: translateY(0);
    }
    
    @media (max-width: 600px) {
        .partnership-modal-content {
            width: 95%;
            margin: 30% auto;
            padding: 20px;
        }
        
        .partnership-modal-content h2 {
            font-size: 20px;
            margin-bottom: 20px;
        }
    }
    `;

    // Initialiser au chargement du DOM
    document.addEventListener('DOMContentLoaded', function() {
        // Ajouter le HTML du formulaire
        document.body.insertAdjacentHTML('beforeend', formHTML);
        
        // Ajouter le CSS
        const styleTag = document.createElement('style');
        styleTag.textContent = formCSS;
        document.head.appendChild(styleTag);
        
        // Récupérer les références du modal
        const modal = document.getElementById('partnershipModal');
        const closeBtn = document.querySelector('.partnership-close');
        const form = document.getElementById('partnershipForm');
        
        // Ouvrir le modal quand on clique sur les boutons de partenariat
        const partnershipButtons = document.querySelectorAll('[data-open-partnership-form]');
        partnershipButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                modal.style.display = 'block';
            });
        });
        
        // Fermer le modal quand on clique sur X
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        // Fermer le modal quand on clique en dehors
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
        
        // Gerer l'envoi du formulaire
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('.btn-submit');
            submitBtn.textContent = 'Envoi en cours...';
            submitBtn.disabled = true;
            
            // Formspree gérera l'envoi
            // Redirection automatique après succès
            setTimeout(function() {
                if (form.action.includes('formspree')) {
                    // Laisser Formspree gérer
                    return;
                } else {
                    alert('Merci pour votre demande ! Nous vous recontacterons bientôt.');
                    modal.style.display = 'none';
                    form.reset();
                    submitBtn.textContent = 'Envoyer ma demande';
                    submitBtn.disabled = false;
                }
            }, 500);
        });
    });
})();
