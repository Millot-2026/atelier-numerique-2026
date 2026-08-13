<?php
/* ============================================================
   PARTIALS / PAGE-DETAIL.PHP — Template générique de détail
   Variables attendues (définies par le fichier appelant) :
   - $slug        (string)  : identifiant du projet, ex. 'la-centrale'
   - $title       (string)  : titre affiché
   - $subtitle    (string)  : sous-titre / accroche courte
   - $pitch       (string)  : paragraphe HTML de présentation (lettre ornée)
   - $technos     (array)   : liste de technologies, ex. ['PHP', 'JS']
   - $statusKey   (string)  : 'operational' | 'validated' | 'progress'
   - $screenshot  (string)  : chemin de l'image principale (relatif à la racine)
   - $sections    (array)   : [['title'=>'...','body'=>'...','figure'=>'...'], ...]
   - $isStatic    (bool)    : true → version HTML statique exportée
   - $basePath    (string)  : '../' depuis {slug}/detail.php, ou chemin statique
   - $appHref     (string)  : lien vers l'application réelle (CTA bas de page)
   ============================================================ */

// ---- Sécurité : variables avec valeurs par défaut ----
if (!isset($slug))       $slug       = '';
if (!isset($title))      $title      = 'Projet';
if (!isset($subtitle))   $subtitle   = '';
if (!isset($pitch))      $pitch      = '';
if (!isset($technos))    $technos    = [];
if (!isset($statusKey))  $statusKey  = 'operational';
if (!isset($screenshot)) $screenshot = '';
if (!isset($sections))   $sections   = [];
if (!isset($isStatic))   $isStatic   = false;
if (!isset($basePath))   $basePath   = '../';
if (!isset($appHref))    $appHref    = '';

// ---- Badge de statut ----
$badgeMap = [
    'validated'   => ['label' => '&#x1F7E2; Validé',       'class' => 'badge badge-validated'],
    'operational' => ['label' => '&#x1F7E0; Opérationnel', 'class' => 'badge badge-operational'],
    'progress'    => ['label' => '&#x1F534; En cours',      'class' => 'badge badge-progress'],
];
$badge = isset($badgeMap[$statusKey]) ? $badgeMap[$statusKey] : $badgeMap['operational'];

// ---- Extension selon le mode (local vs export statique) ----
$detailExt = $isStatic ? 'detail.html' : 'detail.php';

// ---- Méga-menu : liste exhaustive des projets ----
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
        ['skeletor-v1.0',      'skeletor-v1.0'],
        ['wordpress-portable', 'wordpress-portable'],
    ],
];

// ---- Chemins de navigation ----
$indexHref = $basePath . ($isStatic ? 'index.html' : 'index.php');

