<?php
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
$projects = [];

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
$exclude = ['core', 'server', 'Data', 'sql', 'mac-server-runtime', 'mac-tools', 'static', 'projet-client', 'partials'];

foreach ($files as $file) {
    if ($file === '.' || $file === '..' || !is_dir($dir . '/' . $file)) continue;
    if (in_array($file, $exclude)) continue;

    $hasIndex = file_exists($dir . '/' . $file . '/index.php') || file_exists($dir . '/' . $file . '/index.html');
    $isWP     = file_exists($dir . '/' . $file . '/wp-config.php');

    $title     = $file;
    $lowerFile = mb_strtolower($file);
    
    // Associe les détails JSON (visuel, technos, pitch...)
    $customDetails = isset($projectsDetailsMap[$lowerFile]) ? $projectsDetailsMap[$lowerFile] : null;

    $description = "Description détaillée et présentation complète du projet web : " . htmlspecialchars($file, ENT_QUOTES, 'UTF-8') . ". Ce projet fonctionne localement dans votre environnement de développement nomade.";
    
    // Correction du chemin de l'image (utilisation du dossier local assets/img/ du dashboard)
    $imgName = 'photo-640x480.png';
    if ($customDetails && isset($customDetails['details']['niveau1']['image']) && !empty($customDetails['details']['niveau1']['image'])) {
        $imgName = basename($customDetails['details']['niveau1']['image']);
    }
    $screenshot = 'dashboard-designer/assets/img/' . $imgName;
    
    if (isset($savedStatuses[$file])) {
        $statusKey = $savedStatuses[$file];
    } else {
        if ($isWP || $hasIndex) {
            $statusKey = 'operational';
        } else {
            $statusKey = 'progress';
        }
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

    $projects[] = [
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
        'details'     => $customDetails ? (isset($customDetails['details']) ? $customDetails['details'] : null) : null
    ];
}
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

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
            word-break: break-all;
        }

        .badge {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 4px 10px;
            border-radius: 99px;
            margin-bottom: 14px;
            text-transform: uppercase;
            cursor: pointer;
            user-select: none;
            transition: transform 0.1s;
        }
        .badge:active {
            transform: scale(0.95);
        }
        .badge-validated   { background: rgba(34,197,94,0.15); color: var(--green); }
        .badge-operational { background: rgba(245,158,11,0.15); color: var(--orange); }
        .badge-progress    { background: rgba(239,68,68,0.15); color: var(--red); }
        .badge-wp          { background: rgba(56,189,248,0.15); color: var(--accent); cursor: default; }

        .card-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .card-link {
            display: inline-block;
            text-align: center;
            background-color: var(--accent);
            color: var(--bg-color);
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background-color 0.2s;
        }

        .card-link:hover {
            background-color: var(--accent-hover);
        }

        .btn-info {
            background: rgba(56, 189, 248, 0.05);
            border: 1px solid rgba(56, 189, 248, 0.3);
            color: var(--accent);
            text-align: center;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .btn-info:hover {
            background-color: var(--accent);
            color: var(--bg-color);
            border-color: var(--accent);
        }

        #modulor-overlay {
            position: fixed;
            inset: 0;
            background-color: var(--bg-color);
            z-index: 10000;
            display: flex;
            flex-direction: column;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            overflow-y: auto;
            padding: 40px;
            box-sizing: border-box;
        }
        #modulor-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .overlay-container {
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        .overlay-close {
            position: fixed;
            top: 30px;
            right: 40px;
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-main);
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            transition: all 0.2s;
            z-index: 10001;
        }
        .overlay-close:hover {
            background: var(--accent);
            color: var(--bg-color);
        }
        #overlay-title {
            font-size: 2.2rem;
            color: var(--accent);
            margin-top: 0;
            margin-bottom: 15px;
        }
        #overlay-text {
            font-size: 1rem;
            line-height: 1.6;
            color: var(--text-muted);
            margin-bottom: 25px;
        }
        .overlay-image-wrapper {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        #overlay-img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            display: block;
        }

        .level-block {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .level-title {
            color: var(--accent);
            font-size: 1.1rem;
            margin-top: 0;
            margin-bottom: 8px;
            border-bottom: 1px solid rgba(56, 189, 248, 0.2);
            padding-bottom: 4px;
        }
        .badge-tech {
            background: rgba(56, 189, 248, 0.15);
            color: var(--accent);
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-right: 5px;
            display: inline-block;
        }

        .empty {
            color: var(--text-muted);
            font-style: italic;
        }

        .separator-v2 {
            border: none;
            height: 6px;
            background-color: var(--accent);
            margin: 40px 0;
            opacity: 0.8;
            border-radius: 3px;
        }
    </style>
</head>
<body>

    <h1>🚀 Mes Projets Nomades</h1>

    <!-- V1 : GRILLE PRINCIPALE (HAUT DE PAGE) -->
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
                        <a class="card-link" href="<?php echo $linkHref; ?>" target="_blank">
                            Lancer le site
                        </a>
                        <button class="btn-info" onclick="openOverlay(this)"><i class="fas fa-eye"></i> En savoir plus...</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- SÉPARATEUR ÉPAIS BLEU -->
    <hr class="separator-v2">

    <!-- V2 : SECTION EXPÉRIMENTALE INCLUSE -->
    <?php include __DIR__ . '/partials/section-v2.php'; ?>

    <!-- MODALE OVERLAY MODULOR -->
    <div id="modulor-overlay">
        <button class="overlay-close" onclick="closeOverlay()"><i class="fas fa-times"></i> Fermer</button>
        <div class="overlay-container">
            <h2 id="overlay-title"></h2>
            <div id="overlay-text"></div>
            <div class="overlay-image-wrapper" id="overlay-img-container">
                <img id="overlay-img" src="" alt="Aperçu de la page d'accueil">
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

        function openOverlay(btn) {
            const card = btn.closest('.card, .card-v2');
            const title = card.getAttribute('data-title');
            const summary = card.getAttribute('data-summary');
            const imgSrc = card.getAttribute('data-img');
            
            const detailsRaw = card.getAttribute('data-details');
            let details = null;
            try {
                if (detailsRaw && detailsRaw !== "null") {
                    details = JSON.parse(detailsRaw);
                }
            } catch(e) {}

            document.getElementById('overlay-title').innerText = title;
            const containerText = document.getElementById('overlay-text');
            const imgEl = document.getElementById('overlay-img');
            const imgContainer = document.getElementById('overlay-img-container');

            if (details) {
                let html = '';

                // Niveau 1 : Visuel, Pitch & Badges
                html += '<div class="level-block">';
                if (details.niveau1) {
                    if (details.niveau1.pitch) html += `<p style="font-size: 1.1em; color: #fff; font-weight: bold;">${details.niveau1.pitch}</p>`;
                    if (details.niveau1.technos && details.niveau1.technos.length > 0) {
                        html += '<div style="margin-top: 10px;">';
                        details.niveau1.technos.forEach(t => {
                            html += `<span class="badge-tech">${t}</span>`;
                        });
                        html += '</div>';
                    }
                }
                html += '</div>';

                // Niveau 2 : Contexte & Fonctionnalités clés
                if (details.niveau2) {
                    html += '<div class="level-block">';
                    if (details.niveau2.contexte) {
                        html += '<h3 class="level-title">Contexte</h3>';
                        html += `<p>${details.niveau2.contexte}</p>`;
                    }
                    if (details.niveau2.fonctionnalites && details.niveau2.fonctionnalites.length > 0) {
                        html += '<h3 class="level-title" style="margin-top: 15px;">Fonctionnalités clés</h3><ul>';
                        details.niveau2.fonctionnalites.forEach(f => {
                            html += `<li>${f}</li>`;
                        });
                        html += '</ul>';
                    }
                    html += '</div>';
                }

                // Niveau 3 : Spécifications techniques & Roadmap
                if (details.niveau3) {
                    html += '<div class="level-block">';
                    html += '<h3 class="level-title">Spécifications & Architecture</h3>';
                    if (details.niveau3.architecture) html += `<p><strong>Architecture :</strong> ${details.niveau3.architecture}</p>`;
                    if (details.niveau3.environnement) html += `<p><strong>Environnement :</strong> ${details.niveau3.environnement}</p>`;
                    if (details.niveau3.roadmap) html += `<p style="margin-top: 10px;"><strong>Roadmap :</strong> ${details.niveau3.roadmap}</p>`;
                    html += '</div>';
                }

                containerText.innerHTML = html;

                if (details.niveau1 && details.niveau1.image) {
                    imgEl.src = `dashboard-designer/assets/img/${details.niveau1.image}`;
                    imgContainer.style.display = 'block';
                } else {
                    imgEl.src = `dashboard-designer/assets/img/photo-640x480.png`;
                    imgContainer.style.display = 'block';
                }

            } else {
                containerText.innerText = summary;
                imgEl.src = imgSrc;
                imgContainer.style.display = 'block';
            }

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