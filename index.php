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

// Chargement global des configurations sauvegardées (current state, statuses, spans, order, presets, activePreset)
$savedConfig = [];
if (file_exists($statusesFile)) {
    $content = file_get_contents($statusesFile);
    $decoded = json_decode($content, true);
    if (is_array($decoded)) {
        $savedConfig = $decoded;
    }
}

// Auto-réparation et réindexation dynamique des presets au chargement
if (isset($savedConfig['presets']) && is_array($savedConfig['presets'])) {
    $tempPresets = [];
    foreach ($savedConfig['presets'] as $k => $data) {
        if (strpos($k, 'preset_') === 0) {
            $num = (int)$data['name'];
            if (!$num) {
                $num = (int)str_replace('preset_', '', $k);
            }
            $tempPresets[$num] = $data;
        }
    }
    ksort($tempPresets);

    $newPresets = [];
    $newIdx = 1;
    $oldActive = isset($savedConfig['active_preset']) ? (int)$savedConfig['active_preset'] : null;
    $newActive = null;
    $hasChanged = false;

    foreach ($tempPresets as $oldIdx => $data) {
        if ($newIdx > 5) break;
        $newKey = 'preset_' . $newIdx;
        if ($oldIdx !== $newIdx) {
            $hasChanged = true;
        }
        $data['name'] = 'Preset ' . $newIdx;
        $newPresets[$newKey] = $data;
        if ($oldActive === $oldIdx) {
            $newActive = $newIdx;
        }
        $newIdx++;
    }

    if ($hasChanged) {
        $savedConfig['presets'] = $newPresets;
        if ($newActive !== null) {
            $savedConfig['active_preset'] = $newActive;
        } elseif ($oldActive !== null && $oldActive >= $newIdx) {
            unset($savedConfig['active_preset']);
        }
        file_put_contents($statusesFile, json_encode($savedConfig, JSON_PRETTY_PRINT));
    }
}

// Gestion globale synchrone/AJAX de la sauvegarde et des soumissions directes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'delete_export' && isset($_POST['project'])) {
        $targetProject = basename($_POST['project']);
        $targetPath = __DIR__ . '/export/' . $targetProject;
        $response = ['success' => false];

        if (is_dir($targetPath)) {
            $deleteDirectory = function($dir) use (&$deleteDirectory) {
                $files = array_diff(scandir($dir), ['.', '..']);
                foreach ($files as $file) {
                    $path = $dir . '/' . $file;
                    is_dir($path) ? $deleteDirectory($path) : unlink($path);
                }
                return rmdir($dir);
            };
            if ($deleteDirectory($targetPath)) {
                $response['success'] = true;
            }
        }
        echo json_encode($response);
        exit;
    }

    if ($action === 'save_config' || $action === 'apply_config') {
        if (isset($_POST['preset_key'])) {
            $presetNum = (int)$_POST['preset_key'];
            if (!isset($savedConfig['presets']) || !is_array($savedConfig['presets'])) {
                $savedConfig['presets'] = [];
            }
            
            if (isset($_POST['purge']) && $_POST['purge'] === '1') {
                $targetKey = 'preset_' . $presetNum;
                unset($savedConfig['presets'][$targetKey]);
            } else {
                $presetKey = 'preset_' . $presetNum;
                $presetPayload = [];
                if (isset($_POST['spans']) && is_array($_POST['spans'])) {
                    $presetPayload['spans'] = $_POST['spans'];
                }
                if (isset($_POST['order']) && is_array($_POST['order'])) {
                    $presetPayload['order'] = $_POST['order'];
                }
                $presetPayload['name'] = 'Preset ' . $presetNum;
                $savedConfig['presets'][$presetKey] = $presetPayload;
                $savedConfig['active_preset'] = $presetNum;
            }

            $tempPresets = [];
            for ($i = 1; $i <= 5; $i++) {
                $k = 'preset_' . $i;
                if (isset($savedConfig['presets'][$k])) {
                    $tempPresets[$i] = $savedConfig['presets'][$k];
                }
            }
            
            $newPresets = [];
            $newIdx = 1;
            $newActivePreset = null;
            $oldActivePreset = isset($savedConfig['active_preset']) ? (int)$savedConfig['active_preset'] : null;

            foreach ($tempPresets as $oldIdx => $pData) {
                $newKey = 'preset_' . $newIdx;
                $pData['name'] = 'Preset ' . $newIdx;
                $newPresets[$newKey] = $pData;

                if ($oldActivePreset === $oldIdx) {
                    $newActivePreset = $newIdx;
                }
                $newIdx++;
            }
            
            $savedConfig['presets'] = $newPresets;

            if (isset($_POST['purge']) && $_POST['purge'] === '1') {
                unset($savedConfig['active_preset']);
            } else {
                if ($newActivePreset !== null) {
                    $savedConfig['active_preset'] = $newActivePreset;
                }
            }
        } else {
            if (isset($_POST['statuses']) && is_array($_POST['statuses'])) {
                $savedConfig['statuses'] = $_POST['statuses'];
            }
            if (isset($_POST['spans']) && is_array($_POST['spans'])) {
                $savedConfig['spans'] = $_POST['spans'];
            }
            if (isset($_POST['order']) && is_array($_POST['order'])) {
                $savedConfig['order'] = $_POST['order'];
            }
            if (isset($savedConfig['active_preset'])) {
                $activeNum = (int)$savedConfig['active_preset'];
                $activeKey = 'preset_' . $activeNum;
                if (isset($savedConfig['presets'][$activeKey])) {
                    if (isset($_POST['spans'])) $savedConfig['presets'][$activeKey]['spans'] = $_POST['spans'];
                    if (isset($_POST['order'])) $savedConfig['presets'][$activeKey]['order'] = $_POST['order'];
                }
            }
        }
        
        file_put_contents($statusesFile, json_encode($savedConfig, JSON_PRETTY_PRINT));
        
        if ($action === 'apply_config') {
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }

        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'set_active_preset') {
        if (isset($_POST['preset_key'])) {
            $savedConfig['active_preset'] = (int)$_POST['preset_key'];
            file_put_contents($statusesFile, json_encode($savedConfig, JSON_PRETTY_PRINT));
        }
        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'reset_config') {
        if (isset($savedConfig['order'])) unset($savedConfig['order']);
        if (isset($savedConfig['spans'])) unset($savedConfig['spans']);
        if (isset($savedConfig['active_preset'])) unset($savedConfig['active_preset']);
        
        file_put_contents($statusesFile, json_encode($savedConfig, JSON_PRETTY_PRINT));
        echo json_encode(['success' => true]);
        exit;
    }
}

