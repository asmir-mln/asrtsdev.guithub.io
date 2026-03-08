/**
 * AsArt'sDev Orders Client JS
 * Intégration facile de l'API commandes
 */

class AsartsdevOrders {
  constructor(apiUrl = '/api/orders.php') {
    this.apiUrl = apiUrl;
  }

  /**
   * Créer une nouvelle commande
   */
  async createOrder(orderData) {
    try {
      const response = await fetch(this.apiUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(orderData)
      });

      const result = await response.json();
      return result;
    } catch (error) {
      console.error('Erreur création commande:', error);
      return { success: false, error: error.message };
    }
  }

  /**
   * Afficher modale de confirmation
   */
  showConfirmation(order) {
    const modal = document.createElement('div');
    modal.style.cssText = `
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.8);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    `;

    modal.innerHTML = `
      <div style="
        background: white;
        padding: 40px;
        border-radius: 15px;
        max-width: 500px;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0,0,0,0.5);
      ">
        <h2 style="color: #4CAF50; margin-bottom: 20px;">✅ Commande enregistrée !</h2>
        <p style="font-size: 1.1rem; margin-bottom: 10px;">
          <strong>Numéro de commande :</strong><br>
          <span style="color: #667eea; font-size: 1.3rem;">${order.id}</span>
        </p>
        <div style="background: #f5f5f5; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: left;">
          <p style="margin: 10px 0;"><strong>💳 Paiement :</strong></p>
          <p style="font-size: 0.95rem; line-height: 1.6;">
            Vous allez recevoir un email avec :<br>
            • Coordonnées bancaires complètes (virement)<br>
            • Adresse postale (chèque)<br>
            • Montant exact à payer
          </p>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" style="
          background: #667eea;
          color: white;
          border: none;
          padding: 12px 30px;
          border-radius: 8px;
          font-size: 1rem;
          cursor: pointer;
        ">Fermer</button>
      </div>
    `;

    document.body.appendChild(modal);
  }

  /**
   * Intercepter un formulaire existant
   */
  attachToForm(formSelector) {
    const form = document.querySelector(formSelector);
    
    if (!form) {
      console.error('Formulaire non trouvé:', formSelector);
      return;
    }

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const formData = new FormData(form);
      const orderData = Object.fromEntries(formData);
      
      // Afficher loader
      const submitBtn = form.querySelector('[type="submit"]');
      const originalText = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.textContent = '⏳ Envoi en cours...';
      
      const result = await this.createOrder(orderData);
      
      // Restaurer bouton
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
      
      if (result.success) {
        this.showConfirmation(result.order);
        form.reset();
      } else {
        alert('❌ Erreur : ' + (result.error || 'Commande non enregistrée'));
      }
    });
  }
}

// Export global
window.AsartsdevOrders = AsartsdevOrders;
