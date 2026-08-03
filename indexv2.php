<?php
// index v2
$dir = __DIR__;
$statusesFile = $dir . DIRECTORY_SEPARATOR . 'statuses.json';
$jsonFile = $dir . DIRECTORY_SEPARATOR . 'dashboard-designer' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'projects.json';

// Chargement des détails depuis data/projects.json
$projectsDetailsMap = [];
if (file_exists($jsonFile)) {
    $jsonContent = file_get_contents($jsonFile);
    $decodedDetails = json_decode($jsonContent, true);
    if (is_array($decodedDetails)) {
        foreach ($decodedDetails as $pDetail) {
            if (isset($pDetail['name'])) {
                $projectsDetailsMap[mb_strtolower($pDetail['name'])] = $pDetail;
            }
        }
    }
}

// Gestion AJAX du changement de statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project']) && isset($_POST['status'])) {
    $projectName = basename($_POST['project']);
    $newStatus = $_POST['status'];
    
    $statuses = [];
    if (file_exists($statusesFile)) {
        $content = file_get_contents($statusesFile);
        $decoded = json_decode($content, true);
        if (is_array($decoded)) {
            $statuses = $decoded;
        }
    }
    
    $statuses[$projectName] = $newStatus;
    file_put_contents($statusesFile, json_encode($statuses, JSON_PRETTY_PRINT));
    
    echo json_encode(['success' => true]);
    exit;
}

$files = scandir($dir);
$projectsRaw = [];

// Chargement des statuts sauvegardés
$savedStatuses = [];
if (file_exists($statusesFile)) {
    $content = file_get_contents($statusesFile);
    $decoded = json_decode($content, true);
    if (is_array($decoded)) {
        $savedStatuses = $decoded;
    }
}

// Dossiers système et dossiers internes à exclure du listing
$exclude = ['.git', 'core', 'server', 'Data', 'sql', 'mac-server-runtime', 'mac-tools', 'static', 'projet-client', 'partials', 'mon-premier-site'];

foreach ($files as $file) {
    if ($file === '.' || $file === '..' || !is_dir($dir . '/' . $file)) continue;
    if (in_array($file, $exclude)) continue;

    $hasIndex = file_exists($dir . '/' . $file . '/index.php') || file_exists($dir . '/' . $file . '/index.html');
    $isWP    = file_exists($dir . '/' . $file . '/wp-config.php');

    $title   = $file;
    $lowerFile = mb_strtolower($file);
    
    $customDetails = isset($projectsDetailsMap[$lowerFile]) ? $projectsDetailsMap[$lowerFile] : null;

    if (!$customDetails && in_array($lowerFile, ['user_journey-v1.0', 'user_journey'])) {
        $customDetails = [
            'name' => $file,
            'details' => [
                'niveau1' => [
                    'pitch' => "Pensé pour façonner et orchestrer les parcours utilisateurs, <strong>user_journey-v1.0</strong> est le seul outil de cette clé entièrement conçu pour l'UX-UI pure.",
                    'technos' => ['UI Design', 'UX Research', 'Prototypage'],
                    'image' => 'photo-640x480.png'
                ]
            ]
        ];
    }

    $description = "Description détaillée et présentation complète du projet web : " . htmlspecialchars($file, ENT_QUOTES, 'UTF-8') . ".";
    
    $imgName = 'photo-640x480.png';
    if ($customDetails && isset($customDetails['details']['niveau1']['image']) && !empty($customDetails['details']['niveau1']['image'])) {
        $imgName = basename($customDetails['details']['niveau1']['image']);
    }
    
    if ($lowerFile === 'cms-2026-v8-full') {
        $screenshot = 'images/images-cms/' . $imgName;
    } else {
        $screenshot = 'dashboard-designer/assets/img/' . $imgName;
    }
    
    if (isset($savedStatuses[$file])) {
        $statusKey = $savedStatuses[$file];
    } else {
        $statusKey = ($isWP || $hasIndex) ? 'operational' : 'progress';
    }

    if ($isWP) {
        $badgeLabel = '&#x2699;&#xFE0F; WordPress';
        $badgeClass = 'badge badge-wp';
    } else {
        if ($statusKey === 'validated') {
            $badgeLabel = '&#x1F7E2; Valid&eacute;';
        } elseif ($statusKey === 'operational') {
            $badgeLabel = '&#x1F7E0; Op&eacute;rationnel';
        } else {
            $badgeLabel = '&#x1F534; En cours';
        }
        $badgeClass = 'badge badge-' . $statusKey;
    }

    $colSpan = in_array($lowerFile, ['cms-2026-v8-full', 'dashboard-designer', 'wordpress-portable']) ? 12 : 6;
    $sizeLabel = ($colSpan === 12) ? 'CMS' : 'UX-UI';

    $projectsRaw[$lowerFile] = [
        'name'        => $file,
        'title'       => $title,
        'hasIndex'    => true,
        'isWP'        => $isWP,
        'description' => $description,
        'screenshot'  => $screenshot,
        'statusKey'   => $statusKey,
        'badgeLabel'  => $badgeLabel,
        'badgeClass'  => $badgeClass,
        'cardClass'   => 'card',
        'details'     => $customDetails ? $customDetails['details'] : null,
        'colSpan'     => (int)$colSpan,
        'colClass'    => 'news-col-' . (int)$colSpan,
        'sizeLabel'   => $sizeLabel,
        'linkHref'    => '/' . rawurlencode($file) . '/'
    ];
}

// INJECTION MANUELLE DE WORKSTATION
$projectsRaw['workstation'] = [
    'name'        => 'workstation',
    'title'       => 'Workstation',
    'hasIndex'    => true,
    'isWP'        => false,
    'description' => "Cockpit central et tableau de bord de l'atelier nomade.",
    'screenshot'  => 'images/images-workstation/01-header.png',
    'statusKey'   => 'operational',
    'badgeLabel'  => '&#x1F7E0; Op&eacute;rationnel',
    'badgeClass'  => 'badge badge-operational',
    'cardClass'   => 'card',
    'details'     => [
        'niveau1' => [
            'pitch' => "Véritable cockpit central et tableau de bord ultime, <strong>Workstation</strong> unifie le pilotage du temps, la météo en direct et les outils de prototypage.",
            'technos' => ['PHP', 'JavaScript', 'CSS Custom'],
            'image' => '01-header.png'
        ]
    ],
    'colSpan'     => 12,
    'colClass'    => 'news-col-12',
    'sizeLabel'   => 'SYSTEM',
    'linkHref'    => '#'
];

$orderedKeys = [
    'workstation', 'cms-2026-v8-full', 'dashboard-designer', 'skeletor-v1.0',
    'skeletor-v1.0-o2switch', 'modulor', 'personator-v1.2', 'texturor',
    'user_journey-v1.0', 'wordpress-portable', 'mon-site'
];

$projects = [];
foreach ($orderedKeys as $key) {
    if (isset($projectsRaw[$key])) {
        $projects[] = $projectsRaw[$key];
        unset($projectsRaw[$key]);
    }
}
foreach ($projectsRaw as $extraP) {
    $projects[] = $extraP;
}

$rows = [];
$currentRow = [];
$currentSpan = 0;

foreach ($projects as $p) {
    if (($currentSpan + (int)$p['colSpan']) > 12) {
        $rows[] = $currentRow;
        $currentRow = [$p];
        $currentSpan = (int)$p['colSpan'];
    } else {
        $currentRow[] = $p;
        $currentSpan += (int)$p['colSpan'];
        if ($currentSpan === 12) {
            $rows[] = $currentRow;
            $currentRow = [];
            $currentSpan = 0;
        }
    }
}
if (!empty($currentRow)) {
    $rows[] = $currentRow;
}