// Textes éditoriaux de rédaction pour les projets du journal
$editorialDescriptions = [
    'la-centrale' => "Module de conception et de gestion centralisée des palettes de couleurs. Conçu pour unifier l'identité visuelle de l'atelier, il permet de calibrer, tester et exporter des harmonies chromatiques sur-mesure directement depuis l'environnement nomade.",
    'cms-2026-v8-full' => "Architecture CMS modulaire de nouvelle génération. Pensée pour offrir une performance maximale sans dépendance au cloud distant, elle garantit une autonomie éditoriale totale au cœur d'une structure flat-file ultra-sécurisée.",
    'palettor' => "Laboratoire d'extraction et d'analyse chromatique. Cet utilitaire de design dissèque les nuances pour générer instantanément des chartes graphiques cohérentes prêtes à l'intégration.",
    'modulor' => "Système de grille et de mise en page proportionnelle. Inspiré des canons architecturaux classiques, modulor structure l'espace visuel avec une rigueur mathématique irréprochable.",
    'skeletor-v1.0' => "Squelette de démarrage rapide et minimaliste pour applications web agiles. Fournit une base saine, propre et épurée pour prototyper sans contrainte superflue.",
    'personator-v1.2' => "Générateur de profils, de données factices et de personas pour les phases de test d'ergonomie et de parcours utilisateurs en conditions réelles.",
    'texturor' => "Boîte à outils dédiée au traitement typographique, à la gestion des interlignages et à l'optimisation de la lisibilité textuelle sur tous les supports d'affichage.",
    'user_journey-v1.0' => "Cartographie interactive des parcours clients et des flux de navigation. Un outil de pilotage stratégique pour anticiper chaque étape de l'expérience utilisateur.",
    'wordpress-portable' => "Environnement WordPress complètement virtualisé et autonome embarqué sur support amovible, garantissant un fonctionnement hors-ligne instantané.",
    'pixelart' => "Studio créatif rétro-numérique pour la conception de graphismes pixelisés et d'éléments d'interface vintage au charme intemporel."
];

$files = @scandir($dir);
$projectsRaw = [];
$projects = [];

if (is_array($files)) {
    $exclude = ['.git', 'core', 'server', 'Data', 'sql', 'mac-server-runtime', 'mac-tools', 'static', 'projet-client', 'partials', 'mon-premier-site', 'dashboard-designer', 'images', 'export', '_pixelart', 'skeletor-v1.0-o2switch'];

    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || !is_dir($dir . '/' . $file)) continue;
        if (in_array($file, $exclude)) continue;

        $hasIndex = file_exists($dir . '/' . $file . '/index.php') || file_exists($dir . '/' . $file . '/index.html');
        $isWP = file_exists($dir . '/' . $file . '/wp-config.php');

        $title = $file;
        $lowerFile = mb_strtolower($file);
        
        $customDetails = isset($projectsDetailsMap[$lowerFile]) ? $projectsDetailsMap[$lowerFile] : null;

        if (isset($editorialDescriptions[$lowerFile])) {
            $description = $editorialDescriptions[$lowerFile];
        } else {
            $description = "Chronique et analyse technique approfondie du module " . htmlspecialchars($file, ENT_QUOTES, 'UTF-8') . ", développé dans le cadre de l'atelier de nomadisme numérique.";
        }
        
        $imgName = 'photo-640x480.png';
        if ($customDetails && isset($customDetails['details']['niveau1']['image']) && !empty($customDetails['details']['niveau1']['image'])) {
            $imgName = basename($customDetails['details']['niveau1']['image']);
        }
        
        $screenshot = 'dashboard-designer/assets/img/' . $imgName;
        if ($lowerFile === 'cms-2026-v8-full') {
            $screenshot = 'images/images-cms/01-desktop-capture-cms-hero.png';
        }

        $savedStatuses = isset($savedConfig['statuses']) ? $savedConfig['statuses'] : [];
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

        $savedSpans = isset($savedConfig['spans']) ? $savedConfig['spans'] : [];
        if (isset($savedSpans[$file])) {
            $colSpan = (int)$savedSpans[$file];
        } else {
            $colSpan = 6;
        }

        $sizeLabel = ($colSpan === 12) ? 'CMS' : 'UX-UI';

        $projectsRaw[$lowerFile] = [
            'name' => $file,
            'title' => $title,
            'hasIndex' => true,
            'isWP' => $isWP,
            'description' => $description,
            'screenshot' => $screenshot,
            'statusKey' => $statusKey,
            'badgeLabel' => $badgeLabel,
            'badgeClass' => $badgeClass,
            'cardClass' => 'card',
            'details' => $customDetails ? $customDetails['details'] : null,
            'colSpan' => (int)$colSpan,
            'colClass' => 'news-col-' . (int)$colSpan,
            'sizeLabel' => $sizeLabel,
            'linkHref' => rawurlencode($file) . '/'
        ];
    }
}

$wsSpan = (isset($savedConfig['spans']['workstation'])) ? (int)$savedConfig['spans']['workstation'] : 6;
$projectsRaw['workstation'] = [
    'name' => 'workstation',
    'title' => 'Workstation',
    'hasIndex' => true,
    'isWP' => false,
    'description' => "Véritable cockpit central et tableau de bord ultime, Workstation unifie le pilotage du temps, la météo en direct et les outils de prototypage de l'atelier nomade. Conçu pour une maîtrise totale du flux de travail.",
    'screenshot' => 'images/images-workstation/01-header.png',
    'statusKey' => 'operational',
    'badgeLabel' => '&#x1F7E0; Op&eacute;rationnel',
    'badgeClass' => 'badge badge-operational',
    'cardClass' => 'card',
    'details' => [
        'niveau1' => [
            'pitch' => "Véritable cockpit central et tableau de bord ultime, <strong>Workstation</strong> unifie le pilotage du temps, la météo en direct et les outils de prototypage.",
            'technos' => ['PHP', 'JavaScript', 'CSS Custom'],
            'image' => '01-header.png'
        ]
    ],
    'colSpan' => $wsSpan,
    'colClass' => 'news-col-' . $wsSpan,
    'sizeLabel' => 'SYSTEM',
    'linkHref' => '#'
];

