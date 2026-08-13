<?php
/* ============================================================
   PALETTOR — Page de présentation complète du projet
   ============================================================ */
$slug       = 'palettor';
$title      = 'palettor';
$subtitle   = 'Générateur et gestionnaire de palettes de couleurs pour le design web';
$statusKey  = 'operational';
$technos    = ['JavaScript', 'CSS Custom Properties', 'Canvas API', 'JSON'];
$screenshot = 'images/capture-palettor.png'; // Remplacerez l'image au besoin
$appHref    = 'palettor/';
$indexHref  = '../index.php';
$detailExt  = 'detail.php';

$menuProjects = [
    'col1' => [
        ['workstation',      'Workstation'],
        ['la-centrale',      'la-centrale'],
        ['cms-2026-v8-full', 'cms-2026-v8-full'],
    ],
    'col2' => [
        ['palettor',          'palettor'],
        ['modulor',           'modulor'],
        ['texturor',          'texturor'],
        ['personator-v1.2',   'personator-v1.2'],
        ['pixelart',          'pixelart'],
        ['user_journey-v1.0', 'user_journey-v1.0'],
    ],
    'col3' => [
        ['skeletor-v1.0',     'skeletor-v1.0'],
        ['wordpress-portable', 'wordpress-portable'],
    ],
];