$lastRowSpan = 0;
if (!empty($rows)) {
    $lastRow = end($rows);
    foreach ($lastRow as $item) {
        $lastRowSpan += (int)$item['colSpan'];
    }
}
$bearColSpan = max(0, 12 - $lastRowSpan);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>DEV NOMADE - Dashboard</title>
    <link rel="stylesheet" href="static/fonts/fontawesome/css/all.min.css">

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

        h1 {
            font-size: 1.8rem;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--card-bg);
            padding-bottom: 15px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid transparent;
        }
        .card:hover { transform: translateY(-4px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3); }
        .card-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 8px; word-break: break-all; }

        .badge {
            display: inline-block; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px;
            padding: 4px 10px; border-radius: 99px; margin-bottom: 14px; text-transform: uppercase;
            cursor: pointer; user-select: none; transition: transform 0.1s;
        }
        .badge:active { transform: scale(0.95); }
        .badge-validated   { background: rgba(34,197,94,0.15); color: var(--green); }
        .badge-operational { background: rgba(245,158,11,0.15); color: var(--orange); }
        .badge-progress    { background: rgba(239,68,68,0.15); color: var(--red); }
        .badge-wp          { background: rgba(56,189,248,0.15); color: var(--accent); cursor: default; }

        .card-actions { display: flex; flex-direction: column; gap: 8px; }
        .card-link {
            display: inline-block; text-align: center; background-color: var(--accent); color: var(--bg-color);
            text-decoration: none; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 0.9rem;
            transition: background-color 0.2s;
        }
        .card-link:hover { background-color: var(--accent-hover); }

        .btn-info {
            background: rgba(56, 189, 248, 0.05); border: 1px solid rgba(56, 189, 248, 0.3); color: var(--accent);
            text-align: center; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.8rem;
            font-weight: 600; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 6px;
        }
        .btn-info:hover { background-color: var(--accent); color: var(--bg-color); border-color: var(--accent); }

        #modulor-overlay {
            position: fixed; inset: 0; background-color: var(--bg-color); z-index: 10000;
            display: flex; flex-direction: column; opacity: 0; pointer-events: none;
            transition: opacity 0.3s ease; overflow-y: auto; padding: 40px; box-sizing: border-box;
        }
        #modulor-overlay.active { opacity: 1; pointer-events: auto; }
        .overlay-container { max-width: 900px; width: 100%; margin: 0 auto; position: relative; display: flex; flex-direction: column; }
        .overlay-close {
            position: fixed; top: 30px; right: 40px; background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.1); color: var(--text-main);
            padding: 10px 20px; border-radius: 8px; font-size: 1rem; font-weight: bold;
            cursor: pointer; display: flex; align-items: center; gap: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3); transition: all 0.2s; z-index: 10001;
        }
        .overlay-close:hover { background: var(--accent); color: var(--bg-color); }
        #overlay-title { font-size: 2.2rem; color: var(--accent); margin-top: 0; margin-bottom: 15px; }
        #overlay-text { font-size: 1rem; line-height: 1.6; color: var(--text-muted); margin-bottom: 25px; }
        .overlay-image-wrapper { background: var(--card-bg); border-radius: 12px; padding: 10px; border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        #overlay-img { width: 100%; height: auto; border-radius: 8px; display: block; }

        .level-block { background: rgba(255, 255, 255, 0.03); border-radius: 8px; padding: 15px; margin-bottom: 15px; border: 1px solid rgba(255, 255, 255, 0.05); }
        .level-title { color: var(--accent); font-size: 1.1rem; margin-top: 0; margin-bottom: 8px; border-bottom: 1px solid rgba(56, 189, 248, 0.2); padding-bottom: 4px; }
        .badge-tech { background: rgba(56, 189, 248, 0.15); color: var(--accent); padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; margin-right: 5px; display: inline-block; }

        .separator-v2 { border: none; height: 6px; background-color: var(--accent); margin: 40px 0; opacity: 0.8; border-radius: 3px; }

        details.section-block { margin-bottom: 40px; border-radius: 12px; overflow: hidden; }
        details.section-block > summary {
            list-style: none; cursor: pointer; user-select: none; padding: 14px 20px;
            background-color: var(--card-bg); border: 1px solid rgba(56, 189, 248, 0.15);
            border-radius: 12px; display: flex; align-items: center; gap: 12px;
            transition: background-color 0.2s, border-color 0.2s;
        }
        details.section-block > summary::-webkit-details-marker { display: none; }
        details.section-block > summary::marker { display: none; }
        details.section-block > summary:hover { background-color: #243048; border-color: var(--accent); }
        details.section-block > summary h2 { margin: 0; font-size: 1.3rem; font-weight: 700; color: var(--text-main); flex: 1; }
        details.section-block > summary .summary-chevron { font-size: 0.9rem; color: var(--accent); transition: transform 0.25s ease; }
        details.section-block[open] > summary .summary-chevron { transform: rotate(180deg); }
        .section-body { padding: 24px 4px 8px 4px; }

        .news-sheet {
            background-color: #fdfbf7;
            color: #2b2b2b;
            border: 2px solid #2b2b2b;
            border-radius: 4px;
            padding: 35px;
            font-family: Georgia, "Times New Roman", serif;
            margin-bottom: 40px;
            position: relative;
        }

        .news-header-divider {
            border: none;
            border-top: 3px double #333333 !important;
            margin: 15px 0 0 0;
        }

        .news-header-wrapper {
            position: relative;
        }

        .journal-mega-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff !important;
            border: 2px solid #2b2b2b;
            border-top: none;
            padding: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            z-index: 999;
            font-family: -apple-system, sans-serif;
            box-sizing: border-box;
        }
        .journal-mega-menu.active {
            display: block;
        }
        
        .mega-menu-close-btn {
            display: none;
        }

        .mega-menu-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        
        .mega-menu-col h4 {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #c0392b;
            border-bottom: 1px solid #2b2b2b;
            padding-bottom: 6px;
            margin-top: 0;
            margin-bottom: 12px;
        }
        .mega-menu-col ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .mega-menu-col li {
            margin-bottom: 8px;
        }
        .mega-menu-col a {
            color: #2b2b2b;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            display: block;
            padding: 6px 0;
            transition: color 0.2s, background-color 0.2s;
        }
        .mega-menu-col a:hover {
            color: #ffffff !important;
            background-color: #555555 !important;
            text-decoration: none !important;
            padding-left: 8px;
            padding-right: 8px;
            border-radius: 4px;
        }
        .mega-menu-col a:active, .mega-menu-col a.clicked {
            color: #ffffff !important;
            background-color: #000000 !important;
            text-decoration: none !important;
            padding-left: 8px;
            padding-right: 8px;
            border-radius: 4px;
        }

        .news-bandeau {
            text-align: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            border-bottom: 1px solid #333333;
            padding-bottom: 8px;
            margin-bottom: 15px;
            font-weight: 700;
            color: #4a4a4a;
        }

        .news-header-grid {
            display: grid;
            grid-template-columns: 180px 1fr 180px;
            align-items: center;
            padding-bottom: 15px;
            text-align: center;
        }
        .news-ear {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.75rem;
            color: #555555;
            line-height: 1.3;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .news-manchette {
            font-size: 2.2rem;
            font-weight: 900;
            letter-spacing: -0.5px;
            text-transform: uppercase;
            color: #2b2b2b;
            margin: 0;
            line-height: 1.1;
            font-family: Georgia, serif;
        }

        .news-tribune {
            border-bottom: 1px solid #333333;
            padding-top: 25px;
            padding-bottom: 20px;
            margin-top: 15px;
            margin-bottom: 25px;
            text-align: justify;
        }
        .news-tribune h3 {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #c0392b;
            margin: 0 0 8px 0;
            font-weight: 700;
        }
        .news-tribune p {
            font-size: 1.1rem;
            line-height: 1.5;
            margin: 0;
            font-style: italic;
            color: #333333;
        }

        .news-row {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 24px;
            margin-bottom: 24px;
        }

        .news-col-12 { grid-column: span 12; }
        .news-col-6  { grid-column: span 6; }
        .news-col-3  { grid-column: span 3; }

        @media (max-width: 1024px) {
            .news-col-12, .news-col-6 { grid-column: span 12; }
            .news-col-3 { grid-column: span 6; }
        }
        @media (max-width: 768px) {
            .news-col-12, .news-col-6, .news-col-3 { grid-column: span 12; }
            .news-header-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                padding-bottom: 15px;
            }
            .news-header-grid > div:nth-child(1) {
                grid-column: 1 / 2;
                text-align: left !important;
            }
            .news-header-grid > div:nth-child(2) {
                grid-column: 1 / -1;
                grid-row: 1;
                text-align: center !important;
                margin-bottom: 10px;
            }
            .news-header-grid > div:nth-child(3) {
                grid-column: 2 / 3;
                text-align: right !important;
                display: flex;
                flex-direction: column;
                align-items: flex-end !important;
            }
            .news-header-grid > div:nth-child(3) > div:last-child {
                display: flex;
                justify-content: flex-end !important;
                width: 100%;
            }

            /* MENU MOBILE ANIMÉ AVEC PLEINE HAUTEUR, RÉORGANISATION ET REMPLISSAGE TOTAL DU BLOC LI */
            .journal-mega-menu.active {
                display: flex !important;
                position: fixed !important;
                inset: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                min-height: 100dvh !important;
                background: #fdfbf7 !important;
                z-index: 99999 !important;
                padding: 20px !important;
                box-sizing: border-box !important;
                overflow-y: auto !important;
                border: none !important;
                box-shadow: none !important;
                flex-direction: column !important;
                justify-content: flex-start !important;
            }
            .mega-menu-close-btn {
                display: flex;
                justify-content: flex-end;
                margin-bottom: 15px;
                padding-bottom: 12px;
                border-bottom: 1px solid #e2ddd5;
                flex-shrink: 0;
            }
            .mega-menu-close-btn button {
                background: #2b2b2b;
                color: #fff;
                border: none;
                padding: 10px 18px;
                border-radius: 8px;
                font-size: 0.95rem;
                font-weight: bold;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .mega-menu-grid {
                display: flex !important;
                flex-direction: column !important;
                flex: 1 !important;
                gap: 12px !important;
                transition: all 0.3s ease;
            }
            
            .mega-menu-col {
                background: rgba(0,0,0,0.02);
                border: 1px solid #e2ddd5;
                border-radius: 8px;
                overflow: hidden;
                margin-bottom: 0 !important;
                transition: order 0.3s ease, flex 0.3s ease;
            }

            .mega-menu-col.is-expanded {
                order: -1;
                display: flex !important;
                flex-direction: column !important;
                flex: 1 !important;
            }

            .mega-menu-col h4 {
                font-size: 1.15rem;
                font-weight: 800;
                margin: 0;
                padding: 15px;
                background: #fff;
                border-bottom: 1px solid #e2ddd5;
                cursor: pointer;
                display: flex;
                justify-content: space-between;
                align-items: center;
                user-select: none;
                flex-shrink: 0;
            }
            .mega-menu-col h4::after {
                content: '▼';
                font-size: 0.75rem;
                color: #c0392b;
                transition: transform 0.25s ease;
            }
            .mega-menu-col.open h4::after {
                transform: rotate(180deg);
            }
            .mega-menu-col ul {
                display: none;
                padding: 0 !important;
                background: #fdfbf7;
                margin: 0 !important;
            }
            .mega-menu-col.open ul {
                display: flex !important;
                flex-direction: column !important;
                flex: 1 !important;
                justify-content: space-between !important;
            }
            .mega-menu-col.is-expanded.open ul {
                display: flex !important;
                flex-direction: column !important;
                flex: 1 !important;
                justify-content: space-between !important;
            }
            
            /* Les éléments li prennent toute la largeur et répartissent la hauteur en mode ouvert */
            .mega-menu-col li {
                margin-bottom: 0 !important;
                border-bottom: 1px dashed rgba(0,0,0,0.08);
                display: flex !important;
                align-items: stretch !important;
                flex: 1 !important;
                width: 100% !important;
            }
            .mega-menu-col li:last-child {
                border-bottom: none;
            }
            
            /* Les liens remplissent à 100% la largeur et la hauteur de leur conteneur li sans décalage */
            .mega-menu-col a {
                font-size: 1.1rem;
                font-weight: 700;
                padding: 0 20px;
                width: 100% !important;
                height: 100% !important;
                display: flex !important;
                align-items: center !important;
                text-decoration: none !important;
                color: #2b2b2b;
                background-color: transparent;
                box-sizing: border-box;
                border-radius: 0 !important;
                transition: background-color 0.15s ease, color 0.15s ease;
            }
            
            /* Survol mobile fixe : Gris soutenu (#555) et texte blanc occupant 100% de la surface */
            .mega-menu-col a:hover {
                color: #ffffff !important;
                background-color: #555555 !important;
                text-decoration: none !important;
                padding-left: 20px !important;
                padding-right: 20px !important;
                border-radius: 0 !important;
            }
            
            /* Clic mobile : Noir (#000) et texte blanc occupant 100% de la surface */
            .mega-menu-col a:active, .mega-menu-col a.clicked {
                color: #ffffff !important;
                background-color: #000000 !important;
                text-decoration: none !important;
                padding-left: 20px !important;
                padding-right: 20px !important;
                border-radius: 0 !important;
            }
        }

        .news-article {
            background: #fff;
            padding: 22px;
            border: 1px solid #e2ddd5;
            box-shadow: 0 2px 4px rgba(0,0,0,0.01);
            height: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .news-article h4 {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: #2b2b2b;
            margin: 0 0 10px 0;
            border-bottom: 2px solid #2b2b2b;
            padding-bottom: 4px;
        }

        .press-figure {
            margin: 0 0 16px 0;
            background: #fdfbf7;
            border: 1px solid #e2ddd5;
            padding: 8px;
            box-sizing: border-box;
        }
        .press-figure img {
            width: 100%;
            height: auto;
            max-height: 480px;
            object-fit: contain;
            background: #f8fafc;
            display: block;
            border: 1px solid #dcd7ce;
        }
        
        .press-figure-scrollable img {
            height: 400px !important;
            max-height: 400px !important;
            object-fit: cover !important;
            object-position: top !important;
            overflow-y: auto;
        }

        .press-caption {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666;
            margin-top: 6px;
            font-weight: bold;
            text-align: center;
        }

        .press-duo-layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 16px;
            align-items: center;
            margin-bottom: 16px;
            background: #fdfbf7;
            border: 1px solid #e2ddd5;
            padding: 8px;
        }
        .press-duo-layout img {
            width: 100%;
            height: auto;
            max-height: 280px;
            object-fit: contain;
            background: #f8fafc;
            display: block;
            border: 1px solid #dcd7ce;
        }
        .press-duo-text {
            font-size: 0.85rem;
            line-height: 1.5;
            color: #333;
            text-align: justify;
            margin: 0;
        }

        @media (max-width: 768px) {
            .press-duo-layout {
                grid-template-columns: 1fr;
            }
        }

        .editor-switch-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 4px;
            background: #f1f3f5;
            padding: 8px;
            border-radius: 4px 4px 0 0;
            border: 1px solid #e2ddd5;
            border-bottom: none;
        }
        .editor-switch-btn {
            background: #fff;
            border: 1px solid #cbd5e1;
            padding: 6px 14px;
            font-size: 0.7rem;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            border-radius: 3px;
            color: #333;
            transition: all 0.2s;
            text-align: center;
            line-height: 1.2;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .editor-switch-btn.active, .editor-switch-btn:hover {
            background: #2b2b2b;
            color: #fff;
            border-color: #2b2b2b;
        }
        .editor-switch-hint {
            text-align: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.65rem;
            color: #777;
            background: #f1f3f5;
            padding: 0 8px 6px 8px;
            margin-bottom: 12px;
            border: 1px solid #e2ddd5;
            border-top: none;
            border-radius: 0 0 4px 4px;
            font-style: italic;
        }

        .news-article p.news-pitch::first-letter {
            font-size: 2.8rem;
            float: left;
            line-height: 0.85;
            padding-right: 6px;
            font-weight: bold;
            color: #c0392b;
            font-family: Georgia, serif;
        }
        .news-article p.news-pitch {
            font-size: 0.95rem;
            line-height: 1.5;
            margin: 0 0 10px 0;
            text-align: justify;
            color: #333333;
        }

        .news-subhead {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #c0392b;
            margin: 10px 0 3px 0;
            letter-spacing: 0.5px;
        }
        .news-article p {
            font-size: 0.88rem;
            line-height: 1.5;
            margin: 0 0 8px 0;
            text-align: justify;
            color: #4a4a4a;
        }
        .news-article ul { margin: 4px 0 8px 0; padding-left: 1.1em; color: #4a4a4a; }
        .news-article li { font-size: 0.85rem; line-height: 1.4; margin-bottom: 2px; }

        .news-sheet .badge-tech, 
        .news-sheet .press-badge-tech, 
        .news-sheet .badge {
            display: none !important;
        }

        .news-footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #333333;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.75rem;
            color: #555555;
            display: flex;
            justify-content: space-between;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .news-colophon-footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px dashed #bbbbbb;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.80rem;
            color: #4a4a4a;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .news-article-link-container {
            margin-top: 15px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-top: 1px solid #f0ede6;
            padding-top: 8px;
            text-align: right;
        }
        .news-article-link {
            color: #666666;
            text-decoration: none;
            transition: color 0.2s, font-weight 0.2s;
            display: inline-block;
        }
        .news-article-link:hover {
            color: #111111;
            font-weight: bold;
        }

        @media (min-width: 1025px) {
            .desk-col-2 {
                column-count: 2;
                column-gap: 24px;
                text-align: justify;
            }
        }

    </style>
</head>
<body>

    <h1 style="display: none;">🚀 Mes Projets Nomades</h1>

    <!--------------------------------------------------------------------------->
    <!--------------------------------------------------------------------------->
    <!--------------------------------------------------------------------------->
    <!--------------------------------------------------------------------------->
    <!-- JOURNAL (ACCUEIL PRINCIPAL)                                           -->
    <!--------------------------------------------------------------------------->
    <!--------------------------------------------------------------------------->
    <!--------------------------------------------------------------------------->
    <!--------------------------------------------------------------------------->
    <div class="news-sheet">
        
        <div class="news-bandeau">
            Chronique Indépendante • Édition Spéciale Nomadisme Numérique • 2026
        </div>

        <div class="news-header-wrapper">
            <div class="news-header-grid">
                <div class="news-ear" style="text-align: left;">
                    <strong>SUPPORT :</strong> Clé USB F:\<br>
                    <strong>SERVEUR :</strong> XAMPP Portable
                </div>
                <div>
                    <h2 class="news-manchette">L'Atelier Numérique</h2>
                    <div style="font-size: 0.85rem; letter-spacing: 2px; text-transform: uppercase; margin-top: 6px; font-weight: bold; color: #444;">Christophe Millot</div>
                </div>
                <div class="news-ear" style="display: flex; flex-direction: column; align-items: flex-end; justify-content: center; gap: 4px; text-align: right;">
                    <div>
                        <strong>ARCHITECTURES :</strong> Flat-File<br>
                        <strong>STATUT :</strong> Opérationnel
                    </div>
                    <!-- Icône hamburger qui se transformera dynamiquement en croix -->
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

            <div id="journal-mega-menu" class="journal-mega-menu">
                <div class="mega-menu-close-btn">
                    <button type="button" id="mega-menu-close"><i class="fas fa-times"></i> Fermer</button>
                </div>
                <div class="mega-menu-grid">
                    <div class="mega-menu-col">
                        <h4>Navigation</h4>
                        <ul>
                            <li><a href="page2.php"><i class="fas fa-file-alt"></i> Synthèse (Page 2)</a></li>
                            <li><a href="#top"><i class="fas fa-home"></i> Haut de page</a></li>
                        </ul>
                    </div>
                    <div class="mega-menu-col">
                        <h4>Applications &amp; Modules</h4>
                        <ul>
                            <?php foreach ($projects as $p): ?>
                                <li><a href="<?php echo htmlspecialchars($p['linkHref'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank"><i class="fas fa-folder-open"></i> <?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="mega-menu-col">
                        <h4>Outils &amp; Clé</h4>
                        <ul>
                            <li><a href="#"><i class="fas fa-database"></i> Statuts JSON</a></li>
                            <li><a href="#"><i class="fas fa-info-circle"></i> À propos</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="news-tribune">
            <h3>Tribune Libre — L'Affranchissement du Cloud</h3>
            <p>« S'affranchir des infrastructures distantes pour recentrer le développement web sur l'essentiel : la maîtrise absolue du code, de l'octet initial jusqu'au déploiement final, au creux d'un support de poche inaltérable. »</p>
        </div>

        <?php foreach ($rows as $index => $rowProjects): ?>
            <div class="news-row">
                <?php foreach ($rowProjects as $p): ?>
                    <?php
                    $linkHref = '/' . rawurlencode($p['name']) . '/';
                    ?>
                    <div class="<?php echo htmlspecialchars($p['colClass'], ENT_QUOTES, 'UTF-8'); ?>">
                        <article class="news-article" style="<?php echo ($p['name'] === 'workstation' || $p['name'] === 'cms-2026-v8-full') ? 'background-color: #fdf2f4 !important; border: 2px dashed #f43f5e !important;' : ''; ?>">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: baseline; border-bottom: 2px solid #111; padding-bottom: 4px; margin-bottom: 8px;">
                                    <h4 style="margin: 0; border: none; padding: 0;"><?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <span style="font-family: -apple-system, sans-serif; font-size: 0.65rem; text-transform: uppercase; color: #777; letter-spacing: 0.5px;"><?php echo htmlspecialchars($p['sizeLabel'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                
                                <?php if ($p['name'] === 'cms-2026-v8-full'): ?>
                                    
                                    <p class="news-pitch">Au cœur de l'atelier nomade se dresse <strong>cms-2026-v8-full</strong>, un système de gestion de contenu sur-mesure et ultra-léger pensé pour s'affranchir des lourdeurs du web traditionnel. Entièrement autonome sur un serveur local XAMPP, il incarne l'excellence du développement Flat-file.</p>

                                    <div class="press-duo-layout">
                                        <div>
                                            <img src="images/images-cms/01-desktop-capture-cms-hero.png" alt="Vitrine Hero">
                                            <div class="press-caption">Fig. 1 — En-tête de la vitrine (Hero)</div>
                                        </div>
                                        <div>
                                            <p class="press-duo-text"><strong>La vitrine d'accueil :</strong> Première immersion dans l'écosystème du CMS. Conçu comme une invitation visuelle, ce bandeau d'en-tête (Hero) pose l'ambiance graphique de la plateforme. Pensé pour évoluer, il pourra prochainement s'appuyer sur une bibliothèque d'images pour renouveler l'inspiration à chaque connexion.</p>
                                        </div>
                                    </div>

                                    <div class="press-duo-layout">
                                        <div>
                                            <img src="images/images-cms/02-desktop-capture-cms-accueil.png" alt="Poste de pilotage">
                                            <div class="press-caption">Fig. 2 — Poste de pilotage (Gestion des Projets)</div>
                                        </div>
                                        <div>
                                            <p class="press-duo-text"><strong>Le poste de pilotage :</strong> Tour de contrôle de l'administrateur unique, cette interface centralise l'accès aux cartes de projets. Chaque espace permet d'initialiser un nouvel article ou de basculer en mode de configuration, garantissant une maîtrise absolue des flux de travail sans aucune dépendance cloud.</p>
                                        </div>
                                    </div>

                                    <div class="news-subhead">Immersion dans l'Éditeur &amp; Le Responsive</div>
                                    <p class="press-duo-text" style="margin-bottom: 10px;">La visite se poursuit par la découverte de l'éditeur de pages. Utilisez le sélecteur ci-dessous pour basculer dynamiquement entre l'aperçu Bureau et l'aperçu Mobile de l'interface.</p>
                                    
                                    <div class="editor-switch-bar">
                                        <button type="button" class="editor-switch-btn active" onclick="switchEditorView('desktop', this)">
                                            <span>Aperçu desktop</span>
                                        </button>
                                        <button type="button" class="editor-switch-btn" onclick="switchEditorView('mobile', this)">
                                            <span>Aperçu mobile</span>
                                        </button>
                                    </div>
                                    <div class="editor-switch-hint">Cliquez dans l'image pour l'agrandir</div>

                                    <figure class="press-figure press-figure-scrollable" id="editor-figure-wrapper">
                                        <img id="editor-demo-img" src="images/images-cms/03-capture-cms-editeur-frame-desktop.png" alt="Éditeur Desktop">
                                        <figcaption class="press-caption" id="editor-demo-caption">Fig. 3 — L'éditeur en mode Bureau (hauteur fixe de 400px avec défilement interne)</figcaption>
                                    </figure>

                                    <div class="news-subhead">Restitution et Rendu Final</div>
                                    <p class="press-duo-text" style="margin-bottom: 10px;">Chaque modification s'exporte et s'affiche instantanément en conditions réelles, garantissant une parfaite lisibilité sur tous les supports d'exploitation.</p>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 16px;">
                                        <figure class="press-figure" style="margin: 0;">
                                            <img src="images/images-cms/03-desktopcapture cms-site-exemple-page.png" alt="Exemple de page" style="max-height: 250px;">
                                            <figcaption class="press-caption">Fig. 5 — Page exemple générée</figcaption>
                                        </figure>
                                        <figure class="press-figure" style="margin: 0;">
                                            <img src="images/images-cms/05-capture cms-site-mobile.png" alt="Rendu mobile" style="max-height: 250px;">
                                            <figcaption class="press-caption">Fig. 6 — Rendu mobile final</figcaption>
                                        </figure>
                                    </div>

                                <?php elseif ($p['name'] === 'workstation'): ?>
                                    
                                    <p class="news-pitch">Au cœur de l'écosystème portable, <strong>Workstation</strong> s'impose comme le cockpit central et le tableau de bord ultime du développeur nomade. Pensé pour fusionner pilotage du temps, météo en temps réel et utilitaires de code instantanés, il centralise l'ensemble des flux de l'atelier sous une charte graphique sombre et immersive.</p>

                                    <div class="news-subhead">Le Cockpit &amp; Contrôles Horaires</div>
                                    <p class="press-duo-text" style="margin-bottom: 10px;">La partie supérieure réunit les indicateurs vitaux de l'atelier : horloge analogique synchronisée, météo en direct et sélecteur de thèmes dynamiques.</p>
                                    
                                    <figure class="press-figure">
                                        <img src="images/images-workstation/01-header.png" alt="Header Workstation">
                                        <figcaption class="press-caption">Fig. 1 — En-tête, horloge et widgets de session</figcaption>
                                    </figure>

                                    <figure class="press-figure">
                                        <img src="images/images-workstation/02-racourcis.png" alt="Raccourcis web">
                                        <figcaption class="press-caption">Fig. 2 — Barre de raccourcis rapides</figcaption>
                                    </figure>

                                    <div class="news-subhead">Navigation &amp; Utilitaires de Saisie</div>
                                    <p class="press-duo-text" style="margin-bottom: 10px;">Le lanceur de projets et les générateurs intégrés (tests de polices Google Fonts, convertisseurs PX to REM) offrent une réactivité immédiate sans quitter l'interface.</p>

                                    <div class="desk-col-2">
                                        <figure class="press-figure">
                                            <img src="images/images-workstation/03-lanceur.png" alt="Lanceur de projets">
                                            <figcaption class="press-caption">Fig. 3 — Grille du lanceur de projets</figcaption>
                                        </figure>
                                        <figure class="press-figure">
                                            <img src="images/images-workstation/04-google-font-tester.png" alt="Google Font Tester">
                                            <figcaption class="press-caption">Fig. 4 — Module testeur de polices &amp; Lorem</figcaption>
                                        </figure>
                                    </div>

                                    <div class="news-subhead">Prototypage Live &amp; Playground CSS</div>
                                    <p class="press-duo-text" style="margin-bottom: 10px;">L'espace d'expérimentation permet de tester du code HTML/CSS à la volée et de manipuler les propriétés graphiques en direct.</p>

                                    <figure class="press-figure">
                                        <img src="images/images-workstation/05-codepen.png" alt="Codepen Master">
                                        <figcaption class="press-caption">Fig. 5 — Codepen Master intégré</figcaption>
                                    </figure>

                                    <figure class="press-figure">
                                        <img src="images/images-workstation/06-css-playground.png" alt="CSS Playground">
                                        <figcaption class="press-caption">Fig. 6 — Playground CSS interactif</figcaption>
                                    </figure>

                                    <div class="news-subhead">Notes Rapides &amp; Suivi de Projet</div>
                                    <p class="press-duo-text" style="margin-bottom: 10px;">Le bloc de notes persistantes et le journal de bord structurent la feuille de route et l'évolution de l'application vers un mode natif.</p>

                                    <div class="desk-col-2">
                                        <figure class="press-figure">
                                            <img src="images/images-workstation/07-notes-rapides.png" alt="Notes rapides">
                                            <figcaption class="press-caption">Fig. 7 — Bloc notes rapides</figcaption>
                                        </figure>
                                        <figure class="press-figure">
                                            <img src="images/images-workstation/08-journal-de developpement-de-ce-projet.png" alt="Journal de bord">
                                            <figcaption class="press-caption">Fig. 8 — Journal de développement et roadmap</figcaption>
                                        </figure>
                                    </div>

                                <?php elseif ($p['name'] === 'skeletor-v1.0' || $p['name'] === 'skeletor-v1.0-o2switch'): ?>
                                    <figure class="press-figure">
                                        <img src="images/capture-skeletor.png" alt="Aperçu Skeletor">
                                        <figcaption class="press-caption">Illustration — Skeletor</figcaption>
                                    </figure>
                                <?php elseif ($p['name'] === 'personator-v1.2'): ?>
                                    <figure class="press-figure">
                                        <img src="images/capture-personator.png" alt="Aperçu Personator">
                                        <figcaption class="press-caption">Illustration — Personator</figcaption>
                                    </figure>
                                <?php else: ?>
                                    <div style="height: 180px; background: #f1f3f5; border: 1px dashed #cbd5e1; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 16px;">
                                        <i class="fas fa-image" style="margin-right: 6px;">&nbsp;</i> Illustration
                                    </div>
                                <?php endif; ?>

                                <?php if ($p['name'] === 'cms-2026-v8-full' || $p['name'] === 'workstation'): ?>
                                    
                                <?php elseif ($p['name'] === 'dashboard-designer'): ?>
                                    <p class="news-pitch desk-col-2">Le module <strong>dashboard-designer</strong> redéfinit l'ergonomie de pilotage de l'atelier nomade. En fusionnant l'esthétique rédactionnelle de la grande presse et la rigueur d'un tableau de bord technique, le module permet d'orchestrer, de structurer et de visualiser l'ensemble des projets stockés sur la clé USB avec une élégance et une fluidité absolues.</p>
                                <?php elseif ($p['name'] === 'wordpress-portable'): ?>
                                    <p class="news-pitch">Instance WordPress totalement encapsulée et autonome, <strong>wordpress-portable</strong> embarque toute la puissance du CMS le plus populaire du web directement au creux de votre clé USB, sans installation lourde sur la machine hôte.</p>
                                <?php elseif ($p['name'] === 'mon-site'): ?>
                                    <p class="news-pitch">Résultat direct de l'architecture créée avec <strong>Skeletor</strong>, <strong>mon-site</strong> concrétise l'exportation du squelette pour l'afficher et le vérifier directement dans le navigateur en conditions réelles.</p>
                                <?php elseif ($p['name'] === 'user_journey-v1.0'): ?>
                                    <p class="news-pitch">Pensé pour façonner et orchestrer les parcours utilisateurs, <strong>user_journey-v1.0</strong> est le seul outil de cette clé entièrement conçu pour l'UX-UI pure. Destiné en priorité absolue aux web designers et aux spécialistes de l'expérience utilisateur, il fournit l'écosystème visuel et fonctionnel idéal pour concevoir, prototyper et évaluer les interfaces avec une finesse absolue.</p>
                                <?php elseif ($p['name'] === 'texturor'): ?>
                                    <p class="news-pitch">Conçu comme un CodePen <em>home made</em> au cœur de l'atelier, <strong>texturor</strong> est taillé pour prototyper et tester du code en un clin d'œil. Doté d'une capacité redoutable pour enregistrer et organiser tes snippets favoris, il se révèle également parfaitement responsive pour effectuer des tests et des ajustements en ligne directement depuis ton mobile.</p>
                                <?php elseif ($p['name'] === 'personator-v1.2'): ?>
                                    <p class="news-pitch">Atelier d'incarnation et de génération de profils, <strong>personator-v1.2</strong> donne vie à vos applications en peuplant instantanément vos bases ou vos maquettes avec des données utilisateur sur-mesure, réalistes et percutantes.</p>
                                <?php elseif ($p['name'] === 'modulor'): ?>
                                    <p class="news-pitch">Laboratoire visuel et interactif de l'atelier, <strong>modulor</strong> propose l'interface idéale pour tester à la volée des mises en page, expérimenter des structures d'UI et sculpter des composants en direct sans contrainte technique lourde.</p>
                                <?php elseif ($p['name'] === 'skeletor-v1.0-o2switch'): ?>
                                    <p class="news-pitch">Version dopée à la production de l'atelier, <strong>skeletor-v1.0-o2switch</strong> reprend la logique ludique et l'enregistrement de trames de son aîné pour l'ériger en rampe de lancement vers le serveur distant. Il évite les manipulations fastidieuses et sécurise d'un bloc le passage de la clé USB nomade à l'hébergeur o2switch, sans friction ni perte de temps.</p>
                                <?php elseif ($p['name'] === 'skeletor-v1.0'): ?>
                                    <p class="news-pitch">Couteau suisse du développeur nomade, <strong>skeletor-v1.0</strong> transforme la corvée des clics répétés en un jeu d'enfant. Fini les « nouveau dossier », « nouveau fichier index.php » et l'arborescence à recréer à la main : il déploie en un clin d'œil toute la trame de base indispensable pour lancer un nouveau site web, rendant la création de projets à la fois ludique, instantanée et redoutablement efficace.</p>
                                <?php elseif ($p['details'] && isset($p['details']['niveau1']) && !empty($p['details']['niveau1']['pitch'])): ?>
                                    <p class="news-pitch"><?php echo htmlspecialchars($p['details']['niveau1']['pitch'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php else: ?>
                                    <p class="news-pitch"><?php echo htmlspecialchars($p['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>

                                <?php if (!in_array($p['name'], ['cms-2026-v8-full', 'workstation']) && $p['details'] && isset($p['details']['niveau2'])): ?>
                                    <?php if (!empty($p['details']['niveau2']['contexte'])): ?>
                                        <div class="news-subhead">Contexte</div>
                                        <p><?php echo htmlspecialchars($p['details']['niveau2']['contexte'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($p['details']['niveau2']['fonctionnalites'])): ?>
                                        <div class="news-subhead">Fonctionnalités clés</div>
                                        <ul>
                                            <?php foreach (array_slice($p['details']['niveau2']['fonctionnalites'], 0, ($p['colSpan'] == 12 ? 3 : 2)) as $f): ?>
                                                <li><?php echo htmlspecialchars($f, ENT_QUOTES, 'UTF-8'); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <div class="news-article-link-container">
                                <?php if ($p['name'] === 'modulor'): ?>
                                    <a href="<?php echo htmlspecialchars($p['linkHref'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="news-article-link">Modulons ensemble !...</a>
                                <?php elseif ($p['name'] === 'personator-v1.2'): ?>
                                    <a href="<?php echo htmlspecialchars($p['linkHref'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="news-article-link">En quête de personnalité ?...</a>
                                <?php elseif ($p['name'] === 'texturor'): ?>
                                    <a href="<?php echo htmlspecialchars($p['linkHref'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="news-article-link">On refait la déco ?...</a>
                                <?php elseif (in_array($p['name'], ['cms-2026-v8-full', 'workstation'])): ?>
                                    <a href="<?php echo htmlspecialchars($p['linkHref'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="news-article-link">Lire l'article...</a>
                                <?php else: ?>
                                    <a href="<?php echo htmlspecialchars($p['linkHref'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="news-article-link">Voir le projet...</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>

                <?php if ($index === array_key_last($rows) && $bearColSpan > 0): ?>
                    <div class="news-col-<?php echo $bearColSpan; ?>">
                        <article class="news-article" style="background: #f8fafc; border: 1px dashed #cbd5e1; text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                            <div>
                                <ul style="list-style: none; padding: 0; margin: 0; font-family: -apple-system, sans-serif; font-size: 0.85rem; line-height: 1.8; color: #333;">
                                    <li><strong>Rédacteur en chef :</strong> Christophe Millot</li>
                                    <li><strong>Assistant :</strong> Gemini</li>
                                    <li><strong>Pige :</strong> Antigravity</li>
                                </ul>
                            </div>
                        </article>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if ($bearColSpan === 0): ?>
            <div class="news-colophon-footer" style="text-align: center; justify-content: center; gap: 20px;">
                <span>Rédacteur en chef : Christophe Millot | Assistant : Gemini | Pige : Antigravity</span>
            </div>
        <?php endif; ?>

        <div class="news-footer" style="text-align: center; justify-content: center; gap: 20px;">
            <span>&copy; <?php echo date('Y'); ?> Christophe Millot • Tous droits réservés</span>
            <span>•</span>
            <span>Mise à jour : <?php echo date('d/m/Y à H:i'); ?></span>
        </div>

    </div>

    <!--------------------------------------------------------------------------->
    <!--------------------------------------------------------------------------->
    <!--------------------------------------------------------------------------->
    <!--------------------------------------------------------------------------->
    <!-- SECTION V1 : MES PROJETS NOMADES (V1)                                 -->
    <!--------------------------------------------------------------------------->
    <!--------------------------------------------------------------------------->
    <!--------------------------------------------------------------------------->
    <!--------------------------------------------------------------------------->
    <!-- Menu V1 replié par défaut au refresh -->
    <details class="section-block">
        <summary>
            <span class="summary-icon">🗂️</span>
            <h2>Mes Projets Nomades (V1)</h2>
            <span class="summary-chevron">▼</span>
        </summary>
        <div class="section-body">
            <div class="grid">
                <?php if (empty($projects)): ?>
                    <p class="empty">Aucun projet trouvé dans ce dossier.</p>
                <?php else: ?>
                    <?php foreach ($projects as $p): ?>
                        <?php
                        $linkHref = '/' . rawurlencode($p['name']) . '/';
                        $jsonDetailsAttr = htmlspecialchars(json_encode($p['details']), ENT_QUOTES, 'UTF-8');
                        ?>
                        <div class="<?php echo $p['cardClass']; ?>" 
                             data-title="<?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?>"
                             data-summary="<?php echo htmlspecialchars($p['description'], ENT_QUOTES, 'UTF-8'); ?>"
                             data-img="<?php echo htmlspecialchars($p['screenshot'], ENT_QUOTES, 'UTF-8'); ?>"
                             data-details='<?php echo $jsonDetailsAttr; ?>'>
                            <div>
                                <div class="card-title"><?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <span class="<?php echo $p['badgeClass']; ?>" 
                                      data-project="<?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                      data-status="<?php echo $p['statusKey']; ?>"
                                      <?php echo !$p['isWP'] ? 'onclick="cycleStatus(this)" title="Cliquez pour changer le statut"' : ''; ?>>
                                    <?php echo $p['badgeLabel']; ?>
                                </span>
                            </div>
                            <div class="card-actions">
                                <a class="card-link" href="<?php echo $linkHref; ?>" target="_blank">Lancer le site</a>
                                <button type="button" class="btn-info" onclick="openOverlay(this)"><i class="fas fa-eye"></i> En savoir plus...</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </details>

    <hr class="separator-v2">

    <!--------------------------------------------------------------------------->
    <!--------------------------------------------------------------------------->
    <!--------------------------------------------------------------------------->
    <!--------------------------------------------------------------------------->
    <!-- SECTION V2 : LANCEUR PROJETS (V2 — CARTES ENRICHIES)                  -->
    <!--------------------------------------------------------------------------->
    <!--------------------------------------------------------------------------->
    <!--------------------------------------------------------------------------->
    <!--------------------------------------------------------------------------->
    <!-- Menu V2 replié par défaut au refresh (Cartes Enrichies) -->
    <details class="section-block">
        <summary>
            <span class="summary-icon">🚀</span>
            <h2>Lanceur Projets (V2 — Cartes Enrichies)</h2>
            <span class="summary-chevron">▼</span>
        </summary>
        <div class="section-body">
            <?php include __DIR__ . '/partials/section-v2.php'; ?>
        </div>
    </details>

    <div id="modulor-overlay">
        <button class="overlay-close" onclick="closeOverlay()"><i class="fas fa-times"></i> Fermer</button>
        <div class="overlay-container">
            <h2 id="overlay-title"></h2>
            <div id="overlay-text"></div>
            <div class="overlay-image-wrapper" id="overlay-img-container">
                <div id="overlay-media-wrapper">
                    <img id="overlay-img" src="" alt="Aperçu de la page d'accueil">
                </div>
            </div>
        </div>
    </div>

    <script>
        const statusCycle = {
            'validated':   { next: 'operational', label: '&#x1F7E0; Op&eacute;rationnel', class: 'badge badge-operational' },
            'operational': { next: 'progress',    label: '&#x1F534; En cours',        class: 'badge badge-progress' },
            'progress':    { next: 'validated',   label: '&#x1F7E2; Valid&eacute;',    class: 'badge badge-validated' }
        };

        function cycleStatus(badgeEl) {
            const projectName = badgeEl.getAttribute('data-project');
            const currentStatus = badgeEl.getAttribute('data-status');
            
            if (!statusCycle[currentStatus]) return;
            
            const nextData = statusCycle[currentStatus];
            const newStatus = nextData.next;

            badgeEl.setAttribute('data-status', newStatus);
            badgeEl.className = nextData.class;
            badgeEl.innerHTML = nextData.label;

            const formData = new FormData();
            formData.append('project', projectName);
            formData.append('status', newStatus);

            fetch('', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  if (!data.success) {
                      console.error('Erreur d’enregistrement côté serveur');
                  }
              })
              .catch(error => console.error('Erreur réseau :', error));
        }

        // Script de gestion du méga menu, accordéons mobiles animés, réorganisation et restitution active
        document.addEventListener("DOMContentLoaded", function() {
            const hamburgerBtn = document.getElementById('hamburger-menu-btn');
            const megaMenu = document.getElementById('journal-mega-menu');
            const iconSvg = document.getElementById('hamburger-icon-svg');
            const closeBtn = document.getElementById('mega-menu-close');

            if (hamburgerBtn && megaMenu && iconSvg) {
                hamburgerBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isOpen = megaMenu.classList.toggle('active');
                    
                    if (isOpen) {
                        iconSvg.innerHTML = '<line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>';
                    } else {
                        iconSvg.innerHTML = '<line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line>';
                        document.querySelectorAll('.mega-menu-col').forEach(c => {
                            c.classList.remove('open', 'is-expanded');
                        });
                    }
                });

                if (closeBtn) {
                    closeBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        megaMenu.classList.remove('active');
                        iconSvg.innerHTML = '<line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line>';
                        document.querySelectorAll('.mega-menu-col').forEach(c => {
                            c.classList.remove('open', 'is-expanded');
                        });
                    });
                }

                const menuLinks = megaMenu.querySelectorAll('.mega-menu-col a');
                menuLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        menuLinks.forEach(l => l.classList.remove('clicked'));
                        this.classList.add('clicked');
                    });
                });

                const megaMenuCols = document.querySelectorAll('.mega-menu-col');
                megaMenuCols.forEach(col => {
                    const title = col.querySelector('h4');
                    if (title) {
                        title.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const isOpen = col.classList.toggle('open');
                            
                            if (window.innerWidth <= 768) {
                                if (isOpen) {
                                    megaMenuCols.forEach(c => {
                                        if (c !== col) {
                                            c.classList.remove('open', 'is-expanded');
                                        }
                                    });
                                    col.classList.add('is-expanded');
                                } else {
                                    col.classList.remove('is-expanded');
                                }
                            }
                        });
                    }
                });

                megaMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        });

        // Fonction du switch Desktop/Mobile corrigée
        function switchEditorView(mode, btnEl) {
            const imgEl = document.getElementById('editor-demo-img');
            const captionEl = document.getElementById('editor-demo-caption');
            const container = btnEl.closest('.editor-switch-bar') || btnEl.parentElement;
            
            if (container) {
                container.querySelectorAll('.editor-switch-btn').forEach(b => b.classList.remove('active'));
            }
            btnEl.classList.add('active');

            if (imgEl) {
                if (mode === 'desktop') {
                    imgEl.src = 'images/images-cms/03-capture-cms-editeur-frame-desktop.png';
                    if (captionEl) captionEl.innerHTML = 'Fig. 3 — L\'éditeur en mode Bureau (hauteur fixe de 400px avec défilement interne)';
                } else {
                    imgEl.src = 'images/images-cms/04-capture-cms-editeur-frame-mobile.png';
                    if (captionEl) captionEl.innerHTML = 'Fig. 4 — L\'éditeur en mode Mobile (hauteur fixe de 400px avec défilement interne)';
                }
            }
        }

        // Fonction openOverlay corrigée pour cibler correctement .card
        function openOverlay(btn) {
            const card = btn.closest('.card');
            if (!card) return;
            
            const title = card.getAttribute('data-title') || '';
            const summary = card.getAttribute('data-summary') || '';
            const imgSrc = card.getAttribute('data-img') || '';
            
            const detailsRaw = card.getAttribute('data-details');
            let details = null;
            try {
                if (detailsRaw && detailsRaw !== "null") {
                    details = JSON.parse(detailsRaw);
                }
            } catch(e) {}

            document.getElementById('overlay-title').innerText = title || 'Détails';
            const containerText = document.getElementById('overlay-text');
            const mediaWrapper = document.getElementById('overlay-media-wrapper');
            const imgContainer = document.getElementById('overlay-img-container');

            let html = '';
            html += '<div class="level-block">';
            
            const titleLower = title.toLowerCase();
            if (titleLower.includes('cms-2026-v8-full')) {
                html += '<p style="font-size: 1.05em; color: #fff; font-weight: 500; margin-bottom: 12px; line-height: 1.6;">Au cœur de l\'atelier nomade se dresse <strong>cms-2026-v8-full</strong>, un système de gestion de contenu sur-mesure et ultra-léger pensé pour s\'affranchir des lourdeurs du web traditionnel. Véritable poste de pilotage administrateur, l\'application orchestre l\'initialisation des articles et le suivi des flux sans aucune base de données lourde.</p>';
            } else if (titleLower.includes('workstation')) {
                html += '<p style="font-size: 1.05em; color: #fff; font-weight: 500; margin-bottom: 12px; line-height: 1.6;">Véritable cockpit central et tableau de bord ultime, <strong>Workstation</strong> unifie le pilotage du temps, la météo en direct, le lancement rapide des applications et des outils de prototypage de code instantanés dans une interface sombre et immersive.</p>';
            } else if (titleLower.includes('dashboard-designer')) {
                html += '<p style="font-size: 1.05em; color: #fff; font-weight: 500; margin-bottom: 12px; line-height: 1.6;">Le module <strong>dashboard-designer</strong> redéfinit l\'ergonomie de pilotage de l\'atelier nomade. En fusionnant l\'esthétique rédactionnelle de la grande presse et la rigueur d\'un tableau de bord technique, le module permet d\'orchestrer, de structurer et de visualiser l\'ensemble des projets stockés sur la clé USB avec une élégance et une fluidité absolues.</p>';
            } else if (details && details.niveau1 && details.niveau1.pitch) {
                html += `<p style="font-size: 1.1em; color: #fff; font-weight: bold;">${details.niveau1.pitch}</p>`;
            } else {
                html += `<p style="font-size: 1.05em; color: #fff; line-height: 1.6;">${summary}</p>`;
            }

            if (details && details.niveau1 && details.niveau1.technos && details.niveau1.technos.length > 0) {
                html += '<div style="margin-top: 10px;">';
                details.niveau1.technos.forEach(t => {
                    html += `<span class="badge-tech">${t}</span>`;
                });
                html += '</div>';
            }
            html += '</div>';

            if (details && details.niveau2) {
                html += '<div class="level-block">';
                if (details.niveau2.contexte) {
                    html += '<h3 class="level-title">Contexte</h3>';
                    html += `<p>${details.niveau2.contexte}</p>`;
                }
                if (details.niveau2.fonctionnalites && details.niveau2.fonctionnalites.length > 1) {
                    html += '<h3 class="level-title" style="margin-top: 15px;">Fonctionnalités clés</h3><ul>';
                    details.niveau2.fonctionnalites.forEach(f => {
                        html += `<li>${f}</li>`;
                    });
                    html += '</ul>';
                }
                html += '</div>';
            }

            if (details && details.niveau3) {
                html += '<div class="level-block">';
                html += '<h3 class="level-title">Spécifications & Architecture</h3>';
                if (details.niveau3.architecture) html += `<p><strong>Architecture :</strong> ${details.niveau3.architecture}</p>`;
                if (details.niveau3.environnement) html += `<p><strong>Environnement :</strong> ${details.niveau3.environnement}</p>`;
                if (details.niveau3.roadmap) html += `<p style="margin-top: 10px;"><strong>Roadmap :</strong> ${details.niveau3.roadmap}</p>`;
                html += '</div>';
            }

            containerText.innerHTML = html;

            if (titleLower.includes('skeletor')) {
                mediaWrapper.innerHTML = `<img id="overlay-img" src="images/capture-skeletor.png" alt="Aperçu Skeletor" style="width: 100%; height: auto; border-radius: 8px; display: block;">`;
            } else if (titleLower.includes('personator')) {
                mediaWrapper.innerHTML = `<img id="overlay-img" src="images/capture-personator.png" alt="Aperçu Personator" style="width: 100%; height: auto; border-radius: 8px; display: block;">`;
            } else if (titleLower.includes('cms-2026-v8-full')) {
                mediaWrapper.innerHTML = `<img id="overlay-img" src="images/images-cms/02-desktop-capture-cms-accueil.png" alt="Poste de pilotage CMS" style="width: 100%; height: auto; border-radius: 8px; display: block;">`;
            } else if (titleLower.includes('workstation')) {
                mediaWrapper.innerHTML = `<img id="overlay-img" src="images/images-workstation/01-header.png" alt="Aperçu Workstation" style="width: 100%; height: auto; border-radius: 8px; display: block;">`;
            } else {
                let finalImgSrc = imgSrc || `dashboard-designer/assets/img/photo-640x480.png`;
                mediaWrapper.innerHTML = `<img id="overlay-img" src="${finalImgSrc}" alt="Aperçu de la page d'accueil" style="width: 100%; height: auto; border-radius: 8px; display: block;">`;
            }
            imgContainer.style.display = 'block';

            document.getElementById('modulor-overlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeOverlay() {
            document.getElementById('modulor-overlay').classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    </script>

</body>
</html>