$orderedKeys = isset($savedConfig['order']) && is_array($savedConfig['order']) ? $savedConfig['order'] : [
    'workstation', 'la-centrale', 'cms-2026-v8-full', 'palettor', 'modulor',
    'skeletor-v1.0', 'personator-v1.2', 'texturor',
    'user_journey-v1.0', 'wordpress-portable', 'pixelart'
];

foreach ($orderedKeys as $key) {
    $lowerKey = mb_strtolower($key);
    foreach ($projectsRaw as $pKey => $pVal) {
        if (mb_strtolower($pVal['name']) === $lowerKey) {
            $projects[] = $pVal;
            unset($projectsRaw[$pKey]);
            break;
        }
    }
}
foreach ($projectsRaw as $extraP) {
    $projects[] = $extraP;
}

$lastRowSpan = 0;
foreach ($projects as $item) {
    $lastRowSpan += (int)$item['colSpan'];
}
$colophonSpan = max(0, 12 - ($lastRowSpan % 12));
if ($colophonSpan === 0) {
    $colophonSpan = 12;
}

$presets = isset($savedConfig['presets']) && is_array($savedConfig['presets']) ? $savedConfig['presets'] : [];
$activePreset = isset($savedConfig['active_preset']) ? (int)$savedConfig['active_preset'] : 0;
$isExportMode = (isset($_GET['mode']) && $_GET['mode'] === 'export');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEV NOMADE - Dashboard</title>

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
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #fdfbf7;
            border-bottom: 2px solid #2b2b2b;
            padding: 10px 30px;
            box-sizing: border-box;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 99999;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transform: translateY(-100%);
            transition: transform 0.3s ease-in-out;
        }
        .sticky-header-bar.visible {
            transform: translateY(0);
        }
        .sticky-header-brand {
            font-family: Georgia, serif;
            font-size: 1.1rem;
            font-weight: 900;
            text-transform: uppercase;
            color: #2b2b2b;
            letter-spacing: -0.5px;
        }
        .sticky-header-hamburger {
            cursor: pointer;
            display: flex;
            align-items: center;
            background: #2b2b2b;
            color: #fff;
            padding: 6px 10px;
            border-radius: 6px;
        }

        h1 {
            font-size: 1.8rem;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--card-bg);
            padding-bottom: 15px;
        }

        .separator-v2 { border: none; height: 6px; background-color: var(--accent); margin: 40px 0; opacity: 0.8; border-radius: 3px; }

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
            background: #FDFBF7 !important;
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
        
        body.is-scrolled .news-header-wrapper .journal-mega-menu.active {
            position: fixed !important;
            top: 48px !important;
            left: 30px !important;
            right: 30px !important;
            max-width: none !important;
            box-sizing: border-box !important;
        }
        
        .mega-menu-close-btn { display: none; }

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
        .mega-menu-col ul { list-style: none; margin: 0; padding: 0; }
        .mega-menu-col li { margin-bottom: 8px; }
        .mega-menu-col a {
            color: #2b2b2b;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            display: block;
            padding: 6px 0 6px 12px;
            transition: color 0.15s ease, background-color 0.15s ease;
        }
        .mega-menu-col a:hover {
            color: #ffffff !important;
            background-color: #555555 !important;
            text-decoration: none !important;
            padding-left: 12px !important;
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

        .news-grid-container {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 24px;
            margin-bottom: 24px;
        }

        .news-col-12 { grid-column: span 12; }
        .news-col-11 { grid-column: span 11; }
        .news-col-10 { grid-column: span 10; }
        .news-col-9  { grid-column: span 9; }
        .news-col-8  { grid-column: span 8; }
        .news-col-7  { grid-column: span 7; }
        .news-col-6  { grid-column: span 6; }
        .news-col-5  { grid-column: span 5; }
        .news-col-4  { grid-column: span 4; }

        @media (max-width: 1024px) {
            .news-col-12, .news-col-11, .news-col-10, .news-col-9, .news-col-8, .news-col-7, .news-col-6, .news-col-5, .news-col-4 { grid-column: span 12; }
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
            max-height: 220px;
            object-fit: contain;
            background: #f8fafc;
            display: block;
            border: 1px solid #dcd7ce;
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

        .news-article p.news-pitch::first-letter {
            font-size: 2.2rem;
            float: left;
            line-height: 0.85;
            padding-right: 6px;
            font-weight: bold;
            color: #c0392b;
            font-family: Georgia, serif;
        }
        .news-article p.news-pitch {
            font-size: 0.88rem;
            line-height: 1.5;
            margin: 0 0 10px 0;
            text-align: justify;
            color: #333333;
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
            grid-column: span 12;
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

        <?php if (!$isExportMode): ?>
        /* =========================================================================
            CONTROL PANEL — Volet latéral escamotable
           ========================================================================= */
        #cp-toggle-btn {
            position: fixed; bottom: 30px; right: 30px; z-index: 88888;
            width: 52px; height: 52px; border-radius: 50%; background: #2b2b2b;
            color: #fdfbf7; border: 2px solid #555; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 16px rgba(0,0,0,0.45); font-size: 1.2rem;
            transition: background 0.2s, transform 0.2s;
        }
        #cp-toggle-btn:hover { background: #c0392b; border-color: #c0392b; transform: scale(1.08); }
        #cp-toggle-btn.cp-open { background: #c0392b; border-color: #c0392b; }

        #cp-backdrop {
            position: fixed; inset: 0; background: rgba(0,0,0,0.35); z-index: 88889;
            opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
        }
        #cp-backdrop.cp-visible { opacity: 1; pointer-events: auto; }

        #control-panel {
            position: fixed; top: 0; right: 0; width: 330px; max-width: 92vw; height: 100vh;
            background: #fdfbf7; border-left: 2px solid #2b2b2b; z-index: 88890;
            transform: translateX(100%); transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex; flex-direction: column; overflow: hidden; font-family: -apple-system, sans-serif;
        }
        #control-panel.cp-open { transform: translateX(0); box-shadow: -6px 0 30px rgba(0,0,0,0.3); }

        .cp-header { background: #2b2b2b; color: #fdfbf7; padding: 16px 18px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
        .cp-header-title { font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; }
        .cp-header-subtitle { font-size: 0.65rem; color: #aaa; text-transform: uppercase; letter-spacing: 1px; margin-top: 3px; }
        .cp-close-btn { background: none; border: 1px solid #555; color: #fff; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .cp-close-btn:hover { background: #c0392b; border-color: #c0392b; }

        .cp-actions { padding: 12px 18px; display: flex; gap: 8px; border-bottom: 1px solid #e2ddd5; background: #f5f0e8; flex-shrink: 0; }
        .cp-action-btn {
            flex: 1; padding: 8px 6px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
            border-radius: 4px; cursor: pointer; border: 1px solid #2b2b2b; background: #fff; color: #2b2b2b;
            transition: background 0.2s, color 0.2s;
        }
        .cp-action-btn:hover { background: #2b2b2b; color: #fff; }
        .cp-action-btn.cp-btn-primary { background: #2b2b2b; color: #fff; }
        .cp-action-btn.cp-btn-primary:hover { background: #c0392b; border-color: #c0392b; }
        .cp-action-btn.cp-btn-danger { border-color: #c0392b; color: #c0392b; }
        .cp-action-btn.cp-btn-danger:hover { background: #c0392b; color: #fff; }

        .cp-presets-section { padding: 12px 18px; background: #efe9df; border-bottom: 1px solid #e2ddd5; flex-shrink: 0; }
        .cp-presets-title { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: #c0392b; margin-bottom: 8px; }
        .cp-presets-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; }
        .cp-preset-box {
            background: #fff; border: 1px solid #cbd5e1; height: 36px; font-size: 0.95rem; font-weight: 800;
            border-radius: 4px; cursor: pointer; color: #2b2b2b; display: flex; align-items: center; justify-content: center;
        }
        .cp-preset-box.empty { color: #94a3b8; border-style: dashed; background: #f8fafc; font-size: 1.2rem; }
        .cp-preset-box.active-preset { border: 2px solid #2b2b2b !important; box-shadow: 0 0 0 1px #2b2b2b; }
        .cp-preset-box:hover { background: #2b2b2b; color: #fff; border-color: #2b2b2b; }
        .cp-presets-hint { font-size: 0.65rem; color: #777; font-style: italic; margin-top: 6px; text-align: center; }

        .cp-body { flex: 1; overflow-y: auto; padding: 14px 18px; }
        .cp-project-row { display: flex; align-items: center; gap: 8px; padding: 9px 0; border-bottom: 1px dashed #e2ddd5; }
        .cp-project-row:hover { background: #f5f0e8; padding-left: 4px; padding-right: 4px; }

        .cp-toggle { position: relative; width: 34px; height: 18px; flex-shrink: 0; }
        .cp-toggle input { opacity: 0; width: 0; height: 0; position: absolute; }
        .cp-toggle-slider { position: absolute; inset: 0; background: #cbd5e1; border-radius: 20px; cursor: pointer; transition: background 0.2s; }
        .cp-toggle-slider::before {
            content: ''; position: absolute; width: 12px; height: 12px; border-radius: 50%;
            background: #fff; top: 3px; left: 3px; transition: transform 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.25);
        }
        .cp-toggle input:checked + .cp-toggle-slider { background: #2b2b2b; }
        .cp-toggle input:checked + .cp-toggle-slider::before { transform: translateX(16px); }

        .cp-project-name { font-size: 0.78rem; font-weight: 600; color: #2b2b2b; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .cp-project-name.cp-hidden-label { color: #aaa; text-decoration: line-through; }

        .cp-size-select { font-size: 0.7rem; font-weight: 700; background: #fff; border: 1px solid #cbd5e1; color: #333; padding: 2px 4px; border-radius: 3px; cursor: pointer; }
        .cp-move-group { display: flex; flex-direction: column; gap: 2px; flex-shrink: 0; }
        .cp-move-btn {
            background: #fff; border: 1px solid #cbd5e1; color: #333; width: 18px; height: 16px;
            font-size: 9px; font-weight: bold; display: flex; align-items: center; justify-content: center; cursor: pointer; border-radius: 2px;
        }
        .cp-move-btn:hover { background: #2b2b2b; color: #fff; border-color: #2b2b2b; }

        .cp-footer { padding: 12px 18px; border-top: 1px solid #e2ddd5; background: #f5f0e8; flex-shrink: 0; }
        .cp-apply-btn {
            width: 100%; background: #2b2b2b; color: #fdfbf7; border: 1px solid #2b2b2b; padding: 10px;
            font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;
            border-radius: 4px; cursor: pointer; text-align: center; text-decoration: none; display: block; box-sizing: border-box;
        }
        .cp-apply-btn:hover { background: #c0392b; border-color: #c0392b; }
        <?php endif; ?>

        .news-col-hidden { display: none !important; }

        /* Marges totalement à 0 pour coller le haut de la page sur mobile portrait et paysage */
        @media (max-width: 1024px) and (orientation: portrait) {
            body {
                margin-top: 0px !important;
                padding-top: 0px !important;
            }
        }
        @media (max-width: 1024px) and (orientation: landscape) {
            body {
                margin-top: 0px !important;
                padding-top: 0px !important;
            }
        }

        @media (max-width: 1024px) {
            body {
                padding-left: 0 !important;
                padding-right: 0 !important;
                padding-bottom: 0 !important;
            }
            .news-sheet {
                padding: 15px !important;
                margin: 0 !important;
                width: 100% !important;
                border-radius: 0 !important;
                border-left: none !important;
                border-right: none !important;
                box-sizing: border-box !important;
            }
            .news-bandeau {
                font-size: 0.9rem !important;
                letter-spacing: 2px !important;
            }
            .news-header-grid {
                display: flex !important;
                flex-direction: column !important;
                gap: 15px !important;
                padding-bottom: 15px !important;
                text-align: center !important;
            }
            .news-header-grid > div:nth-child(1) {
                text-align: center !important;
                font-size: 0.9rem !important;
            }
            .news-header-grid > div:nth-child(2) {
                text-align: center !important;
            }
            .news-manchette {
                font-size: 2.4rem !important;
            }
            .news-header-grid > div:nth-child(3) {
                align-items: center !important;
                text-align: center !important;
                font-size: 0.9rem !important;
            }
            .news-header-grid > div:nth-child(3) > div:last-child {
                display: flex !important;
                justify-content: center !important;
                width: 100% !important;
                margin-top: 8px !important;
            }

            /* Paragraphes alignés et justifiés à gauche dans les deux modes mobiles */
            .news-tribune p,
            .news-article p.news-pitch {
                text-align: left !important;
            }

            .news-tribune p {
                font-size: 1.25rem !important;
                line-height: 1.6 !important;
            }

            .news-article h4 {
                font-size: 1.5rem !important;
            }

            .news-article p.news-pitch {
                font-size: 1.2rem !important;
                line-height: 1.6 !important;
            }
            .news-article p.news-pitch::first-letter {
                font-size: 3.4rem !important;
            }

            /* Étirement complet des images sur toute la largeur de l'écran en mobile */
            .press-figure {
                margin-left: -15px !important;
                margin-right: -15px !important;
                border-left: none !important;
                border-right: none !important;
                padding: 4px 0 !important;
            }
            .press-figure img {
                max-height: none !important;
                width: 100% !important;
                height: auto !important;
                object-fit: cover !important;
                border-left: none !important;
                border-right: none !important;
            }

            .press-caption {
                font-size: 0.9rem !important;
                padding: 0 15px;
            }

            .news-article-link {
                font-size: 1rem !important;
                font-weight: bold;
            }

            /* Refonte du footer en mode mobile */
            .news-footer {
                flex-direction: column !important;
                gap: 8px !important;
                align-items: center !important;
                text-align: center !important;
                font-size: 0.85rem !important;
            }
            .news-footer span:nth-child(2) {
                display: none !important;
            }

            .journal-mega-menu {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
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
                margin: 0 !important;
            }
            .journal-mega-menu.active {
                display: flex !important;
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
                background: #fdfbf7;
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
            }
        }
    </style>
</head>
<body>

    <div class="sticky-header-bar" id="sticky-header">
        <div class="sticky-header-brand">L'Atelier Numérique</div>
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
                    <h2 class="news-manchette">L'Atelier Numérique</h2>
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

            <div id="journal-mega-menu" class="journal-mega-menu">
                <div class="mega-menu-close-btn">
                    <button type="button" id="mega-menu-close">Fermer</button>
                </div>
                <?php
                // Détermine l'extension des pages de détail selon le contexte (local vs export statique)
                $detailPageExt = (defined('FIREBASE_STATIC') && FIREBASE_STATIC) ? 'detail.html' : 'detail.php';
                // Liste des projets dans chaque colonne du menu (slug => label)
                $menuCol1 = ['workstation' => 'Workstation', 'la-centrale' => 'la-centrale', 'cms-2026-v8-full' => 'cms-2026-v8-full'];
                $menuCol2 = ['palettor' => 'palettor', 'modulor' => 'modulor', 'texturor' => 'texturor', 'personator-v1.2' => 'personator-v1.2', 'pixelart' => 'pixelart', 'user_journey-v1.0' => 'user_journey-v1.0'];
                $menuCol3 = ['skeletor-v1.0' => 'skeletor-v1.0', 'wordpress-portable' => 'wordpress-portable'];
                ?>
                <div class="mega-menu-grid">
                    <!-- Colonne 1 : Pilotage & Structure -->
                    <div class="mega-menu-col">
                        <h4>Pilotage &amp; Structure</h4>
                        <ul>
                            <?php foreach ($menuCol1 as $mSlug => $mLabel): ?>
                                <li><a href="<?php echo rawurlencode($mSlug) . '/' . $detailPageExt; ?>"><?php echo htmlspecialchars($mLabel, ENT_QUOTES, 'UTF-8'); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <!-- Colonne 2 : Outils Créatifs & Design -->
                    <div class="mega-menu-col">
                        <h4>Outils Créatifs &amp; Design</h4>
                        <ul>
                            <?php foreach ($menuCol2 as $mSlug => $mLabel): ?>
                                <li><a href="<?php echo rawurlencode($mSlug) . '/' . $detailPageExt; ?>"><?php echo htmlspecialchars($mLabel, ENT_QUOTES, 'UTF-8'); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <!-- Colonne 3 : Templates & Environnements -->
                    <div class="mega-menu-col">
                        <h4>Templates &amp; Environnements</h4>
                        <ul>
                            <?php foreach ($menuCol3 as $mSlug => $mLabel): ?>
                                <li><a href="<?php echo rawurlencode($mSlug) . '/' . $detailPageExt; ?>"><?php echo htmlspecialchars($mLabel, ENT_QUOTES, 'UTF-8'); ?></a></li>
                            <?php endforeach; ?>
                            <li><a href="#">À propos</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="news-tribune">
            <h3>Tribune Libre — L'Affranchissement du Cloud</h3>
            <p>« S'affranchir des infrastructures distantes pour recentrer le développement web sur l'essentiel : la maîtrise absolue du code, de l'octet initial jusqu'au déploiement final, au creux d'un support de poche inaltérable. »</p>
        </div>

        <!-- GRILLE DES ARTICLES LÉGERS (GÉRÉE PAR LE CONTROL PANEL) -->
        <div class="news-grid-container" id="news-grid-container">
            <?php foreach ($projects as $p): ?>
                <?php $linkHref = rawurlencode($p['name']) . '/'; ?>
                <div class="<?php echo htmlspecialchars($p['colClass'], ENT_QUOTES, 'UTF-8'); ?>" data-project-name="<?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>">
                    <article class="news-article">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: baseline; border-bottom: 2px solid #111; padding-bottom: 4px; margin-bottom: 8px;">
                                <h4 style="margin: 0; border: none; padding: 0; font-size: 1.25rem;"><?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                <span style="font-family: -apple-system, sans-serif; font-size: 0.65rem; text-transform: uppercase; color: #777;"><?php echo htmlspecialchars($p['sizeLabel'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>

                            <figure class="press-figure">
                                <img src="<?php echo htmlspecialchars($p['screenshot'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                <figcaption class="press-caption">Aperçu — <?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?></figcaption>
                            </figure>

                            <p class="news-pitch"><?php echo htmlspecialchars($p['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>

                        <div class="news-article-link-container">
                            <a href="<?php echo $linkHref; ?>" class="news-article-link" target="_blank">Voir le projet &rarr;</a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>

            <div class="news-col-<?php echo $colophonSpan; ?>" id="bear-col-block" data-bear-span="<?php echo $colophonSpan; ?>">
                <article class="news-article" style="background: #f8fafc; border: 1px dashed #cbd5e1; text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%;">
                    <div>
                        <ul style="list-style: none; padding: 0; margin: 0; font-family: -apple-system, sans-serif; font-size: 0.85rem; line-height: 1.8; color: #333;">
                            <li><strong>Rédacteur en chef :</strong> Christophe Millot</li>
                            <li><strong>Assistant :</strong> Gemini</li>
                            <li><strong>Pige :</strong> Antigravity</li>
                        </ul>
                    </div>
                </article>
            </div>
        </div>

        <div class="news-footer">
            <span>&copy; <?php echo date('Y'); ?> Christophe Millot • Tous droits réservés</span>
            <span>•</span>
            <span>Mise à jour : <?php echo date('d/m/Y à H:i'); ?></span>
        </div>
    </div>

    <!-- CONTROL PANEL — CONSERVÉ ET TOTALEMENT FONCTIONNEL -->
    <?php if (!$isExportMode): ?>
    <button id="cp-toggle-btn" title="Control Panel — Affichage des projets" aria-label="Ouvrir le Control Panel" aria-expanded="false" aria-controls="control-panel">
        &#9776;
    </button>

    <div id="cp-backdrop" aria-hidden="true"></div>

    <aside id="control-panel" role="complementary" aria-label="Control Panel — Visibilité des projets">
        <div class="cp-header">
            <div>
                <div class="cp-header-title">&#9881; Control Panel</div>
                <div class="cp-header-subtitle">Affichage des projets</div>
            </div>
            <button class="cp-close-btn" id="cp-close-btn" title="Fermer le panneau">&times;</button>
        </div>

        <div class="cp-actions">
            <button class="cp-action-btn cp-btn-primary" id="cp-save-btn" type="button">&#10003; Save</button>
            <button class="cp-action-btn cp-btn-danger" id="cp-reset-btn" type="button">&#8635; Reset</button>
        </div>

        <div class="cp-presets-section">
            <div class="cp-presets-title">Presets</div>
            <div class="cp-presets-grid" id="cp-presets-grid">
                <?php for ($n = 1; $n <= 5; $n++): ?>
                    <?php 
                    $presetKey = 'preset_' . $n;
                    $isStored = isset($presets[$presetKey]);
                    $classes = ['cp-preset-box'];
                    if (!$isStored) $classes[] = 'empty';
                    if ($activePreset === $n) $classes[] = 'active-preset';
                    $boxClass = implode(' ', $classes);
                    $boxLabel = $isStored ? $n : '+';
                    $boxTitle = $isStored ? "Preset $n. Clic simple pour charger, Shift + Clic pour enregistrer/écraser, Ctrl + Clic pour supprimer." : "Preset $n vide. Clic simple pour enregistrer ici.";
                    ?>
                    <button type="button" class="<?php echo $boxClass; ?>" data-preset-num="<?php echo $n; ?>" title="<?php echo $boxTitle; ?>">
                        <?php echo $boxLabel; ?>
                    </button>
                <?php endfor; ?>
            </div>
            <div class="cp-presets-hint">clic pour enregistrer si vide, ctrl+clic pour supprimer.</div>
        </div>

        <div class="cp-body" id="cp-body">
            <div style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: #c0392b; border-bottom: 1px solid #e2ddd5; padding-bottom: 5px; margin-bottom: 10px;">Projets du Journal</div>
            <?php foreach ($projects as $p): ?>
            <div class="cp-project-row" data-cp-row="<?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>">
                <label class="cp-toggle" title="Afficher / masquer <?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input
                        type="checkbox"
                        class="cp-checkbox"
                        data-project="<?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>"
                        checked
                        onchange="cpToggleProject('<?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>', this.closest('.cp-project-row'))"
                    >
                    <span class="cp-toggle-slider"></span>
                </label>
                <span class="cp-project-name" id="cp-label-<?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>" onclick="cpToggleProject('<?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>', this.closest('.cp-project-row'))">
                    <?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <select class="cp-size-select" data-project-size="<?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>" title="Choisir le nombre de colonnes">
                    <?php for ($i = 4; $i <= 12; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo ($p['colSpan'] === $i) ? 'selected' : ''; ?>><?php echo $i; ?> col</option>
                    <?php endfor; ?>
                </select>
                <div class="cp-move-group">
                    <button type="button" class="cp-move-btn" onclick="cpMoveProject('<?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>', -1)" title="Monter l'article">▲</button>
                    <button type="button" class="cp-move-btn" onclick="cpMoveProject('<?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>', 1)" title="Descendre l'article">▼</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="cp-footer">
            <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                <a href="export/o2switch/" target="_blank" class="cp-apply-btn" style="background: #38bdf8; color: #0f172a; border-color: #38bdf8; text-align: center; text-decoration: none; line-height: normal; display: flex; align-items: center; justify-content: center;">Voir le site</a>
                <button class="cp-apply-btn" id="cp-export-journal-btn" type="button" style="background: #38bdf8; color: #0f172a; border-color: #38bdf8;" onclick="confirmExportJournal()">Exporter</button>
            </div>
            <button class="cp-apply-btn" id="cp-apply-btn" type="button">Apply</button>
        </div>
    </aside>
    <?php endif; ?>

    <!-- JAVASCRIPT DE GESTION DU MENU ET DU CONTROL PANEL -->
    <script>
        window.addEventListener('orientationchange', function() {
            const resetScroll = () => {
                window.scrollTo(0, 0);
                document.body.scrollTop = 0;
                document.documentElement.scrollTop = 0;
            };
            setTimeout(resetScroll, 50);
            setTimeout(resetScroll, 150);
            setTimeout(resetScroll, 350);
        });

        const clientPresets = <?php echo json_encode($presets); ?>;

        function applyPresetToDOM(presetKey) {
            const preset = clientPresets[presetKey];
            if (!preset) return false;

            const container = document.getElementById('news-grid-container');
            const cpBody = document.getElementById('cp-body');
            const bearBlock = document.getElementById('bear-col-block');

            if (preset.order) {
                const orderArr = Array.isArray(preset.order) ? preset.order : Object.values(preset.order);
                const gridItems = Array.from(container.querySelectorAll('[data-project-name]'));
                const cpRows = cpBody ? Array.from(cpBody.querySelectorAll('.cp-project-row')) : [];
                
                const sortFn = (a, b) => {
                    const nameA = a.getAttribute('data-project-name') || a.getAttribute('data-cp-row');
                    const nameB = b.getAttribute('data-project-name') || b.getAttribute('data-cp-row');
                    const idxA = orderArr.indexOf(nameA);
                    const idxB = orderArr.indexOf(nameB);
                    
                    if (idxA !== -1 && idxB !== -1) return idxA - idxB;
                    if (idxA !== -1) return -1;
                    if (idxB !== -1) return 1;
                    return 0;
                };

                gridItems.sort(sortFn);
                if (cpRows.length > 0) cpRows.sort(sortFn);

                gridItems.forEach(item => {
                    if (bearBlock) {
                        container.insertBefore(item, bearBlock);
                    } else {
                        container.appendChild(item);
                    }
                });

                if (cpBody && cpRows.length > 0) {
                    cpRows.forEach(row => cpBody.appendChild(row));
                }
            }

            if (preset.spans) {
                for (const [pName, spanVal] of Object.entries(preset.spans)) {
                    const sel = cpBody ? cpBody.querySelector('[data-project-size="' + CSS.escape(pName) + '"]') : null;
                    if (sel) {
                        sel.value = spanVal;
                    }
                    document.querySelectorAll('.news-grid-container [data-project-name="' + CSS.escape(pName) + '"]').forEach(col => {
                        col.className = col.className.replace(/news-col-\d+/, '').trim();
                        col.classList.add('news-col-' + spanVal);
                    });
                }
            }

            updateBearSpan();
            return true;
        }

        (function() {
            const serverActivePreset = <?php echo $activePreset ? $activePreset : 'null'; ?>;
            if (serverActivePreset) {
                applyPresetToDOM('preset_' + serverActivePreset);
            }
        })();

        document.addEventListener("DOMContentLoaded", function() {
            const hamburgerBtn = document.getElementById('hamburger-menu-btn');
            const stickyBtn = document.getElementById('sticky-hamburger-btn');
            const megaMenu = document.getElementById('journal-mega-menu');
            const iconSvg = document.getElementById('hamburger-icon-svg');
            const stickyIconSvg = document.getElementById('sticky-hamburger-icon-svg');
            const closeBtn = document.getElementById('mega-menu-close');
            const stickyHeader = document.getElementById('sticky-header');
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
                    document.querySelectorAll('.mega-menu-col').forEach(c => {
                        c.classList.remove('open', 'is-expanded');
                    });
                }
            }

            if (hamburgerBtn) hamburgerBtn.addEventListener('click', toggleMenu);
            if (stickyBtn) stickyBtn.addEventListener('click', toggleMenu);

            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    megaMenu.classList.remove('active');
                    megaMenu.style.position = '';
                    megaMenu.style.top = '';
                    megaMenu.style.left = '';
                    megaMenu.style.right = '';
                    if (window.innerWidth <= 768 && headerWrapper) {
                        headerWrapper.appendChild(megaMenu);
                    }
                    const defaultSvg = '<line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line>';
                    if (iconSvg) iconSvg.innerHTML = defaultSvg;
                    if (stickyIconSvg) stickyIconSvg.innerHTML = defaultSvg;
                    document.querySelectorAll('.mega-menu-col').forEach(c => {
                        c.classList.remove('open', 'is-expanded');
                    });
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

            if (megaMenu) {
                megaMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            // CONTROL PANEL LOGIC
            const cpToggleBtn = document.getElementById('cp-toggle-btn');
            const cpCloseBtn = document.getElementById('cp-close-btn');
            const cpBackdrop = document.getElementById('cp-backdrop');
            const cpPanel = document.getElementById('control-panel');
            const cpSaveBtn = document.getElementById('cp-save-btn');
            const cpResetBtn = document.getElementById('cp-reset-btn');
            const cpApplyBtn = document.getElementById('cp-apply-btn');

            function openCp() {
                cpPanel.classList.add('cp-open');
                cpBackdrop.classList.add('cp-visible');
                cpToggleBtn.classList.add('cp-open');
                cpToggleBtn.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            }

            function closeCp() {
                cpPanel.classList.remove('cp-open');
                cpBackdrop.classList.remove('cp-visible');
                cpToggleBtn.classList.remove('cp-open');
                cpToggleBtn.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }

            if (cpToggleBtn) {
                cpToggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    cpPanel.classList.contains('cp-open') ? closeCp() : openCp();
                });
            }

            if (cpCloseBtn) cpCloseBtn.addEventListener('click', closeCp);
            if (cpBackdrop) cpBackdrop.addEventListener('click', closeCp);

            document.querySelectorAll('.cp-preset-box').forEach(presetBtn => {
                presetBtn.onclick = function(e) {
                    e.stopPropagation();
                    const presetNum = parseInt(this.getAttribute('data-preset-num'));
                    const presetKey = 'preset_' + presetNum;
                    const isCtrl = (e.ctrlKey || e.metaKey);
                    const isShift = e.shiftKey;

                    if (isCtrl) {
                        if (clientPresets[presetKey]) {
                            if (confirm("⚠️ Voulez-vous supprimer le Preset " + presetNum + " ?")) {
                                const formData = new FormData();
                                formData.append('action', 'save_config');
                                formData.append('preset_key', presetNum);
                                formData.append('purge', '1');
                                fetch('', { method: 'POST', body: formData })
                                .then(res => res.json())
                                .then(data => { if (data.success) window.location.reload(); });
                            }
                        }
                        return;
                    }

                    if (!clientPresets[presetKey] || isShift) {
                        const actionMsg = !clientPresets[presetKey] 
                            ? "Le Preset " + presetNum + " est vide. Voulez-vous y enregistrer la disposition actuelle ?"
                            : "Voulez-vous écraser et enregistrer la disposition actuelle dans le Preset " + presetNum + " ?";
                            
                        if (confirm(actionMsg)) {
                            const formData = new FormData();
                            formData.append('action', 'save_config');
                            formData.append('preset_key', presetNum);

                            document.querySelectorAll('.cp-size-select').forEach(sel => {
                                formData.append('spans[' + sel.getAttribute('data-project-size') + ']', sel.value);
                            });

                            const container = document.getElementById('news-grid-container');
                            if (container) {
                                container.querySelectorAll('[data-project-name]').forEach((card, index) => {
                                    formData.append('order[' + index + ']', card.getAttribute('data-project-name'));
                                });
                            }

                            fetch('', { method: 'POST', body: formData })
                            .then(res => res.json())
                            .then(data => { if (data.success) window.location.reload(); });
                        }
                        return;
                    }

                    const formData = new FormData();
                    formData.append('action', 'set_active_preset');
                    formData.append('preset_key', presetNum);
                    fetch('', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => { if (data.success) window.location.reload(); });
                };
            });

            if (cpSaveBtn) {
                cpSaveBtn.onclick = function(e) {
                    e.stopPropagation();
                    const formData = new FormData();
                    formData.append('action', 'save_config');

                    document.querySelectorAll('.cp-size-select').forEach(sel => {
                        formData.append('spans[' + sel.getAttribute('data-project-size') + ']', sel.value);
                    });

                    const container = document.getElementById('news-grid-container');
                    if (container) {
                        container.querySelectorAll('[data-project-name]').forEach((card, index) => {
                            formData.append('order[' + index + ']', card.getAttribute('data-project-name'));
                        });
                    }

                    fetch('', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            cpSaveBtn.innerHTML = '✓ Enregistré !';
                            setTimeout(() => { cpSaveBtn.innerHTML = '✓ Save'; }, 1500);
                        }
                    });
                };
            }

            if (cpResetBtn) {
                cpResetBtn.onclick = function(e) {
                    e.stopPropagation();
                    if (confirm("⚠️ Voulez-vous réinitialiser la mise en page par défaut ?")) {
                        const formData = new FormData();
                        formData.append('action', 'reset_config');
                        fetch('', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(data => { if (data.success) window.location.reload(); });
                    }
                };
            }

            if (cpApplyBtn) {
                cpApplyBtn.onclick = function(e) {
                    e.stopPropagation();
                    document.querySelectorAll('.news-grid-container [data-project-name]').forEach(col => {
                        col.className = col.className.replace(/news-col-\d+/, '').trim();
                        col.classList.add('news-col-6');
                    });

                    document.querySelectorAll('.cp-size-select').forEach(sel => {
                        const pName = sel.getAttribute('data-project-size');
                        const spanVal = sel.value;
                        document.querySelectorAll('.news-grid-container [data-project-name="' + CSS.escape(pName) + '"]').forEach(col => {
                            col.className = col.className.replace(/news-col-\d+/, '').trim();
                            col.classList.add('news-col-' + spanVal);
                        });
                    });
                    updateBearSpan();
                };
            }
        });

        function cpToggleProject(projectName, rowEl) {
            const checkbox = rowEl.querySelector('.cp-checkbox');
            if (!checkbox) return;
            const isVisible = checkbox.checked;
            const cols = document.querySelectorAll('.news-grid-container [data-project-name="' + CSS.escape(projectName) + '"]');
            const label = document.getElementById('cp-label-' + projectName);

            cols.forEach(col => {
                if (isVisible) {
                    col.classList.remove('news-col-hidden');
                    col.setAttribute('aria-hidden', 'false');
                } else {
                    col.classList.add('news-col-hidden');
                    col.setAttribute('aria-hidden', 'true');
                }
            });

            if (label) {
                label.classList.toggle('cp-hidden-label', !isVisible);
            }
            updateBearSpan();
        }

        function cpMoveProject(projectName, direction) {
            const container = document.getElementById('news-grid-container');
            const cpBody = document.getElementById('cp-body');
            const bearBlock = document.getElementById('bear-col-block');

            document.querySelectorAll('.news-grid-container [data-project-name="' + CSS.escape(projectName) + '"]').forEach(col => {
                if (direction === -1 && col.previousElementSibling && col.previousElementSibling !== bearBlock) {
                    container.insertBefore(col, col.previousElementSibling);
                } else if (direction === 1 && col.nextElementSibling && col.nextElementSibling !== bearBlock) {
                    container.insertBefore(col.nextElementSibling, col);
                }
            });

            const cpRow = cpBody ? cpBody.querySelector('[data-cp-row="' + CSS.escape(projectName) + '"]') : null;
            if (cpRow) {
                if (direction === -1 && cpRow.previousElementSibling && cpRow.previousElementSibling.classList.contains('cp-project-row')) {
                    cpBody.insertBefore(cpRow, cpRow.previousElementSibling);
                } else if (direction === 1 && cpRow.nextElementSibling && cpRow.nextElementSibling.classList.contains('cp-project-row')) {
                    cpBody.insertBefore(cpRow.nextElementSibling, cpRow);
                }
            }
            updateBearSpan();
        }

        function updateBearSpan() {
            const container = document.getElementById('news-grid-container');
            const bearBlock = document.getElementById('bear-col-block');
            if (!container || !bearBlock) return;

            let currentTotalSpan = 0;
            Array.from(container.children).forEach(child => {
                if (child === bearBlock) return;
                if (!child.classList.contains('news-col-hidden')) {
                    for (let i = 4; i <= 12; i++) {
                        if (child.classList.contains('news-col-' + i)) {
                            currentTotalSpan += i;
                            break;
                        }
                    }
                }
            });

            const remainder = currentTotalSpan % 12;
            let activeSpan = (remainder > 0) ? (12 - remainder) : 6;

            bearBlock.className = bearBlock.className.replace(/news-col-\d+/, '').trim();
            bearBlock.classList.add('news-col-' + activeSpan);
        }
    </script>

</body>
</html>