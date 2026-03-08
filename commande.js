/* AsArt'sDev | Signature invisible | ASmir Milia | ASARTSDEV_SIGNATURE_INVISIBLE */
// Script pour la page de commande
// AsArt'sDev

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('#formulaire form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Récupérer les données du formulaire
            const formData = new FormData(form);
            const produitSelect = document.getElementById('produit');
            const produitText = produitSelect.options[produitSelect.selectedIndex].text;
            
            // Extraire le prix du texte (ex: "Les Aventures des Animaux (9,99 €)")
            const priceMatch = produitText.match(/\((\d+,\d+)\s*€\)/);
            const prix = priceMatch ? parseFloat(priceMatch[1].replace(',', '.')) : 0;
            
            const quantite = parseInt(formData.get('quantite')) || 1;
            
            // Préparer les données de commande
            const orderData = {
                produit: produitText.replace(/\s*\([^)]*\)/, ''), // Enlever le prix du nom
                prix: prix,
                quantite: quantite,
                nom: formData.get('nom'),
                email: formData.get('email'),
                telephone: formData.get('telephone'),
                message: formData.get('message')
            };
            
            // Sauvegarder dans localStorage
            localStorage.setItem('orderData', JSON.stringify(orderData));
            
            // Rediriger vers la page de paiement
            if (prix > 0) {
                window.location.href = 'paiement.html';
            } else {
                // Pour les créations personnalisées (devis), envoyer par email
                alert('Merci pour votre demande de devis. Nous vous contacterons rapidement.');
                // TODO: Envoyer le formulaire par email
            }
        });
    }
});


