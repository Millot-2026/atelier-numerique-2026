<?php
/**
 * Page 2 — Synthèse des Projets de l'Atelier Nomade (Version Flipbook Page Unique StPageFlip)
 * Générée le : 03/08/2026
 * Basée sur le rapport d'analyse de la clé USB (F:\_www)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Synthèse des Projets — Atelier Nomade (Flipbook)</title>
    <link rel="stylesheet" href="static/fonts/fontawesome/css/all.min.css">
    <!-- Intégration de la bibliothèque StPageFlip -->
    <script src="https://cdn.jsdelivr.net/npm/page-flip@2.0.7/dist/js/page-flip.browser.js"></script>
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #38bdf8;
            --accent-hover: #0ea5e9;
            --sheet-bg: #fdfbf7;
            --sheet-text: #2b2b2b;
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .main-wrapper {
            max-width: 650px;
            width: 100%;
            margin: 0 auto;
        }

        .nav-back {
            display: inline-block;
            margin-bottom: 20px;
            font-family: -apple-system, sans-serif;
            font-size: 0.8rem;
            text-transform: uppercase;
            text-decoration: none;
            background: var(--card-bg);
            color: var(--accent);
            padding: 8px 14px;
            border-radius: 4px;
            border: 1px solid rgba(56, 189, 248, 0.3);
            transition: all 0.2s;
        }
        .nav-back:hover {
            background: var(--accent);
            color: var(--bg-color);
        }

        /* Conteneur principal du Flipbook StPageFlip en mode unitaire */
        #book-container {
            width: 100%;
            max-width: 600px;
            height: 650px;
            margin: 0 auto;
            box-shadow: 0 15px 35px rgba(0,0,0,0.6);
            display: none; /* Affiché via JS après chargement pour éviter le flash */
        }

        /* Style individuel des pages du livre */
        .book-page {
            background-color: var(--sheet-bg);
            color: var(--sheet-text);
            padding: 35px;
            box-sizing: border-box;
            font-family: Georgia, "Times New Roman", serif;
            overflow-y: auto;
            height: 100%;
            border: 1px solid #dcd7ce;
        }

        .page-header {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .page-header h1 {
            font-size: 1.6rem;
            text-transform: uppercase;
            margin: 0 0 6px 0;
            letter-spacing: -0.5px;
        }

        .page-header p {
            font-family: -apple-system, sans-serif;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #555;
            margin: 0;
        }

        h2 {
            font-family: -apple-system, sans-serif;
            font-size: 1.1rem;
            border-bottom: 2px solid #2b2b2b;
            padding-bottom: 4px;
            margin-top: 20px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #c0392b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-family: -apple-system, sans-serif;
            font-size: 0.78rem;
        }

        th, td {
            border: 1px solid #dcd7ce;
            padding: 7px 10px;
            text-align: left;
        }

        th {
            background-color: #f1f3f5;
            color: #2b2b2b;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background-color: #f8f6f0;
        }

        .project-card {
            background: #fff;
            border: 1px solid #e2ddd5;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .project-card h3 {
            font-family: -apple-system, sans-serif;
            font-size: 1rem;
            margin-top: 0;
            color: #2b2b2b;
            border-bottom: 1px solid #eee;
            padding-bottom: 6px;
        }

        .project-card p {
            font-size: 0.88rem;
            line-height: 1.4;
            color: #4a4a4a;
            margin: 6px 0;
            text-align: justify;
        }

        .project-meta {
            font-family: -apple-system, sans-serif;
            font-size: 0.7rem;
            color: #666;
            background: #fdfbf7;
            padding: 5px 8px;
            border-left: 3px solid #c0392b;
            margin-top: 8px;
        }

        .page-footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #333;
            text-align: center;
            font-family: -apple-system, sans-serif;
            font-size: 0.65rem;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Barre de contrôle / pagination en bas */
        .flip-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-top: 20px;
            font-family: -apple-system, sans-serif;
        }

        .flip-btn {
            background: var(--card-bg);
            color: var(--text-main);
            border: 1px solid rgba(56, 189, 248, 0.3);
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .flip-btn:hover {
            background: var(--accent);
            color: var(--bg-color);
        }

        #page-indicator {
            font-size: 0.9rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <a href="index.php" class="nav-back"><i class="fas fa-arrow-left"></i> Retour au tableau de bord</a>

    <!-- Conteneur global du Flipbook -->
    <div id="book-container">

        <!-- PAGE 1 : En-tête + Vue d'ensemble (Partie 1) -->
        <div class="book-page">
            <div class="page-header">
                <h1>Synthèse des Projets</h1>
                <p>Rapport d'analyse de la clé USB — Généré le 03/08/2026</p>
            </div>
            <h2>Vue d'ensemble (1/2)</h2>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Dossier</th>
                        <th>Nom du projet</th>
                        <th>Type</th>
                        <th>Technologie</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>1</td><td><code>index.php</code></td><td>Tableau de bord central</td><td>Hub</td><td>PHP</td></tr>
                    <tr><td>2</td><td><code>dashboard-designer</code></td><td>WORKSTATION Dashboard</td><td>Application</td><td>PHP + JS + CSS</td></tr>
                    <tr><td>3</td><td><code>cms-2026-v8-full</code></td><td>CMS 2026 — Études de cas</td><td>CMS éditorial</td><td>PHP (flat-file)</td></tr>
                    <tr><td>4</td><td><code>modulor</code></td><td>Modulor Workstation</td><td>Éditeur visuel</td><td>PHP + JSON</td></tr>
                    <tr><td>5</td><td><code>personator-v1.2</code></td><td>Personator v1.2</td><td>Outil UX</td><td>PHP + JSON</td></tr>
                    <tr><td>6</td><td><code>skeletor-v1.0</code></td><td>Skeletor v1.0</td><td>Scaffolding</td><td>PHP + JSON</td></tr>
                    <tr><td>7</td><td><code>skeletor-v1.0-o2switch</code></td><td>Skeletor o2switch</td><td>Déploiement</td><td>PHP + JSON + BAT</td></tr>
                </tbody>
            </table>
            <div class="page-footer">Atelier Nomade • Page 1 / 4</div>
        </div>

        <!-- PAGE 2 : Vue d'ensemble (Partie 2) -->
        <div class="book-page">
            <div class="page-header">
                <h1>Synthèse des Projets</h1>
                <p>Suite de la vue d'ensemble</p>
            </div>
            <h2>Vue d'ensemble (2/2)</h2>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Dossier</th>
                        <th>Nom du projet</th>
                        <th>Type</th>
                        <th>Technologie</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>8</td><td><code>texturor</code></td><td>Texturor</td><td>Outil design</td><td>HTML + JS + CSS</td></tr>
                    <tr><td>9</td><td><code>user_journey-v1.0</code></td><td>User Journey v1.0</td><td>Outil UX</td><td>PHP + JS + JSON</td></tr>
                    <tr><td>10</td><td><code>wordpress-portable</code></td><td>WordPress Portable</td><td>CMS</td><td>PHP + WP + MySQL</td></tr>
                    <tr><td>11</td><td><code>mon-site</code></td><td>Mon Site</td><td>Gabarit</td><td>PHP + CSS</td></tr>
                    <tr><td>12</td><td><code>mon-premier-site</code></td><td>Mon Premier Site</td><td>Gabarit</td><td>PHP</td></tr>
                    <tr><td>13</td><td><code>projet-client</code></td><td>Projet Client</td><td>Gabarit client</td><td>PHP</td></tr>
                </tbody>
            </table>
            <div class="page-footer">Atelier Nomade • Page 2 / 4</div>
        </div>

        <!-- PAGE 3 : Fiches détaillées (Partie 1) -->
        <div class="book-page">
            <div class="page-header">
                <h1>Fiches Détaillées</h1>
                <p>Analyses des cœurs de l'atelier</p>
            </div>
            <h2>Fiches Techniques (1/2)</h2>

            <div class="project-card">
                <h3>1. Tableau de bord central (<code>index.php</code>)</h3>
                <p><strong>Objectif :</strong> Page d'accueil centrale du serveur local. Elle scanne automatiquement le dossier <code>_www</code> et liste tous les projets sous forme de cartes cliquables avec gestion des statuts.</p>
                <div class="project-meta">Technologie : PHP, JSON (statuses.json), HTML/CSS/JS</div>
            </div>

            <div class="project-card">
                <h3>2. WORKSTATION Dashboard (<code>dashboard-designer</code>)</h3>
                <p><strong>Objectif :</strong> Cockpit personnel agrégeant une série de widgets au quotidien (horloge, météo, chronomètre, convertisseur px/rem, éditeur CodePen, blocs-notes et lanceur).</p>
                <div class="project-meta">Technologie : PHP, JavaScript (LocalStorage), Grille 3 Tiers CSS</div>
            </div>
            <div class="page-footer">Atelier Nomade • Page 3 / 4</div>
        </div>

        <!-- PAGE 4 : Fiches détaillées (Partie 2) -->
        <div class="book-page">
            <div class="page-header">
                <h1>Fiches Détaillées</h1>
                <p>Suite et conclusion</p>
            </div>
            <h2>Fiches Techniques (2/2)</h2>

            <div class="project-card">
                <h3>3. CMS 2026 (<code>cms-2026-v8-full</code>)</h3>
                <p><strong>Objectif :</strong> Interface d'administration flat-file pour la gestion de projets éditoriaux. Permet l'édition locale et génère un livrable statique prêt à être déployé sur hébergement mutualisé.</p>
                <div class="project-meta">Technologie : PHP flat-file, Export statique Nuxit</div>
            </div>

            <div class="project-card">
                <h3>4. Modulor Workstation (<code>modulor</code>)</h3>
                <p><strong>Objectif :</strong> Environnement de composition visuelle par blocs modulaires (1, 2 ou 3 colonnes) avec un Skin Engine intégré permettant de basculer instantanément entre 6 thèmes graphiques.</p>
                <div class="project-meta">Technologie : PHP, JSON, Skin Engine multi-thèmes</div>
            </div>

            <div class="page-footer">Atelier Nomade • Christophe Millot • 2026 • Page 4 / 4</div>
        </div>

    </div>

    <!-- Contrôles de navigation manuels pour le Flipbook -->
    <div class="flip-controls">
        <button class="flip-btn" id="prev-btn"><i class="fas fa-chevron-left"></i> Précédent</button>
        <span id="page-indicator">Page 1 sur 4</span>
        <button class="flip-btn" id="next-btn">Suivant <i class="fas fa-chevron-right"></i></button>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const bookElement = document.getElementById('book-container');
        
        // Initialisation avec size: "fixed" et usePortrait: true pour forcer strictement une seule page visible
        const pageFlip = new St.PageFlip(bookElement, {
            width: 550,
            height: 620,
            size: "fixed",
            usePortrait: true,
            showCover: false,
            mobileScrollSupport: true,
            drawShadow: true,
            flippingTime: 500
        });

        // Charger les éléments HTML correspondant aux classes .book-page
        pageFlip.loadFromHTML(document.querySelectorAll('.book-page'));

        // Rendre visible le conteneur une fois initialisé
        bookElement.style.display = 'block';

        // Gestion des boutons de contrôle externes
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const pageIndicator = document.getElementById('page-indicator');

        function updateIndicator() {
            const currentPage = pageFlip.getCurrentPageIndex() + 1;
            const totalPages = pageFlip.getPageCount();
            pageIndicator.innerText = `Page ${currentPage} sur ${totalPages}`;
        }

        prevBtn.addEventListener('click', () => {
            pageFlip.flipPrev();
        });

        nextBtn.addEventListener('click', () => {
            pageFlip.flipNext();
        });

        // Mise à jour de l'indicateur lors des changements de page
        pageFlip.on('flip', (e) => {
            updateIndicator();
        });

        updateIndicator();
    });
</script>

</body>
</html>