function detailLink($targetSlug, $currentSlug, $basePath, $detailExt) {
    if ($targetSlug === $currentSlug) return '#';
    return $basePath . rawurlencode($targetSlug) . '/' . $detailExt;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>palettor — L'Atelier Numérique</title>
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #38bdf8;
            --accent-hover: #0ea5e9;
            --red: #ef4444;
            --orange: #f59e0b;
            --green: #22c55e;
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            padding: 40px;
        }

        .sticky-header-bar {
            position: fixed; top: 0; left: 0; width: 100%;
            background-color: #fdfbf7; border-bottom: 2px solid #2b2b2b;
            padding: 10px 30px; box-sizing: border-box;
            display: flex; justify-content: space-between; align-items: center;
            z-index: 99999; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transform: translateY(-100%); transition: transform 0.3s ease-in-out;
        }
        .sticky-header-bar.visible { transform: translateY(0); }
        .sticky-header-brand { font-family: Georgia, serif; font-size: 1.1rem; font-weight: 900; text-transform: uppercase; color: #2b2b2b; letter-spacing: -0.5px; }
        .sticky-header-brand a { color: inherit; text-decoration: none; }
        .sticky-header-hamburger { cursor: pointer; display: flex; align-items: center; background: #2b2b2b; color: #fff; padding: 6px 10px; border-radius: 6px; }

        .news-sheet {
            background-color: #fdfbf7; color: #2b2b2b;
            border: 2px solid #2b2b2b; border-radius: 4px;
            padding: 35px; font-family: Georgia, "Times New Roman", serif;
            margin-bottom: 40px; position: relative;
        }

        .news-header-divider { border: none; border-top: 3px double #333333 !important; margin: 15px 0 0 0; }
        .news-header-wrapper { position: relative; }

        .journal-mega-menu {
            display: none; position: absolute; top: 100%; left: 0; right: 0;
            background: #FDFBF7 !important; border: 2px solid #2b2b2b; border-top: none;
            padding: 25px; box-shadow: 0 15px 35px rgba(0,0,0,0.3); z-index: 999;
            font-family: -apple-system, sans-serif; box-sizing: border-box;
        }
        .journal-mega-menu.active { display: block; }
        body.is-scrolled .news-header-wrapper .journal-mega-menu.active {
            position: fixed !important; top: 48px !important; left: 30px !important; right: 30px !important;
            max-width: none !important; box-sizing: border-box !important;
        }
        .mega-menu-close-btn { display: none; }
        .mega-menu-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .mega-menu-col h4 { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; color: #c0392b; border-bottom: 1px solid #2b2b2b; padding-bottom: 6px; margin-top: 0; margin-bottom: 12px; }
        .mega-menu-col ul { list-style: none; margin: 0; padding: 0; }
        .mega-menu-col li { margin-bottom: 8px; }
        .mega-menu-col a { color: #2b2b2b; text-decoration: none; font-size: 0.9rem; font-weight: 500; display: block; padding: 6px 0 6px 12px; transition: color 0.15s ease, background-color 0.15s ease; }
        .mega-menu-col a:hover { color: #ffffff !important; background-color: #555555 !important; text-decoration: none !important; padding-left: 12px !important; padding-right: 8px; border-radius: 4px; }
        .mega-menu-col a.current-page { font-weight: 800; color: #c0392b !important; background: #fef0ee; border-radius: 4px; }
        .mega-menu-col a.current-page::before { content: '▶ '; font-size: 0.65rem; }

        .news-bandeau { text-align: center; font-family: -apple-system, sans-serif; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 3px; border-bottom: 1px solid #333333; padding-bottom: 8px; margin-bottom: 15px; font-weight: 700; color: #4a4a4a; }
        .news-header-grid { display: grid; grid-template-columns: 180px 1fr 180px; align-items: center; padding-bottom: 15px; text-align: center; }
        .news-ear { font-family: -apple-system, sans-serif; font-size: 0.75rem; color: #555555; line-height: 1.3; text-transform: uppercase; letter-spacing: 1px; }
        .news-manchette { font-size: 2.2rem; font-weight: 900; letter-spacing: -0.5px; text-transform: uppercase; color: #2b2b2b; margin: 0; line-height: 1.1; font-family: Georgia, serif; }
        .news-manchette a { color: inherit; text-decoration: none; }
        .news-manchette a:hover { color: #c0392b; }

        .detail-breadcrumb { font-family: -apple-system, sans-serif; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #777; padding: 12px 0 0 0; border-top: 1px solid #e2ddd5; margin-top: 15px; }
        .detail-breadcrumb a { color: #555; text-decoration: none; }
        .detail-breadcrumb a:hover { color: #c0392b; }
        .detail-breadcrumb span { color: #c0392b; font-weight: 700; }

        .detail-layout { display: grid; grid-template-columns: 1fr 300px; gap: 30px; margin-top: 25px; }

        .detail-title-block { border-bottom: 2px solid #111; padding-bottom: 10px; margin-bottom: 18px; }
        .detail-project-label { font-family: -apple-system, sans-serif; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; color: #c0392b; font-weight: 700; margin-bottom: 4px; }
        .detail-h1 { font-size: 2rem; font-weight: 900; color: #2b2b2b; margin: 0 0 6px 0; line-height: 1.1; }
        .detail-subtitle { font-size: 1rem; font-style: italic; color: #555; margin: 0; }

        .detail-meta { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }
        .badge { font-family: -apple-system, sans-serif; font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 3px; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-operational { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .badge-tech { font-family: -apple-system, sans-serif; font-size: 0.68rem; font-weight: 600; background: #f1f5f9; border: 1px solid #cbd5e1; color: #334155; padding: 2px 7px; border-radius: 3px; }

        .detail-pitch::first-letter { font-size: 2.6rem; float: left; line-height: 0.82; padding-right: 7px; font-weight: bold; color: #c0392b; font-family: Georgia, serif; }
        .detail-pitch { font-size: 0.95rem; line-height: 1.65; text-align: justify; color: #333; margin: 0 0 22px 0; }

        .press-figure { margin: 0 0 20px 0; background: #fdfbf7; border: 1px solid #e2ddd5; padding: 8px; box-sizing: border-box; }
        .press-figure img { width: 100%; height: auto; max-height: 260px; object-fit: contain; background: #f8fafc; display: block; border: 1px solid #dcd7ce; }
        .press-caption { font-family: -apple-system, sans-serif; font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px; color: #666; margin-top: 6px; font-weight: bold; text-align: center; }

        .detail-section { border-top: 1px solid #e2ddd5; padding-top: 18px; margin-top: 18px; }
        .detail-section h3 { font-family: -apple-system, sans-serif; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1.5px; color: #c0392b; margin: 0 0 10px 0; font-weight: 700; }
        .detail-section p { font-size: 0.9rem; line-height: 1.65; text-align: justify; color: #333; margin: 0 0 12px 0; }
        .detail-section ul { font-size: 0.9rem; color: #333; line-height: 1.7; padding-left: 18px; margin: 0 0 12px 0; }

        .sidebar-block { background: #fff; border: 1px solid #e2ddd5; padding: 16px; margin-bottom: 20px; }
        .sidebar-block h5 { font-family: -apple-system, sans-serif; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1.5px; color: #c0392b; margin: 0 0 10px 0; padding-bottom: 6px; border-bottom: 1px solid #e2ddd5; font-weight: 800; }
        .sidebar-block p { font-size: 0.85rem; line-height: 1.5; color: #444; margin: 0 0 8px 0; }
        .sidebar-block p:last-child { margin-bottom: 0; }
        .sidebar-block strong { color: #2b2b2b; }

        .detail-cta { display: block; text-align: center; background: #2b2b2b; color: #fdfbf7; text-decoration: none; padding: 12px 20px; font-family: -apple-system, sans-serif; font-size: 0.78rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; border-radius: 3px; transition: background 0.2s; margin-top: 8px; }
        .detail-cta:hover { background: #c0392b; }

        .news-footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #333333; font-family: -apple-system, sans-serif; font-size: 0.75rem; color: #555555; display: flex; justify-content: space-between; text-transform: uppercase; letter-spacing: 1px; }

        @media (max-width: 900px) { .detail-layout { grid-template-columns: 1fr; } }
        @media (max-width: 768px) {
            body { padding: 0 !important; }
            .news-sheet { padding: 15px !important; margin: 0 !important; width: 100% !important; border-radius: 0 !important; }
            .news-header-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        }
    </style>
</head>
<body>

    <div class="sticky-header-bar" id="sticky-header">
        <div class="sticky-header-brand"><a href="../index.php">L'Atelier Numérique</a></div>
        <div class="sticky-header-hamburger" id="sticky-hamburger-btn" title="Menu">
            <span>Menu</span>
        </div>
    </div>

    <div class="news-sheet">
        <div class="news-bandeau">
            Chronique Indépendante • Édition Spéciale Nomadisme Numérique • 2026
        </div>

        <div class="news-header-wrapper">
            <div class="news-header-grid" id="original-header-grid">
                <div class="news-ear" style="text-align: left;">
                    <strong>SUPPORT :</strong> Clé USB F:\<br>
                    <strong>SERVEUR :</strong> XAMPP Portable
                </div>
                <div>
                    <h2 class="news-manchette"><a href="../index.php">L'Atelier Numérique</a></h2>
                    <div style="font-size: 0.85rem; letter-spacing: 2px; text-transform: uppercase; margin-top: 6px; font-weight: bold; color: #444;">Christophe Millot</div>
                </div>
                <div class="news-ear" style="display: flex; flex-direction: column; align-items: flex-end; justify-content: center; gap: 4px; text-align: right;">
                    <div>
                        <strong>ARCHITECTURES :</strong> Flat-File<br>
                        <strong>STATUT :</strong> Opérationnel
                    </div>
                    <div id="hamburger-menu-btn" style="cursor: pointer; display: flex; align-items: center;" title="Menu">
                        <svg width="22" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </div>
                </div>
            </div>

            <hr class="news-header-divider">

            <div id="journal-mega-menu" class="journal-mega-menu">
                <div class="mega-menu-close-btn">
                    <button type="button" id="mega-menu-close">Fermer</button>
                </div>
                <div class="mega-menu-grid">
                    <div class="mega-menu-col">
                        <h4>Pilotage &amp; Structure</h4>
                        <ul>
                            <?php foreach ($menuProjects['col1'] as [$pSlug, $pLabel]): ?>
                                <li><a href="<?php echo htmlspecialchars(detailLink($pSlug, $slug, '../', 'detail.php'), ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($pSlug === $slug) ? 'class="current-page"' : ''; ?>><?php echo htmlspecialchars($pLabel, ENT_QUOTES, 'UTF-8'); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="mega-menu-col">
                        <h4>Outils Créatifs &amp; Design</h4>
                        <ul>
                            <?php foreach ($menuProjects['col2'] as [$pSlug, $pLabel]): ?>
                                <li><a href="<?php echo htmlspecialchars(detailLink($pSlug, $slug, '../', 'detail.php'), ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($pSlug === $slug) ? 'class="current-page"' : ''; ?>><?php echo htmlspecialchars($pLabel, ENT_QUOTES, 'UTF-8'); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="mega-menu-col">
                        <h4>Templates &amp; Environnements</h4>
                        <ul>
                            <?php foreach ($menuProjects['col3'] as [$pSlug, $pLabel]): ?>
                                <li><a href="<?php echo htmlspecialchars(detailLink($pSlug, $slug, '../', 'detail.php'), ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($pSlug === $slug) ? 'class="current-page"' : ''; ?>><?php echo htmlspecialchars($pLabel, ENT_QUOTES, 'UTF-8'); ?></a></li>
                            <?php endforeach; ?>
                            <li><a href="#">À propos</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-breadcrumb">
            <a href="../index.php">Accueil</a> &rsaquo; <span>palettor</span>
        </div>

        <div class="detail-layout">
            <div class="detail-main">
                <div class="detail-title-block">
                    <div class="detail-project-label">Dossier Projet — L'Atelier Numérique</div>
                    <h1 class="detail-h1">palettor</h1>
                    <p class="detail-subtitle">Générateur et gestionnaire de palettes de couleurs pour le design web</p>
                </div>

                <div class="detail-meta">
                    <span class="badge badge-operational">🟢 Opérationnel</span>
                    <span class="badge-tech">JavaScript</span>
                    <span class="badge-tech">CSS Custom Properties</span>
                    <span class="badge-tech">Canvas API</span>
                    <span class="badge-tech">JSON</span>
                </div>

                <figure class="press-figure">
                    <img src="../images/capture-palettor.png" alt="Interface Palettor">
                    <figcaption class="press-caption">Fig. 1 — Interface principale de Palettor</figcaption>
                </figure>

                <p class="detail-pitch">Palettor est né d'un constat simple : les outils de gestion de palettes disponibles en ligne sont trop lourds, trop connectés, trop dépendants d'un abonnement. Ce générateur de palettes autonome permet de créer, éditer, exporter et appliquer des harmonies colorées directement depuis le navigateur, sans aucune connexion réseau, avec une précision professionnelle.</p>

                <div class="detail-section">
                    <h3>Génération de Palettes et Harmonies</h3>
                    <p>L'outil propose plusieurs modes de génération harmonique : complémentaire, triadique, tétradique, analogique et monochrome. Chaque palette est définie par un triplet HSL, permettant un contrôle précis de la teinte, de la saturation et de la luminosité sans passer par des codes hexadécimaux cryptiques.</p>
                    <ul>
                        <li>Modes harmoniques : complémentaire, triade, tétrade, analogique, monochrome</li>
                        <li>Prévisualisation temps réel sur des composants UI types</li>
                        <li>Export en formats CSS Custom Properties, SCSS et JSON</li>
                    </ul>
                </div>

                <div class="detail-section">
                    <h3>Intégration avec l'Atelier</h3>
                    <p>Palettor s'intègre nativement avec les autres outils de l'atelier via un format JSON commun. Les palettes créées peuvent être directement importées dans Skeletor pour la génération de thèmes CSS, ou dans Texturor pour l'harmonie typographique.</p>
                </div>

                <div class="detail-section" style="border-top: 2px solid #111; text-align: center; padding-top: 22px; margin-top: 24px;">
                    <a href="../palettor/" class="detail-cta" target="_blank">🚀 Lancer Palettor</a>
                    <p style="font-family: -apple-system, sans-serif; font-size: 0.7rem; color: #aaa; margin-top: 8px; text-transform: uppercase; letter-spacing: 1px;">
                        &larr; <a href="../index.php" style="color: #555; text-decoration: none;">Retour au Journal</a>
                    </p>
                </div>
            </div>

            <div class="detail-sidebar">
                <div class="sidebar-block">
                    <h5>📋 Fiche Technique</h5>
                    <p><strong>Projet :</strong> palettor</p>
                    <p><strong>Statut :</strong> Opérationnel</p>
                    <p><strong>Technologies :</strong> JavaScript, CSS Custom Properties, Canvas API, JSON</p>
                    <p><strong>Architecture :</strong> Flat-File / PHP Vanille</p>
                    <p><strong>Environnement :</strong> XAMPP Portable · Clé USB F:\</p>
                </div>

                <div class="sidebar-block">
                    <h5>📰 L'Atelier Numérique</h5>
                    <p>Journal de bord d'un développeur nomade. Chaque projet est une station de travail autonome, pensée pour fonctionner sans cloud et sans dépendance réseau.</p>
                    <p><strong>Rédacteur :</strong> Christophe Millot</p>
                </div>
            </div>
        </div>

        <div class="news-footer">
            <span>&copy; 2026 Christophe Millot &bull; Tous droits réservés</span>
            <span>&bull;</span>
            <span>L'Atelier Numérique &mdash; palettor</span>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const hamburgerBtn = document.getElementById('hamburger-menu-btn');
            const stickyBtn = document.getElementById('sticky-hamburger-btn');
            const megaMenu = document.getElementById('journal-mega-menu');
            const stickyHeader = document.getElementById('sticky-header');

            if (stickyHeader) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 120) {
                        stickyHeader.classList.add('visible');
                        document.body.classList.add('is-scrolled');
                    } else {
                        stickyHeader.classList.remove('visible');
                        document.body.classList.remove('is-scrolled');
                        if (megaMenu) megaMenu.classList.remove('active');
                    }
                });
            }

            function toggleMenu(e) {
                if (e) e.stopPropagation();
                megaMenu.classList.toggle('active');
            }

            if (hamburgerBtn) hamburgerBtn.addEventListener('click', toggleMenu);
            if (stickyBtn) stickyBtn.addEventListener('click', toggleMenu);
        });
    </script>
</body>
</html>