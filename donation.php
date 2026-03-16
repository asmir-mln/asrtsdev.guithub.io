<?php
/* AsArt'sDev | Signature invisible | ASmir Milia | ASARTSDEV_SIGNATURE_INVISIBLE */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Livres & Donations - AsArt'sDev</title>
    <!-- AsArt'sDev | Signature invisible | ASmir Milia | ASARTSDEV_SIGNATURE_INVISIBLE -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="logo-style.css">
    <style>
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }
        .book-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            text-align: center;
        }
        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        .book-card h3 {
            color: #2c3e50;
            margin: 15px 0;
            font-size: 1.5em;
        }
        .book-card p {
            color: #555;
            line-height: 1.6;
            margin: 15px 0;
        }
        .book-price {
            font-size: 1.8em;
            color: #e74c3c;
            font-weight: bold;
            margin: 20px 0;
        }
        .btn-buy {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
            margin: 10px 5px;
            border: none;
            cursor: pointer;
            font-size: 1em;
        }
        .btn-buy:hover {
            background: #2980b9;
        }
        .btn-library {
            display: block;
            background: #27ae60;
            color: white;
            padding: 15px 40px;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            margin: 40px auto;
            font-weight: bold;
            max-width: 400px;
            transition: background 0.3s;
            font-size: 1.1em;
        }
        .btn-library:hover {
            background: #229954;
        }
        .library-link-section {
            background: #ecf0f1;
            padding: 40px;
            border-radius: 12px;
            margin: 40px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Logo AsArt'sDev -->
    <a href="index.html" class="asartsdev-logo" title="Retour à l'accueil AsArt'sDev" aria-label="AsArt'sDev, retour à l'accueil du site">
        <div class="asartsdev-logo-icon">
            <span>A</span>
        </div>
        <div class="asartsdev-logo-text">
            <span class="asartsdev-logo-brand">AsArt'sDev</span>
            <span class="asartsdev-logo-tagline">Éditions & Créations</span>
        </div>
    </a>

    <header>
        <h1>Nos Livres & Donations</h1>
        <p>Explorez nos trois univers littéraires — Code, Section, Édition & Récits</p>
    </header>

    <main>
        <!-- Présentation des trois livres -->
        <section class="books-section">
            <h2>Nos Trois Univers</h2>
            
            <div class="books-grid">
                <!-- Livre 1 : Code -->
                <div class="book-card">
                    <h3>📚 CODE</h3>
                    <p>Plongez dans l'essence de la programmation et de l'innovation technologique. Un guide complet pour les développeurs en quête d'excellence.</p>
                    <div class="book-price">À partir de 19,99 €</div>
                    <button class="btn-buy" onclick="scrollToDonation('code')">Acheter</button>
                </div>

                <!-- Livre 2 : Section -->
                <div class="book-card">
                    <h3>📖 SECTION</h3>
                    <p>Les sections de nos réflexions, une exploration des thèmes majeurs : accessibilité, innovation inclusive et transformation sociale.</p>
                    <div class="book-price">À partir de 24,99 €</div>
                    <button class="btn-buy" onclick="scrollToDonation('section')">Acheter</button>
                </div>

                <!-- Livre 3 : Édition & Récit -->
                <div class="book-card">
                    <h3>✨ ÉDITION & RÉCIT</h3>
                    <p>Les trois vies d'Asmir — Un récit autobiographique authentique racontant un parcours d'autodidacte dyslexique devenu technologue.</p>
                    <div class="book-price">39,99 €</div>
                    <button class="btn-buy" onclick="scrollToDonation('recit')">Acheter</button>
                </div>
            </div>
        </section>

        <!-- Lien vers la Bibliothèque 3D -->
        <div class="library-link-section">
            <h2>🏛️ Explorez Notre Bibliothèque 3D</h2>
            <p>Découvrez tous nos titres et créations dans un environnement immersif en 3 dimensions</p>
            <a href="bibliotheque.html" class="btn-library">Visiter la Bibliothèque 3D</a>
        </div>

        <!-- Formulaire de donation et achat -->
        <section id="donation-form">
            <h2>Formulaire de Donation / Achat</h2>
            <form action="mailto:asartdev.contact@gmail.com" method="POST" enctype="text/plain">
                <div class="form-group">
                    <label for="type-demande">Type de demande *</label>
                    <select id="type-demande" name="type_demande" required>
                        <option value="">-- Sélectionnez --</option>
                        <option value="don">Don de soutien</option>
                        <option value="achat">Achat de livre</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="produit">Produit concerné</label>
                    <select id="produit" name="produit">
                        <option value="">-- Don libre --</option>
                        <option value="code">CODE (19,99 € + frais)</option>
                        <option value="section">SECTION (24,99 € + frais)</option>
                        <option value="recit">ÉDITION & RÉCIT — Les trois vies d'Asmir (39,99 € + frais)</option>
                        <option value="max-milla">Max et Milla - version enfant (15,00 € précommande)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="montant">Montant (€) *</label>
                    <input type="number" id="montant" name="montant" min="1" step="0.01" required placeholder="Ex: 19.99, 24.99, 39.99 ou don libre">
                </div>

                <div class="form-group">
                    <label for="raison">Message / Raison de votre demande *</label>
                    <textarea id="raison" name="raison" rows="4" required placeholder="Ex: Achat CODE, don de soutien, etc..."></textarea>
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

