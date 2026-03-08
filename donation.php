<?php
/* AsArt'sDev | Signature invisible | ASmir Milia | ASARTSDEV_SIGNATURE_INVISIBLE */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Don / Achat sécurisé - AsArt'sDev</title>
    <!-- AsArt'sDev | Signature invisible | ASmir Milia | ASARTSDEV_SIGNATURE_INVISIBLE -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Formulaire Don / Achat</h1>
        <p>AsArt'sDev — Samir Milianni (pseudonyme: Asmir mln)</p>
    </header>

    <main>
        <section>
            <h2>Choisissez votre demande</h2>
            <form action="mailto:asartdev.contact@gmail.com" method="POST" enctype="text/plain">
                <div class="form-group">
                    <label for="type-demande">Type de demande *</label>
                    <select id="type-demande" name="type_demande" required>
                        <option value="">-- Sélectionnez --</option>
                        <option value="don">Don</option>
                        <option value="achat">Achat</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="produit">Produit concerné</label>
                    <select id="produit" name="produit">
                        <option value="">-- Aucun / Don libre --</option>
                        <option value="max-milla">Max et Milla - version finale enfant (précommande 15 € + devis offert)</option>
                        <option value="autobiographie">Les trois vies d'Asmir — La fin du cycle des dix ans (39,99 €)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="montant">Montant (€)</label>
                    <input type="number" id="montant" name="montant" min="1" step="0.01" placeholder="Ex: 15.00 ou don libre">
                </div>

                <div class="form-group">
                    <label for="raison">Raison / message *</label>
                    <textarea id="raison" name="raison" rows="4" required placeholder="Ex: Don de soutien, achat précommande, commande autobiographie..."></textarea>
                </div>

                <h3>Coordonnées d'envoi (impression + expédition)</h3>

                <div class="form-group">
                    <label for="nom">Nom complet *</label>
                    <input type="text" id="nom" name="nom" required>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone">
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse complète *</label>
                    <textarea id="adresse" name="adresse" rows="3" required placeholder="Numéro, rue, code postal, ville, pays"></textarea>
                </div>

                <div class="form-group checkbox-group">
                    <input type="checkbox" id="accept-cgv" name="accept_cgv" required>
                    <label for="accept-cgv">J'accepte les <a href="LivresAsArtsDev/cgv.html" target="_blank">Conditions Générales de Vente</a> *</label>
                </div>

                <button type="submit" class="btn-primary">Envoyer mon formulaire</button>
            </form>
        </section>

        <section class="info-paiement">
            <h2>Paiement par virement (informations floutées)</h2>
            <p>Pour votre sécurité, les données bancaires sensibles sont partiellement masquées sur le site public.</p>
            <ul>
                <li><strong>Bénéficiaire :</strong> Samir Milianni</li>
                <li><strong>IBAN (masqué) :</strong> FR76 **** **** **** **** **** 122</li>
                <li><strong>BIC :</strong> REVOFRP2</li>
                <li><strong>Banque :</strong> Revolut Bank UAB, 10 avenue Kléber, 75116 Paris, France</li>
                <li><strong>Banque correspondante :</strong> CHASDEFX</li>
            </ul>
            <p>Après réception du formulaire, les informations complètes de paiement sont confirmées par email selon votre demande (don ou achat).</p>
        </section>

        <section>
            <h2>Informations produits</h2>
            <p><strong>Produit 1 :</strong> <em>Max et Milla - version finale enfant</em> (histoire vraie, précommande 15 € + prix de devis offert).</p>
            <p><strong>Produit 2 :</strong> <em>Biographie des Asmir mln — La fin du cycle des 10 ans</em> (version imprimée et envoi sur formulaire).</p>
            <p><strong>Autobiographie adulte :</strong> <em>Les trois vies d'Asmir — La fin du cycle des dix ans</em> — 39,99 €.</p>
            <p><strong>Note :</strong> version en évolution, manuscrit en cours de finalisation.</p>
            <p>Tous les dons, quel que soit le montant, sont les bienvenus pour soutenir les livres. Une formule dédicacée est proposée pour l'autobiographie adulte.</p>
        </section>

        <section>
            <h2>Visuel version adulte</h2>
            <p>Aperçu de la version adulte et de l'univers éditorial AsArt'sDev.</p>
            <object data="MemoLivreAutobiographique/pdf_version.pdf" type="application/pdf" style="width:100%; max-width:980px; height:560px; border-radius:10px; display:block; margin:10px auto 0 auto;"></object>
            <p style="text-align:center; margin-top:10px;"><a href="MemoLivreAutobiographique/pdf_version.pdf" target="_blank">Ouvrir la version adulte (PDF)</a></p>
        </section>
    </main>

    <footer>
        <nav>
            <a href="index.html">Accueil</a> |
            <a href="commande.html">Commander</a> |
            <a href="LivresAsArtsDev/cgv.html">CGV</a> |
            <a href=\"contact.html\">Contact</a> |
            <a href=\"tel:0781586882\">📱 0781586882</a> |
            <a href=\"mailto:asartdev.contact@gmail.com\">📧 asartdev.contact@gmail.com</a>
        </nav>
        <p>© 2026 AsArt'sDev – créé par ASmir Milia. Tous droits réservés.</p>
    </footer>
</body>
</html>