if (!function_exists('detailLink')) {
    function detailLink($targetSlug, $currentSlug, $basePath, $detailExt) {
        if ($targetSlug === $currentSlug) return '#';
        return $basePath . rawurlencode($targetSlug) . '/' . $detailExt;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?> — L'Atelier Numérique</title>
    <meta name="description" content="<?php echo htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8'); ?>">

    <style>
        /* =========================================================================
           CHARTE GRAPHIQUE — JOURNAL (identique à index.php)
           ========================================================================= */
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



/* Rend le lien Modulor vert dans le menu */
a[href*="modulor"] {
    color: #10b981 !important;
}





        body {
            font-family: system-ui, -apple-system, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            padding: 40px;
        }

        /* ---- STICKY HEADER (identique index.php) ---- */
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

        /* ---- JOURNAL SHEET ---- */
        .news-sheet {
            background-color: #fdfbf7; color: #2b2b2b;
            border: 2px solid #2b2b2b; border-radius: 4px;
            padding: 35px; font-family: Georgia, "Times New Roman", serif;
            margin-bottom: 40px; position: relative;
        }

        .news-header-divider { border: none; border-top: 3px double #333333 !important; margin: 15px 0 0 0; }
        .news-header-wrapper { position: relative; }

        /* ---- MEGA MENU (identique index.php) ---- */
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

        /* ---- BANDEAU & EN-TÊTE ---- */
        .news-bandeau { text-align: center; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 3px; border-bottom: 1px solid #333333; padding-bottom: 8px; margin-bottom: 15px; font-weight: 700; color: #4a4a4a; }
        .news-header-grid { display: grid; grid-template-columns: 180px 1fr 180px; align-items: center; padding-bottom: 15px; text-align: center; }
        .news-ear { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 0.75rem; color: #555555; line-height: 1.3; text-transform: uppercase; letter-spacing: 1px; }
        .news-manchette { font-size: 2.2rem; font-weight: 900; letter-spacing: -0.5px; text-transform: uppercase; color: #2b2b2b; margin: 0; line-height: 1.1; font-family: Georgia, serif; }
        .news-manchette a { color: inherit; text-decoration: none; }
        .news-manchette a:hover { color: #c0392b; }

        /* ---- BREADCRUMB ---- */
        .detail-breadcrumb { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #777; padding: 12px 0 0 0; border-top: 1px solid #e2ddd5; margin-top: 15px; }
        .detail-breadcrumb a { color: #555; text-decoration: none; }
        .detail-breadcrumb a:hover { color: #c0392b; }
        .detail-breadcrumb span { color: #c0392b; font-weight: 700; }

        /* ---- LAYOUT ARTICLE ---- */
        .detail-layout { display: grid; grid-template-columns: 1fr 300px; gap: 30px; margin-top: 25px; }

        /* ---- COLONNE PRINCIPALE ---- */
        .detail-title-block { border-bottom: 2px solid #111; padding-bottom: 10px; margin-bottom: 18px; }
        .detail-project-label { font-family: -apple-system, sans-serif; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; color: #c0392b; font-weight: 700; margin-bottom: 4px; }
        .detail-h1 { font-size: 2rem; font-weight: 900; color: #2b2b2b; margin: 0 0 6px 0; line-height: 1.1; }
        .detail-subtitle { font-size: 1rem; font-style: italic; color: #555; margin: 0; }

        /* Badges */
        .detail-meta { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }
        .badge { font-family: -apple-system, sans-serif; font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 3px; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-operational { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .badge-validated   { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .badge-progress    { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .badge-tech { font-family: -apple-system, sans-serif; font-size: 0.68rem; font-weight: 600; background: #f1f5f9; border: 1px solid #cbd5e1; color: #334155; padding: 2px 7px; border-radius: 3px; }

        /* Pitch avec lettre ornée */
        .detail-pitch::first-letter { font-size: 2.6rem; float: left; line-height: 0.82; padding-right: 7px; font-weight: bold; color: #c0392b; font-family: Georgia, serif; }
        .detail-pitch { font-size: 0.95rem; line-height: 1.65; text-align: justify; color: #333; margin: 0 0 22px 0; }

        /* Figures presse */
        .press-figure { margin: 0 0 20px 0; background: #fdfbf7; border: 1px solid #e2ddd5; padding: 8px; box-sizing: border-box; }
        .press-figure img { width: 100%; height: auto; max-height: 260px; object-fit: contain; background: #f8fafc; display: block; border: 1px solid #dcd7ce; }
        .press-caption { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px; color: #666; margin-top: 6px; font-weight: bold; text-align: center; }

        /* Sections */
        .detail-section { border-top: 1px solid #e2ddd5; padding-top: 18px; margin-top: 18px; }
        .detail-section h3 { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1.5px; color: #c0392b; margin: 0 0 10px 0; font-weight: 700; }
        .detail-section p { font-size: 0.9rem; line-height: 1.65; text-align: justify; color: #333; margin: 0 0 12px 0; }
        .detail-section ul { font-size: 0.9rem; color: #333; line-height: 1.7; padding-left: 18px; margin: 0 0 12px 0; }

        /* Sidebar */
        .sidebar-block { background: #fff; border: 1px solid #e2ddd5; padding: 16px; margin-bottom: 20px; }
        .sidebar-block h5 { font-family: -apple-system, sans-serif; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1.5px; color: #c0392b; margin: 0 0 10px 0; padding-bottom: 6px; border-bottom: 1px solid #e2ddd5; font-weight: 800; }
        .sidebar-block p { font-size: 0.85rem; line-height: 1.5; color: #444; margin: 0 0 8px 0; }
        .sidebar-block p:last-child { margin-bottom: 0; }
        .sidebar-block strong { color: #2b2b2b; }
        .detail-sidebar {
    position: sticky;
    top: 50px; /* Ajustez selon la hauteur de votre header */
    height: fit-content;
}

        /* CTA */
        .detail-cta { display: block; text-align: center; background: #2b2b2b; color: #fdfbf7; text-decoration: none; padding: 12px 20px; font-family: -apple-system, sans-serif; font-size: 0.78rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; border-radius: 3px; transition: background 0.2s; margin-top: 8px; }
        .detail-cta:hover { background: #c0392b; }
        .detail-cta-disabled { background: #e2ddd5; color: #999; cursor: default; }
        .detail-cta-disabled:hover { background: #e2ddd5; }

        /* Footer */
        .news-footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #333333; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 0.75rem; color: #555555; display: flex; justify-content: space-between; text-transform: uppercase; letter-spacing: 1px; }

        /* =========================================================================
           CONTROL PANEL — Volet latéral escamotable (identique à index.php)
           ========================================================================= */
        #cp-toggle-btn { position: fixed; bottom: 30px; right: 30px; z-index: 88888; width: 52px; height: 52px; border-radius: 50%; background: #2b2b2b; color: #fdfbf7; border: 2px solid #555; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 16px rgba(0,0,0,0.45); font-size: 1.2rem; transition: background 0.2s, transform 0.2s; }
        #cp-toggle-btn:hover { background: #c0392b; border-color: #c0392b; transform: scale(1.08); }
        #cp-toggle-btn.cp-open { background: #c0392b; border-color: #c0392b; }
        #cp-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.35); z-index: 88889; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; }
        #cp-backdrop.cp-visible { opacity: 1; pointer-events: auto; }
        #control-panel { position: fixed; top: 0; right: 0; width: 330px; max-width: 92vw; height: 100vh; background: #fdfbf7; border-left: 2px solid #2b2b2b; z-index: 88890; transform: translateX(100%); transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column; overflow: hidden; font-family: -apple-system, sans-serif; }
        #control-panel.cp-open { transform: translateX(0); box-shadow: -6px 0 30px rgba(0,0,0,0.3); }
        .cp-header { background: #2b2b2b; color: #fdfbf7; padding: 16px 18px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
        .cp-header-title { font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; }
        .cp-header-subtitle { font-size: 0.65rem; color: #aaa; text-transform: uppercase; letter-spacing: 1px; margin-top: 3px; }
        .cp-close-btn { background: none; border: 1px solid #555; color: #fff; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .cp-close-btn:hover { background: #c0392b; border-color: #c0392b; }
        .cp-actions { padding: 12px 18px; display: flex; gap: 8px; border-bottom: 1px solid #e2ddd5; background: #f5f0e8; flex-shrink: 0; }
        .cp-action-btn { flex: 1; padding: 8px 6px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; border-radius: 4px; cursor: pointer; border: 1px solid #2b2b2b; background: #fff; color: #2b2b2b; transition: background 0.2s, color 0.2s; }
        .cp-action-btn:hover { background: #2b2b2b; color: #fff; }
        .cp-action-btn.cp-btn-primary { background: #2b2b2b; color: #fff; }
        .cp-action-btn.cp-btn-primary:hover { background: #c0392b; border-color: #c0392b; }
        .cp-presets-section { padding: 12px 18px; background: #efe9df; border-bottom: 1px solid #e2ddd5; flex-shrink: 0; }
        .cp-presets-title { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: #c0392b; margin-bottom: 8px; }
        .cp-presets-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; }
        .cp-preset-box { background: #fff; border: 1px solid #cbd5e1; height: 36px; font-size: 0.95rem; font-weight: 800; border-radius: 4px; cursor: pointer; color: #2b2b2b; display: flex; align-items: center; justify-content: center; }
        .cp-preset-box.empty { color: #94a3b8; border-style: dashed; background: #f8fafc; font-size: 1.2rem; }
        .cp-preset-box:hover { background: #2b2b2b; color: #fff; border-color: #2b2b2b; }
        .cp-body { flex: 1; overflow-y: auto; padding: 14px 18px; }
        .cp-project-row { display: flex; align-items: center; gap: 8px; padding: 9px 0; border-bottom: 1px dashed #e2ddd5; }
        .cp-project-row:hover { background: #f5f0e8; padding-left: 4px; padding-right: 4px; }
        .cp-footer { padding: 12px 18px; border-top: 1px solid #e2ddd5; background: #f5f0e8; flex-shrink: 0; }
        .cp-apply-btn { width: 100%; background: #2b2b2b; color: #fdfbf7; border: 1px solid #2b2b2b; padding: 10px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; border-radius: 4px; cursor: pointer; text-align: center; text-decoration: none; display: block; box-sizing: border-box; }
        .cp-apply-btn:hover { background: #c0392b; border-color: #c0392b; }

        /* ---- RESPONSIVE ---- */
        @media (max-width: 900px) { .detail-layout { grid-template-columns: 1fr; } }
        @media (max-width: 768px) {
            body { padding: 0 !important; }
            .news-sheet { padding: 15px !important; margin: 0 !important; width: 100% !important; border-radius: 0 !important; border-left: none !important; border-right: none !important; box-sizing: border-box !important; }
            .news-header-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding-bottom: 15px; }
            .news-header-grid > div:nth-child(1) { grid-column: 1 / 2; text-align: left !important; }
            .news-header-grid > div:nth-child(2) { grid-column: 1 / -1; grid-row: 1; text-align: center !important; margin-bottom: 10px; }
            .news-header-grid > div:nth-child(3) { grid-column: 2 / 3; text-align: right !important; display: flex; flex-direction: column; align-items: flex-end !important; }
            .journal-mega-menu { position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; min-height: 100dvh !important; background: #fdfbf7 !important; z-index: 99999 !important; padding: 20px !important; box-sizing: border-box !important; overflow-y: auto !important; border: none !important; box-shadow: none !important; flex-direction: column !important; justify-content: flex-start !important; margin: 0 !important; }
            .journal-mega-menu.active { display: flex !important; }
            .mega-menu-close-btn { display: flex; justify-content: flex-end; margin-bottom: 15px; padding-bottom: 12px; border-bottom: 1px solid #e2ddd5; flex-shrink: 0; }
            .mega-menu-close-btn button { background: #2b2b2b; color: #fff; border: none; padding: 10px 18px; border-radius: 8px; font-size: 0.95rem; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 8px; }
            .mega-menu-grid { display: flex !important; flex-direction: column !important; flex: 1 !important; gap: 12px !important; }
            .mega-menu-col { background: #fdfbf7; border: 1px solid #e2ddd5; border-radius: 8px; overflow: hidden; margin-bottom: 0 !important; }
            .mega-menu-col.is-expanded { order: -1; display: flex !important; flex-direction: column !important; flex: 1 !important; }
            .mega-menu-col h4 { font-size: 1.15rem; font-weight: 800; margin: 0; padding: 15px; background: #fff; border-bottom: 1px solid #e2ddd5; cursor: pointer; display: flex; justify-content: space-between; align-items: center; user-select: none; flex-shrink: 0; }
            .mega-menu-col h4::after { content: '▼'; font-size: 0.75rem; color: #c0392b; transition: transform 0.25s ease; }
            .mega-menu-col.open h4::after { transform: rotate(180deg); }
            .mega-menu-col ul { display: none; padding: 0 !important; background: #fdfbf7; margin: 0 !important; }
            .mega-menu-col.open ul { display: flex !important; flex-direction: column !important; flex: 1 !important; justify-content: space-between !important; }
            .mega-menu-col li { margin-bottom: 0 !important; border-bottom: 1px dashed rgba(0,0,0,0.08); display: flex !important; align-items: stretch !important; flex: 1 !important; width: 100% !important; }
            .mega-menu-col li:last-child { border-bottom: none; }
            .mega-menu-col a { font-size: 1.1rem; font-weight: 700; padding: 0 20px; width: 100% !important; height: 100% !important; display: flex !important; align-items: center !important; text-decoration: none !important; color: #2b2b2b; background-color: transparent; box-sizing: border-box; }
        }
    </style>
</head>
<body>

    <!-- STICKY HEADER BAR -->
    <div class="sticky-header-bar" id="sticky-header">
        <div class="sticky-header-brand"><a href="<?php echo htmlspecialchars($indexHref, ENT_QUOTES, 'UTF-8'); ?>">L'Atelier Numérique</a></div>
        <div class="sticky-header-hamburger" id="sticky-hamburger-btn" title="Menu">
            <svg id="sticky-hamburger-icon-svg" width="20" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
            <span style="font-family: -apple-system, sans-serif; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; margin-left: 8px; font-weight: bold;">Menu</span>
        </div>
    </div>

    <div class="news-sheet">
        <!-- BANDEAU -->
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
                    <h2 class="news-manchette"><a href="<?php echo htmlspecialchars($indexHref, ENT_QUOTES, 'UTF-8'); ?>">L'Atelier Numérique</a></h2>
                    <div style="font-size: 0.85rem; letter-spacing: 2px; text-transform: uppercase; margin-top: 6px; font-weight: bold; color: #444;">Christophe Millot</div>
                </div>
                <div class="news-ear" style="display: flex; flex-direction: column; align-items: flex-end; justify-content: center; gap: 4px; text-align: right;">
                    <div>
                        <strong>ARCHITECTURES :</strong> Flat-File<br>
                        <strong>STATUT :</strong> Opérationnel
                    </div>
                    <div id="hamburger-menu-btn" style="cursor: pointer; display: flex; align-items: center;" title="Menu">
                        <svg id="hamburger-icon-svg" width="22" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </div>
                </div>
            </div>

            <hr class="news-header-divider">

            <!-- MEGA MENU — liens vers les pages de détail de chaque projet -->
            <div id="journal-mega-menu" class="journal-mega-menu">
                <div class="mega-menu-close-btn">
                    <button type="button" id="mega-menu-close">Fermer</button>
                </div>
                <div class="mega-menu-grid">
                    <!-- Colonne 1 : Pilotage & Structure -->
                    <div class="mega-menu-col">
                        <h4>Pilotage &amp; Structure</h4>
                        <ul>
                            <?php foreach ($menuProjects['col1'] as [$pSlug, $pLabel]): ?>
                                <li><a href="<?php echo htmlspecialchars(detailLink($pSlug, $slug, $basePath, $detailExt), ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($pSlug === $slug) ? 'class="current-page"' : ''; ?>><?php echo htmlspecialchars($pLabel, ENT_QUOTES, 'UTF-8'); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <!-- Colonne 2 : Outils Créatifs & Design -->
                    <div class="mega-menu-col">
                        <h4>Outils Créatifs &amp; Design</h4>
                        <ul>
                            <?php foreach ($menuProjects['col2'] as [$pSlug, $pLabel]): ?>
                                <li><a href="<?php echo htmlspecialchars(detailLink($pSlug, $slug, $basePath, $detailExt), ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($pSlug === $slug) ? 'class="current-page"' : ''; ?>><?php echo htmlspecialchars($pLabel, ENT_QUOTES, 'UTF-8'); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <!-- Colonne 3 : Templates & Environnements -->
                    <div class="mega-menu-col">
                        <h4>Templates &amp; Environnements</h4>
                        <ul>
                            <?php foreach ($menuProjects['col3'] as [$pSlug, $pLabel]): ?>
                                <li><a href="<?php echo htmlspecialchars(detailLink($pSlug, $slug, $basePath, $detailExt), ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($pSlug === $slug) ? 'class="current-page"' : ''; ?>><?php echo htmlspecialchars($pLabel, ENT_QUOTES, 'UTF-8'); ?></a></li>
                            <?php endforeach; ?>
                            <li><a href="#">À propos</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- BREADCRUMB -->
        <div class="detail-breadcrumb">
            <a href="<?php echo htmlspecialchars($indexHref, ENT_QUOTES, 'UTF-8'); ?>">Accueil</a>
            &rsaquo;
            <span><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>

        <!-- CORPS DE L'ARTICLE -->
        <div class="detail-layout">

            <!-- COLONNE PRINCIPALE -->
            <div class="detail-main">

                <div class="detail-title-block">
                    <div class="detail-project-label">Dossier Projet — L'Atelier Numérique</div>
                    <h1 class="detail-h1"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p class="detail-subtitle"><?php echo htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>

                <!-- Badge statut + technos -->
                <div class="detail-meta">
                    <span class="<?php echo htmlspecialchars($badge['class'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo $badge['label']; ?></span>
                    <?php foreach ($technos as $tech): ?>
                        <span class="badge-tech"><?php echo htmlspecialchars($tech, ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endforeach; ?>
                </div>

                <!-- Image principale -->
                <?php if ($screenshot): ?>
                <figure class="press-figure">
                    <img src="<?php echo htmlspecialchars($basePath . $screenshot, ENT_QUOTES, 'UTF-8'); ?>"
                         alt="fig: interface principale — <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
                    <figcaption class="press-caption">Fig. 1 — Interface principale · <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></figcaption>
                </figure>
                <?php endif; ?>

                <!-- Pitch -->
                <?php if ($pitch): ?>
                <p class="detail-pitch"><?php echo $pitch; ?></p>
                <?php endif; ?>

                <!-- Sections -->
                <?php foreach ($sections as $i => $section): ?>
                <div class="detail-section">
                    <?php if (!empty($section['title'])): ?>
                        <h3><?php echo htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <?php endif; ?>
                    <?php if (!empty($section['figure'])): ?>
                    <figure class="press-figure">
                        <img src="<?php echo htmlspecialchars($basePath . $section['figure'], ENT_QUOTES, 'UTF-8'); ?>"
                             alt="fig: <?php echo htmlspecialchars($section['figcaption'] ?? $section['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <figcaption class="press-caption">
                            Fig. <?php echo ($i + 2); ?> — <?php echo htmlspecialchars($section['figcaption'] ?? $section['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        </figcaption>
                    </figure>
                    <?php endif; ?>
                    <?php if (!empty($section['body'])): ?>
                        <?php echo $section['body']; ?>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>

                <!-- CTA vers l'application -->
                <div class="detail-section" style="border-top: 2px solid #111; text-align: center; padding-top: 22px; margin-top: 24px;">
                    <?php
                    // En local : depuis {slug}/detail.php, l'app est dans ../{appHref}
                    // En statique : chemin relatif depuis {slug}/detail.html
                    $ctaHref = '#';
                    if ($appHref && $appHref !== '#') {
                        if ($isStatic) {
                            $ctaHref = '../' . $appHref;
                        } else {
                            $ctaHref = '../' . $appHref;
                        }
                    }
                    ?>
                    <?php if ($appHref && $appHref !== '#'): ?>
                        <a href="<?php echo htmlspecialchars($ctaHref, ENT_QUOTES, 'UTF-8'); ?>"
                           class="detail-cta" target="_blank">
                            &#x1F680; Lancer <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    <?php else: ?>
                        <span class="detail-cta detail-cta-disabled">Application non disponible dans cet environnement</span>
                    <?php endif; ?>
                    <p style="font-family: -apple-system, sans-serif; font-size: 0.7rem; color: #aaa; margin-top: 8px; text-transform: uppercase; letter-spacing: 1px;">
                        &larr; <a href="<?php echo htmlspecialchars($indexHref, ENT_QUOTES, 'UTF-8'); ?>" style="color: #555; text-decoration: none;">Retour au Journal</a>
                    </p>
                </div>

            </div><!-- /detail-main -->

            <!-- COLONNE LATÉRALE -->
            <div class="detail-sidebar">

                <div class="sidebar-block">
                    <h5>&#x1F4CB; Fiche Technique</h5>
                    <p><strong>Projet :</strong> <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Statut :</strong> <?php echo $badge['label']; ?></p>
                    <?php if ($technos): ?>
                    <p><strong>Technologies :</strong><br><?php echo implode(', ', array_map(fn($t) => htmlspecialchars($t, ENT_QUOTES, 'UTF-8'), $technos)); ?></p>
                    <?php endif; ?>
                    <p><strong>Architecture :</strong> Flat-File / PHP Vanille</p>
                    <p><strong>Environnement :</strong> XAMPP Portable · Clé USB F:\</p>
                </div>

                <div class="sidebar-block">
                    <h5>&#x1F5DE; L'Atelier Numérique</h5>
                    <p>Journal de bord d'un développeur nomade. Chaque projet est une station de travail autonome, pensée pour fonctionner sans cloud et sans dépendance réseau.</p>
                    <p><strong>Rédacteur :</strong> Christophe Millot</p>
                </div>

                <?php if ($appHref && $appHref !== '#'): ?>
                <div class="sidebar-block" style="text-align: center;">
                    <h5>&#x1F680; Accès Direct</h5>
                    <a href="<?php echo htmlspecialchars($ctaHref, ENT_QUOTES, 'UTF-8'); ?>"
                       class="detail-cta" target="_blank" style="display: inline-block; width: auto; padding: 10px 16px;">
                        Lancer l'application
                    </a>
                </div>
                <?php endif; ?>

            </div><!-- /detail-sidebar -->

        </div><!-- /detail-layout -->

        <!-- FOOTER -->
        <div class="news-footer">
            <span>&copy; 2026 Christophe Millot &bull; Tous droits réservés</span>
            <span>&bull;</span>
            <span>L'Atelier Numérique &mdash; <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>

    </div><!-- /news-sheet -->

    <!-- CONTROL PANEL — PRÉSENT ET FONCTIONNEL (identique à index.php) -->
    <?php if (!$isStatic): ?>
    <button id="cp-toggle-btn" title="Control Panel" aria-label="Ouvrir le Control Panel" aria-expanded="false" aria-controls="control-panel">
        &#9776;
    </button>
    <div id="cp-backdrop" aria-hidden="true"></div>

    <aside id="control-panel" role="complementary" aria-label="Control Panel — Navigation du Journal">
        <div class="cp-header">
            <div>
                <div class="cp-header-title">&#9881; Control Panel</div>
                <div class="cp-header-subtitle">Navigation du Journal</div>
            </div>
            <button class="cp-close-btn" id="cp-close-btn" title="Fermer le panneau">&times;</button>
        </div>

        <div class="cp-actions">
            <a href="<?php echo htmlspecialchars($indexHref, ENT_QUOTES, 'UTF-8'); ?>"
               class="cp-action-btn cp-btn-primary"
               style="text-align:center; text-decoration:none; display:flex; align-items:center; justify-content:center;">
                &#8617; Accueil
            </a>
            <?php if ($appHref && $appHref !== '#'): ?>
            <a href="<?php echo htmlspecialchars($ctaHref, ENT_QUOTES, 'UTF-8'); ?>"
               class="cp-action-btn"
               target="_blank"
               style="text-align:center; text-decoration:none; display:flex; align-items:center; justify-content:center; background:#38bdf8; color:#0f172a; border-color:#38bdf8;">
                &#x1F680; App
            </a>
            <?php endif; ?>
        </div>

        <!-- Presets (affichage de navigation uniquement — presets gérés depuis l'index) -->
        <div class="cp-presets-section">
            <div class="cp-presets-title">Presets</div>
            <div class="cp-presets-grid">
                <?php for ($n = 1; $n <= 5; $n++): ?>
                <button type="button" class="cp-preset-box empty"
                        title="Gérez les presets depuis la page d'accueil du journal"
                        onclick="window.location.href='<?php echo htmlspecialchars($indexHref, ENT_QUOTES, 'UTF-8'); ?>'">
                    <?php echo $n; ?>
                </button>
                <?php endfor; ?>
            </div>
            <div class="cp-presets-hint">Configurables depuis l'accueil.</div>
        </div>

        <!-- Projets du Journal (navigation rapide) -->
        <div class="cp-body" id="cp-body">
            <div style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: #c0392b; border-bottom: 1px solid #e2ddd5; padding-bottom: 5px; margin-bottom: 10px;">Projets du Journal</div>
            <?php
            $allMenuProjects = array_merge($menuProjects['col1'], $menuProjects['col2'], $menuProjects['col3']);
            foreach ($allMenuProjects as [$pSlug, $pLabel]):
                $isCurrent = ($pSlug === $slug);
                $pLink = detailLink($pSlug, $slug, $basePath, $detailExt);
            ?>
            <div class="cp-project-row">
                <span style="width:8px; height:8px; border-radius:50%; background:<?php echo $isCurrent ? '#c0392b' : '#cbd5e1'; ?>; flex-shrink:0; display:inline-block;"></span>
                <a href="<?php echo htmlspecialchars($pLink, ENT_QUOTES, 'UTF-8'); ?>"
                   style="font-size:0.78rem; font-weight:<?php echo $isCurrent ? '800' : '600'; ?>; color:<?php echo $isCurrent ? '#c0392b' : '#2b2b2b'; ?>; text-decoration:none; flex:1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    <?php if ($isCurrent): ?>&#9654; <?php endif; ?>
                    <?php echo htmlspecialchars($pLabel, ENT_QUOTES, 'UTF-8'); ?>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="cp-footer">
            <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                <a href="<?php echo htmlspecialchars($indexHref, ENT_QUOTES, 'UTF-8'); ?>"
                   class="cp-apply-btn"
                   style="background:#38bdf8; color:#0f172a; border-color:#38bdf8; text-align:center; text-decoration:none; display:flex; align-items:center; justify-content:center;">
                    VOIR LE SITE
                </a>
                <?php if ($appHref && $appHref !== '#'): ?>
                <a href="<?php echo htmlspecialchars($ctaHref, ENT_QUOTES, 'UTF-8'); ?>"
                   class="cp-apply-btn" target="_blank"
                   style="background:#38bdf8; color:#0f172a; border-color:#38bdf8; text-align:center; text-decoration:none; display:flex; align-items:center; justify-content:center;">
                    EXPORTER
                </a>
                <?php else: ?>
                <button class="cp-apply-btn" style="background:#e2ddd5; color:#999; border-color:#e2ddd5; cursor:default;" disabled>EXPORTER</button>
                <?php endif; ?>
            </div>
            <button class="cp-apply-btn" onclick="window.location.href='<?php echo htmlspecialchars($indexHref, ENT_QUOTES, 'UTF-8'); ?>'">APPLY</button>
        </div>
    </aside>
    <?php endif; ?>

    <!-- JAVASCRIPT — MENU BURGER ET CONTROL PANEL (identique à index.php) -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const hamburgerBtn  = document.getElementById('hamburger-menu-btn');
            const stickyBtn     = document.getElementById('sticky-hamburger-btn');
            const megaMenu      = document.getElementById('journal-mega-menu');
            const iconSvg       = document.getElementById('hamburger-icon-svg');
            const stickyIconSvg = document.getElementById('sticky-hamburger-icon-svg');
            const closeBtn      = document.getElementById('mega-menu-close');
            const stickyHeader  = document.getElementById('sticky-header');
            const headerWrapper = document.querySelector('.news-header-wrapper');

            if (stickyHeader) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 120) {
                        stickyHeader.classList.add('visible');
                        document.body.classList.add('is-scrolled');
                    } else {
                        stickyHeader.classList.remove('visible');
                        document.body.classList.remove('is-scrolled');
                        if (megaMenu) {
                            megaMenu.classList.remove('active');
                            megaMenu.style.position = '';
                            megaMenu.style.top = '';
                            megaMenu.style.left = '';
                            megaMenu.style.right = '';
                        }
                        const defaultSvg = '<line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line>';
                        if (iconSvg) iconSvg.innerHTML = defaultSvg;
                        if (stickyIconSvg) stickyIconSvg.innerHTML = defaultSvg;
                    }
                });
            }

            function toggleMenu(e) {
                if (e) e.stopPropagation();
                const isOpen = megaMenu.classList.toggle('active');
                if (isOpen && window.innerWidth > 768 && window.scrollY > 120) {
                    megaMenu.style.position = 'fixed';
                    megaMenu.style.top = '48px';
                    megaMenu.style.left = '30px';
                    megaMenu.style.right = '30px';
                } else if (isOpen && window.innerWidth > 768) {
                    megaMenu.style.position = 'absolute';
                    megaMenu.style.top = '100%';
                    megaMenu.style.left = '0';
                    megaMenu.style.right = '0';
                }
                if (isOpen && window.innerWidth <= 768) {
                    document.body.appendChild(megaMenu);
                } else if (!isOpen && window.innerWidth <= 768 && headerWrapper) {
                    headerWrapper.appendChild(megaMenu);
                }
                const svgContent = isOpen
                    ? '<line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>'
                    : '<line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line>';
                if (iconSvg) iconSvg.innerHTML = svgContent;
                if (stickyIconSvg) stickyIconSvg.innerHTML = svgContent;
                if (!isOpen) {
                    document.querySelectorAll('.mega-menu-col').forEach(c => c.classList.remove('open', 'is-expanded'));
                }
            }

            if (hamburgerBtn) hamburgerBtn.addEventListener('click', toggleMenu);
            if (stickyBtn)    stickyBtn.addEventListener('click', toggleMenu);

            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    megaMenu.classList.remove('active');
                    megaMenu.style.position = '';
                    megaMenu.style.top = '';
                    megaMenu.style.left = '';
                    megaMenu.style.right = '';
                    if (window.innerWidth <= 768 && headerWrapper) headerWrapper.appendChild(megaMenu);
                    const defaultSvg = '<line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line>';
                    if (iconSvg) iconSvg.innerHTML = defaultSvg;
                    if (stickyIconSvg) stickyIconSvg.innerHTML = defaultSvg;
                    document.querySelectorAll('.mega-menu-col').forEach(c => c.classList.remove('open', 'is-expanded'));
                });
            }

            const megaMenuCols = document.querySelectorAll('.mega-menu-col');
            megaMenuCols.forEach(col => {
                const title = col.querySelector('h4');
                if (title) {
                    title.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const isOpen = col.classList.toggle('open');
                        if (window.innerWidth <= 768) {
                            if (isOpen) {
                                megaMenuCols.forEach(c => { if (c !== col) c.classList.remove('open', 'is-expanded'); });
                                col.classList.add('is-expanded');
                            } else {
                                col.classList.remove('is-expanded');
                            }
                        }
                    });
                }
            });

            if (megaMenu) megaMenu.addEventListener('click', e => e.stopPropagation());

            // CONTROL PANEL
            const cpToggleBtn = document.getElementById('cp-toggle-btn');
            const cpCloseBtn  = document.getElementById('cp-close-btn');
            const cpBackdrop  = document.getElementById('cp-backdrop');
            const cpPanel     = document.getElementById('control-panel');

            function openCp() {
                if (!cpPanel) return;
                cpPanel.classList.add('cp-open');
                if (cpBackdrop) cpBackdrop.classList.add('cp-visible');
                cpToggleBtn.classList.add('cp-open');
                cpToggleBtn.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            }
            function closeCp() {
                if (!cpPanel) return;
                cpPanel.classList.remove('cp-open');
                if (cpBackdrop) cpBackdrop.classList.remove('cp-visible');
                cpToggleBtn.classList.remove('cp-open');
                cpToggleBtn.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }

            if (cpToggleBtn) cpToggleBtn.addEventListener('click', e => { e.stopPropagation(); cpPanel && cpPanel.classList.contains('cp-open') ? closeCp() : openCp(); });
            if (cpCloseBtn)  cpCloseBtn.addEventListener('click', closeCp);
            if (cpBackdrop)  cpBackdrop.addEventListener('click', closeCp);
        });
    </script>

</body>
</html>
