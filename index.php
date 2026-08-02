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

// Dossiers système et dossiers internes à exclure du listing (mon-premier-site masqué ici)
$exclude = ['.git', 'core', 'server', 'Data', 'sql', 'mac-server-runtime', 'mac-tools', 'static', 'projet-client', 'partials', 'mon-premier-site'];

foreach ($files as $file) {
    if ($file === '.' || $file === '..' || !is_dir($dir . '/' . $file)) continue;
    if (in_array($file, $exclude)) continue;

    $hasIndex = file_exists($dir . '/' . $file . '/index.php') || file_exists($dir . '/' . $file . '/index.html');
    $isWP    = file_exists($dir . '/' . $file . '/wp-config.php');

    $title   = $file;
    $lowerFile = mb_strtolower($file);
    
    $customDetails = isset($projectsDetailsMap[$lowerFile]) ? $projectsDetailsMap[$lowerFile] : null;

    // Injection des détails complets (avec niveaux 2 et 3)
    if (!$customDetails && in_array($lowerFile, ['user_journey-v1.0', 'user_journey'])) {
        $customDetails = [
            'name' => $file,
            'details' => [
                'niveau1' => [
                    'pitch' => "Pensé pour façonner et orchestrer les parcours utilisateurs, <strong>user_journey-v1.0</strong> est le seul outil de cette clé entièrement conçu pour l'UX-UI pure. Destiné en priorité absolue aux web designers et aux spécialistes de l'expérience utilisateur, il fournit l'écosystème visuel et fonctionnel idéal pour concevoir, prototyper et évaluer les interfaces avec une finesse absolue.",
                    'technos' => ['UI Design', 'UX Research', 'Prototypage'],
                    'image' => 'photo-640x480.png'
                ],
                'niveau2' => [
                    'contexte' => "Conçu exclusivement pour les professionnels du design d'interface et de l'expérience utilisateur ne faisant que de l'UX-UI.",
                    'fonctionnalites' => [
                        "Modélisation et analyse des parcours utilisateurs",
                        "Conception et test d'interfaces orientées UX",
                        "Outils dédiés aux exigences strictes des web designers"
                    ]
                ],
                'niveau3' => [
                    'architecture' => "Design System / Parcours UX",
                    'environnement' => "Clé USB / Atelier Nomade",
                    'roadmap' => "Intégration de nouveaux composants d'ergonomie"
                ]
            ]
        ];
    } elseif (!$customDetails && $lowerFile === 'texturor') {
        $customDetails = [
            'name' => 'texturor',
            'details' => [
                'niveau1' => [
                    'pitch' => "Conçu comme un CodePen <em>home made</em> au cœur de l'atelier, <strong>texturor</strong> est taillé pour prototyper et tester du code en un clin d'œil. Doté d'une capacité redoutable pour enregistrer et organiser tes snippets favoris, il se révèle également parfaitement responsive pour effectuer des tests et des ajustements en ligne directement depuis ton mobile.",
                    'technos' => ['PHP', 'JavaScript', 'HTML/CSS Live'],
                    'image' => 'photo-640x480.png'
                ],
                'niveau2' => [
                    'contexte' => "Offre un espace d'expérimentation de code instantané, portable et accessible sur tous les supports, y compris en situation de mobilité.",
                    'fonctionnalites' => [
                        "Environnement de test type CodePen maison",
                        "Enregistrement et gestion de snippets réutilisables",
                        "Interface responsive taillée pour le test sur mobile"
                    ]
                ],
                'niveau3' => [
                    'architecture' => "Éditeur de snippets / Flat-file",
                    'environnement' => "Clé USB / Mobile / Local",
                    'roadmap' => "Optimisation du stockage des snippets favoris"
                ]
            ]
        ];
    } elseif (!$customDetails && $lowerFile === 'personator-v1.2') {
        $customDetails = [
            'name' => 'personator-v1.2',
            'details' => [
                'niveau1' => [
                    'pitch' => "Atelier d'incarnation et de génération de profils, <strong>personator-v1.2</strong> donne vie à vos applications en peuplant instantanément vos bases ou vos maquettes avec des données utilisateur sur-mesure, réalistes et percutantes.",
                    'technos' => ['PHP', 'JSON', 'JavaScript'],
                    'image' => 'capture-personator.png'
                ],
                'niveau2' => [
                    'contexte' => "S'affranchit des saisies manuelles fastidieuses lors des phases de test pour injecter du contenu vivant dans les interfaces.",
                    'fonctionnalites' => [
                        "Génération à la volée de profils et d'identités fictives",
                        "Injection rapide de données de test cohérentes",
                        "Personnalisation fine des attributs et des rôles"
                    ]
                ],
                'niveau3' => [
                    'architecture' => "Générateur dynamique / Flat-file",
                    'environnement' => "Clé USB / Serveur Local",
                    'roadmap' => "Extension des jeux de données internationaux"
                ]
            ]
        ];
    } elseif (!$customDetails && $lowerFile === 'modulor') {
        $customDetails = [
            'name' => 'modulor',
            'details' => [
                'niveau1' => [
                    'pitch' => "Laboratoire visuel et interactif de l'atelier, <strong>modulor</strong> propose l'interface idéale pour tester à la volée des mises en page, expérimenter des structures d'UI et sculpter des composants en direct sans contrainte technique lourde.",
                    'technos' => ['PHP', 'CSS Live', 'JavaScript'],
                    'image' => 'photo-640x480.png'
                ],
                'niveau2' => [
                    'contexte' => "S'affranchit des maquettes statiques pour prototyper instantanément des blocs d'interface au sein de l'atelier nomade.",
                    'fonctionnalites' => [
                        "Test en direct de mises en page et de designs",
                        "Prototypage rapide de composants web",
                        "Espace d'expérimentation visuelle modulable"
                    ]
                ],
                'niveau3' => [
                    'architecture' => "Design Lab / Moteur de Skin",
                    'environnement' => "Clé USB / Serveur Local",
                    'roadmap' => "Extension des moteurs de sections et d'export"
                ]
            ]
        ];
    } elseif (!$customDetails && $lowerFile === 'skeletor-v1.0') {
        $customDetails = [
            'name' => 'skeletor-v1.0',
            'details' => [
                'niveau1' => [
                    'pitch' => "Couteau suisse du développeur nomade, <strong>skeletor-v1.0</strong> transforme la corvée des clics répétés en un jeu d'enfant. Fini les « nouveau dossier », « nouveau fichier index.php » et l'arborescence à recréer à la main : il déploie en un clin d'œil toute la trame de base indispensable pour lancer un nouveau site web, rendant la création de projets à la fois ludique, instantanée et redoutablement efficace.",
                    'technos' => ['PHP', 'HTML5', 'CSS3'],
                    'image' => 'capture-skeletor.png'
                ],
                'niveau2' => [
                    'contexte' => "Créé pour automatiser la mise en place initiale des structures de projets web directement depuis la clé USB.",
                    'fonctionnalites' => [
                        "Génération instantanée de l'arborescence de dossiers",
                        "Enregistrement de trames de base réutilisables",
                        "Élimination des tâches répétitives de configuration"
                    ]
                ],
                'niveau3' => [
                    'architecture' => "Modulaire / Arborescence standard",
                    'environnement' => "Clé USB / Serveur Local",
                    'roadmap' => "Extension des trames personnalisables"
                ]
            ]
        ];
    } elseif (!$customDetails && $lowerFile === 'skeletor-v1.0-o2switch') {
        $customDetails = [
            'name' => 'skeletor-v1.0-o2switch',
            'details' => [
                'niveau1' => [
                    'pitch' => "Version dopée à la production de l'atelier, <strong>skeletor-v1.0-o2switch</strong> reprend la logique ludique et l'enregistrement de trames de son aîné pour l'ériger en rampe de lancement vers le serveur distant. Il évite les manipulations fastidieuses et sécurise d'un bloc le passage de la clé USB nomade à l'hébergeur o2switch, sans friction ni perte de temps.",
                    'technos' => ['PHP', 'Déploiement', 'Sécurité'],
                    'image' => 'capture-skeletor.png'
                ],
                'niveau2' => [
                    'contexte' => "Adaptation des trames nomades aux exigences de sécurité et de configuration d'un hébergement web professionnel.",
                    'fonctionnalites' => [
                        "Préparation des fichiers pour le transfert distant",
                        "Optimisation des configurations serveur",
                        "Pont direct entre l'atelier local et o2switch"
                    ]
                ],
                'niveau3' => [
                    'architecture' => "Production / O2switch Ready",
                    'environnement' => "Hébergement Distant / Clé USB",
                    'roadmap' => "Automatisation des scripts de déploiement"
                ]
            ]
        ];
    } elseif (!$customDetails && $lowerFile === 'dashboard-designer') {
        $customDetails = [
            'name' => 'dashboard-designer',
            'details' => [
                'niveau1' => [
                    'pitch' => "Le module <strong>dashboard-designer</strong> redéfinit l'ergonomie de pilotage de l'atelier nomade. En fusionnant l'esthétique rédactionnelle de la grande presse et la rigueur d'un tableau de bord technique, il permet d'orchestrer, de structurer et de visualiser l'ensemble des projets stockés sur la clé USB avec une élégance et une fluidité absolues.",
                    'technos' => ['PHP', 'JavaScript', 'CSS Custom'],
                    'image' => 'photo-640x480.png'
                ],
                'niveau2' => [
                    'contexte' => "Conçu pour unifier l'accès aux multiples applications de l'atelier nomade sans dépendre d'outils lourds ou distants.",
                    'fonctionnalites' => [
                        "Mise en page journalistique multi-combinaisons",
                        "Gestion dynamique des statuts de projets",
                        "Architecture modulaire et responsive"
                    ]
                ],
                'niveau3' => [
                    'architecture' => "Flat-file / PHP Vanille / CSS Grid",
                    'environnement' => "Serveur local XAMPP / Clé USB",
                    'roadmap' => "Ajout de nouveaux thèmes typographiques"
                ]
            ]
        ];
    } elseif (!$customDetails && $lowerFile === 'cms-2026-v8-full') {
        $customDetails = [
            'name' => 'cms-2026-v8-full',
            'details' => [
                'niveau1' => [
                    'pitch' => "Au cœur de l'atelier nomade se dresse <strong>cms-2026-v8-full</strong>, un système de gestion de contenu sur-mesure et ultra-léger pensé pour s'affranchir des lourdeurs du web traditionnel. Conçu pour fonctionner de manière autonome sur un serveur local XAMPP hébergé au creux d\'une clé USB, ce projet incarne la quintessence du développement Flat-file en PHP vanille, garantissant une souveraineté technique totale et une réactivité immédiate sans aucune dépendance cloud.",
                    'technos' => ['PHP', 'JavaScript', 'CSS Custom'],
                    'image' => 'photo-640x480.png'
                ],
                'niveau2' => [
                    'contexte' => "Conçu pour répondre au besoin d'un CMS rapide, totalement personnalisable et indépendant des bases de données lourdes.",
                    'fonctionnalites' => [
                        "Édition dynamique des contenus",
                        "Gestion des blocs d'organisation",
                        "Architecture flat-file sans BDD"
                    ]
                ],
                'niveau3' => [
                    'architecture' => "Flat-file / PHP Vanille",
                    'environnement' => "Serveur local XAMPP / Clé USB",
                    'roadmap' => "Ajout de nouveaux modules de mise en page"
                ]
            ]
        ];
    } elseif (!$customDetails && $lowerFile === 'wordpress-portable') {
        $customDetails = [
            'name' => 'wordpress-portable',
            'details' => [
                'niveau1' => [
                    'pitch' => "Instance WordPress totalement encapsulée et autonome, <strong>wordpress-portable</strong> embarque toute la puissance du CMS le plus populaire du web directement au creux de votre clé USB, sans installation lourde sur la machine hôte.",
                    'technos' => ['WordPress', 'PHP', 'MySQL Portable'],
                    'image' => 'photo-640x480.png'
                ],
                'niveau2' => [
                    'contexte' => "Permet de prototyper, tester et développer des sites sous WordPress de manière nomade et isolée.",
                    'fonctionnalites' => [
                        "Environnement CMS complet et transportable",
                        "Intégration transparente avec le serveur XAMPP local",
                        "Autonomie totale des fichiers et de la base de données"
                    ]
                ],
                'niveau3' => [
                    'architecture' => "CMS / Base de données embarquée",
                    'environnement' => "Clé USB / XAMPP Local",
                    'roadmap' => "Mise à jour des noyaux et des extensions de base"
                ]
            ]
        ];
    } elseif (!$customDetails && $lowerFile === 'mon-site') {
        $customDetails = [
            'name' => 'mon-site',
            'details' => [
                'niveau1' => [
                    'pitch' => "Résultat direct de l'architecture créée avec <strong>Skeletor</strong>, <strong>mon-site</strong> concrétise l'exportation du squelette pour l'afficher et le vérifier directement dans le navigateur en conditions réelles.",
                    'technos' => ['HTML5', 'CSS3', 'PHP'],
                    'image' => 'photo-640x480.png'
                ],
                'niveau2' => [
                    'contexte' => "Sert de livrable de test et de vérification visuelle pour valider le bon fonctionnement du squelette exporté depuis Skeletor.",
                    'fonctionnalites' => [
                        "Vérification de l'arborescence exportée dans le navigateur",
                        "Zone de test en conditions réelles de rendu",
                        "Passage de relais entre génération et affichage"
                    ]
                ],
                'niveau3' => [
                    'architecture' => "Livrable de vérification / Flat-file",
                    'environnement' => "Clé USB / Serveur Local",
                    'roadmap' => "Vérification itérative des gabarits"
                ]
            ]
        ];
    }

    // Fallback automatique pour tout autre dossier n'ayant pas de détails explicites
    if (!$customDetails) {
        $customDetails = [
            'name' => $file,
            'details' => [
                'niveau1' => [
                    'pitch' => "Description détaillée et présentation complète du projet web : " . htmlspecialchars($file, ENT_QUOTES, 'UTF-8') . ". Ce projet fonctionne localement dans votre environnement de développement nomade.",
                    'technos' => ['PHP', 'HTML/CSS'],
                    'image' => 'photo-640x480.png'
                ],
                'niveau2' => [
                    'contexte' => "Intégration et suivi de projet au sein de l'environnement de développement portable.",
                    'fonctionnalites' => [
                        "Exécution locale sur serveur XAMPP",
                        "Structure de fichiers normalisée",
                        "Suivi d'état et de validation"
                    ]
                ],
                'niveau3' => [
                    'architecture' => "Standard / Standardisé",
                    'environnement' => "Clé USB / Local",
                    'roadmap' => "Évolution continue"
                ]
            ]
        ];
    }

    $description = "Description détaillée et présentation complète du projet web : " . htmlspecialchars($file, ENT_QUOTES, 'UTF-8') . ". Ce projet fonctionne localement dans votre environnement de développement nomade.";
    
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

    // Attribution de la largeur en colonnes et du libellé technique selon la nature du projet
    $colSpan = 6; 
    $sizeLabel = 'UX-UI';

    if (in_array($lowerFile, ['cms-2026-v8-full', 'dashboard-designer', 'wordpress-portable'])) {
        $colSpan = 12;
        $sizeLabel = 'CMS';
    } elseif ($lowerFile === 'mon-site') {
        $colSpan = 6; 
        $sizeLabel = 'Preview';
    }

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
        'colSpan'     => $colSpan,
        'colClass'    => 'news-col-' . $colSpan,
        'sizeLabel'   => $sizeLabel,
        'linkHref'    => '/' . rawurlencode($file) . '/'
    ];
}

// Ordonnancement strict selon notre classement validé
$orderedKeys = [
    'cms-2026-v8-full',
    'dashboard-designer',
    'skeletor-v1.0',
    'skeletor-v1.0-o2switch',
    'modulor',
    'personator-v1.2',
    'texturor',
    'user_journey-v1.0',
    'wordpress-portable',
    'mon-site'
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

// Algorithme d'empaquetage mathématique strict (lignes exactes de 12 colonnes -> Zéro trou)
$rows = [];
$currentRow = [];
$currentSpan = 0;

foreach ($projects as $p) {
    if (($currentSpan + $p['colSpan']) > 12) {
        $rows[] = $currentRow;
        $currentRow = [$p];
        $currentSpan = $p['colSpan'];
    } else {
        $currentRow[] = $p;
        $currentSpan += $p['colSpan'];
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

// Calcul de l'espace restant sur la dernière ligne pour l'ours dynamique
$lastRowSpan = 0;
if (!empty($rows)) {
    $lastRow = end($rows);
    foreach ($lastRow as $item) {
        $lastRowSpan += $item['colSpan'];
    }
}
$bearColSpan = (12 - $lastRowSpan);
if ($bearColSpan < 0) $bearColSpan = 0;
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

        /* SECTIONS PLIABLES <details> */
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

        /* ============================================================
            SECTION NEWS (GRILLE 12 COLONNES - MISE EN PAGE JOURNAL 100% SANS TROU)
        ============================================================ */
        .news-sheet {
            background-color: #fdfbf7;
            color: #2b2b2b;
            border: 2px solid #2b2b2b;
            border-radius: 4px;
            padding: 35px;
            font-family: Georgia, "Times New Roman", serif;
            margin-top: 20px;
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
            border-bottom: 3px double #333333;
            padding-bottom: 20px;
            margin-bottom: 25px;
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
            padding-bottom: 20px;
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

        /* SYSTÈME DE LIGNES 12 COLONNES */
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
            .news-header-grid { grid-template-columns: 1fr; gap: 15px; }
            .news-ear { display: none; }
        }

        /* Style des articles rédactionnels avec encart visuel fixe (hauteur 220px, ancré en haut, loupe) */
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

        .news-article-visual {
            width: 100%;
            height: 220px;
            background-color: #f1f3f5;
            border: 1px dashed #cbd5e1;
            border-radius: 4px;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            color: #64748b;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
            overflow: hidden;
            cursor: zoom-in;
            position: relative;
        }
        .news-article-visual img {
            width: 100%;
            height: auto;
            display: block;
            object-position: top;
        }

        /* OVERLAY DÉDIÉ POUR L'IMAGE DU JOURNAL (Noir dense, curseur SVG miniature intégré propre et net en 24x24, scrollbar masquée) */
        #journal-img-overlay {
            position: fixed;
            inset: 0;
            background: #000000;
            z-index: 20000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            padding: 40px;
            box-sizing: border-box;
        }
        #journal-img-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .journal-img-overlay-content {
            position: relative;
            max-width: 95vw;
            max-height: 90vh;
            overflow-y: auto;
            background: #000000;
            padding: 0;
            border-radius: 0;
            box-shadow: none;
            border: none;
            cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E%3Ccircle cx='12' cy='12' r='10' fill='%23ffffff' stroke='%23000000' stroke-width='2'/%3E%3Cpath d='M12 7l-3 4h6z' fill='%23000000'/%3E%3Cpath d='M12 17l-3-4h6z' fill='%23000000'/%3E%3C/svg%3E") 12 12, ns-resize;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .journal-img-overlay-content::-webkit-scrollbar {
            display: none;
        }

        .journal-img-overlay-content img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 0;
            cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E%3Ccircle cx='12' cy='12' r='10' fill='%23ffffff' stroke='%23000000' stroke-width='2'/%3E%3Cpath d='M12 7l-3 4h6z' fill='%23000000'/%3E%3Cpath d='M12 17l-3-4h6z' fill='%23000000'/%3E%3C/svg%3E") 12 12, ns-resize;
        }

        .journal-img-close {
            position: fixed;
            top: 30px;
            right: 40px;
            background: #1c1c1c;
            border: none;
            color: #f3f3f3;
            padding: 10px 20px;
            border-radius: 0;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            z-index: 20001;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
            transition: all 0.2s;
        }
        .journal-img-close:hover {
            background: #333333;
            color: #ffffff;
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

        /* Masquage strict des tags et badges techniques dans cette section */
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

        /* Style de l'ours en pied de page si la grille est complète */
        .news-colophon-footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px dashed #bbbbbb;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.8rem;
            color: #4a4a4a;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Lien interactif aligné à droite, gris et obscurci au survol */
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

        /* MISE EN PAGE 2 COLONNES UNIQUEMENT AU FORMAT DESK (> 1024px) */
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

    <!-- ============================================================
         SECTION V1 : GRILLE PRINCIPALE (HAUT DE PAGE)
    ============================================================ -->
    <details class="section-block" open>
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

    <!-- ============================================================
         SECTION V2 : LANCEUR PROJETS - CARTES ENRICHIES
    ============================================================ -->
    <details class="section-block" open>
        <summary>
            <span class="summary-icon">🚀</span>
            <h2>Lanceur Projets (V2 — Cartes Enrichies)</h2>
            <span class="summary-chevron">▼</span>
        </summary>
        <div class="section-body">
            <?php include __DIR__ . '/partials/section-v2.php'; ?>
        </div>
    </details>

    <!-- ============================================================
         SECTION V3 : PRÉSENTATION DE L'ÉCOSYSTÈME & RAPPORT CLÉ [MASQUÉE EN RÉSERVE]
    ============================================================ -->
    <details class="section-block section-ecosystem" style="display: none;">
        <summary>
            <span class="summary-icon">📋</span>
            <h2>Présentation de l'Écosystème &amp; Rapport de la Clé</h2>
            <span class="summary-chevron">▼</span>
        </summary>
        <div class="section-body">
            <div class="eco-section">
                <h3>🎯 Concept &amp; Paradigme Global</h3>
                <p>Environnement de développement web 100% portable sur clé USB (F:\).</p>
            </div>
        </div>
    </details>

    <!-- ============================================================
         SECTION V4 : PRÉSENTATION DE L'ATELIER 2026 [MASQUÉE EN RÉSERVE - NON JETÉE]
    ============================================================ -->
    <details class="section-block" style="display: none;">
        <summary>
            <span class="summary-icon">🏛️</span>
            <h2>Atelier 2026 (En réserve)</h2>
            <span class="summary-chevron">▼</span>
        </summary>
        <div class="section-body">
            <p>Contenu en réserve de l'Atelier 2026.</p>
        </div>
    </details>

    <!-- ============================================================
         SECTION 4 : NEWS (JOURNAL DE L'ATELIER)
    ============================================================ -->
    <div class="news-sheet">
        
        <!-- Bandeau supérieur -->
        <div class="news-bandeau">
            Chronique Indépendante • Édition Spéciale Nomadisme Numérique • 2026
        </div>

        <!-- Manchette et Oreilles -->
        <div class="news-header-grid">
            <div class="news-ear">
                <strong>SUPPORT :</strong> Clé USB F:\<br>
                <strong>SERVEUR :</strong> XAMPP Portable
            </div>
            <div>
                <h2 class="news-manchette">L'Atelier Numérique</h2>
                <div style="font-size: 0.85rem; letter-spacing: 2px; text-transform: uppercase; margin-top: 6px; font-weight: bold; color: #444;">Christophe Millot</div>
            </div>
            <div class="news-ear">
                <strong>ARCHITECTURES :</strong> Flat-File<br>
                <strong>STATUT :</strong> Opérationnel
            </div>
        </div>

        <!-- Tribune (Éditorial) -->
        <div class="news-tribune">
            <h3>Tribune Libre — L'Affranchissement du Cloud</h3>
            <p>« S'affranchir des infrastructures distantes pour recentrer le développement web sur l'essentiel : la maîtrise absolue du code, de l'octet initial jusqu'au déploiement final, au creux d'un support de poche inaltérable. »</p>
        </div>

        <!-- Le Ventre : Affichage par lignes de 12 colonnes sans trou -->
        <?php foreach ($rows as $index => $rowProjects): ?>
            <div class="news-row">
                <?php foreach ($rowProjects as $p): ?>
                    <?php
                    $linkHref = '/' . rawurlencode($p['name']) . '/';
                    ?>
                    <div class="<?php echo htmlspecialchars($p['colClass'], ENT_QUOTES, 'UTF-8'); ?>">
                        <article class="news-article">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: baseline; border-bottom: 2px solid #111; padding-bottom: 4px; margin-bottom: 8px;">
                                    <h4 style="margin: 0; border: none; padding: 0;"><?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <span style="font-family: -apple-system, sans-serif; font-size: 0.65rem; text-transform: uppercase; color: #777; letter-spacing: 0.5px;"><?php echo htmlspecialchars($p['sizeLabel'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                
                                <!-- Encart visuel / Image dynamique ancrée en haut dans le journal -->
                                <div class="news-article-visual" style="<?php echo ($p['name'] === 'skeletor-v1.0' || $p['name'] === 'skeletor-v1.0-o2switch' || $p['name'] === 'personator-v1.2') ? 'border: none; background: transparent;' : ''; ?>">
                                    <?php if ($p['name'] === 'skeletor-v1.0' || $p['name'] === 'skeletor-v1.0-o2switch'): ?>
                                        <img src="images/capture-skeletor.png" alt="Aperçu Skeletor" onclick="openJournalImage(this.src)">
                                    <?php elseif ($p['name'] === 'personator-v1.2'): ?>
                                        <img src="images/capture-personator.png" alt="Aperçu Personator" onclick="openJournalImage(this.src)">
                                    <?php else: ?>
                                        <i class="fas fa-image" style="margin-right: 6px;"></i> Encart Visuel / Illustration
                                    <?php endif; ?>
                                </div>

                                <?php if ($p['name'] === 'cms-2026-v8-full'): ?>
                                    <p class="news-pitch desk-col-2">Au cœur de l'atelier nomade se dresse <strong>cms-2026-v8-full</strong>, un système de gestion de contenu sur-mesure et ultra-léger pensé pour s'affranchir des lourdeurs du web traditionnel. Conçu pour fonctionner de manière autonome sur un serveur local XAMPP hébergé au creux d\'une clé USB, ce projet incarne la quintessence du développement Flat-file en PHP vanille, garantissant une souveraineté technique totale et une réactivité immédiate sans aucune dépendance cloud.</p>
                                <?php elseif ($p['name'] === 'dashboard-designer'): ?>
                                    <p class="news-pitch desk-col-2">Le module <strong>dashboard-designer</strong> redéfinit l'ergonomie de pilotage de l'atelier nomade. En fusionnant l'esthétique rédactionnelle de la grande presse et la rigueur d'un tableau de bord technique, il permet d'orchestrer, de structurer et de visualiser l'ensemble des projets stockés sur la clé USB avec une élégance et une fluidité absolues.</p>
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

                                <?php if ($p['details'] && isset($p['details']['niveau2'])): ?>
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
                                <?php else: ?>
                                    <a href="<?php echo htmlspecialchars($p['linkHref'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="news-article-link">Voir le projet...</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>

                <!-- Injection dynamique de l'Ours en mode bouche-trou (centré horizontalement et verticalement) -->
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

        <!-- Bascule automatique de l'Ours en pied de page si la grille est complète (aucun trou, centré) -->
        <?php if ($bearColSpan === 0): ?>
            <div class="news-colophon-footer" style="text-align: center; justify-content: center; gap: 20px;">
                <span>Rédacteur en chef : Christophe Millot | Assistant : Gemini | Pige : Antigravity</span>
            </div>
        <?php endif; ?>

        <!-- Pied de page / Rez-de-chaussée avec horloge dynamique et copyright (centré) -->
        <div class="news-footer" style="text-align: center; justify-content: center; gap: 20px;">
            <span>&copy; <?php echo date('Y'); ?> Christophe Millot • Tous droits réservés</span>
            <span>•</span>
            <span>Mise à jour : <?php echo date('d/m/Y à H:i'); ?></span>
        </div>

    </div>

    <!-- MODALE OVERLAY -->
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

    <!-- OVERLAY DÉDIÉ POUR L'IMAGE DU JOURNAL (Noir dense, curseur SVG miniature 24x24 rond flèches haut/bas, scrollbar masquée) -->
    <div id="journal-img-overlay">
        <button class="journal-img-close" onclick="closeJournalImage()"><i class="fas fa-times"></i> Fermer</button>
        <div class="journal-img-overlay-content">
            <img id="journal-overlay-img" src="" alt="Aperçu grand format">
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
            if (!card) return;
            
            const badgeEl = card.querySelector('[data-project]');
            const projectName = badgeEl ? badgeEl.getAttribute('data-project') : '';
            
            const title = (card.getAttribute('data-title') || '').toLowerCase();
            const summary = card.getAttribute('data-summary') || '';
            const imgSrc = card.getAttribute('data-img') || '';
            
            const detailsRaw = card.getAttribute('data-details');
            let details = null;
            try {
                if (detailsRaw && detailsRaw !== "null") {
                    details = JSON.parse(detailsRaw);
                }
            } catch(e) {}

            document.getElementById('overlay-title').innerText = card.getAttribute('data-title') || 'Détails';
            const containerText = document.getElementById('overlay-text');
            const mediaWrapper = document.getElementById('overlay-media-wrapper');
            const imgContainer = document.getElementById('overlay-img-container');

            let html = '';

            html += '<div class="level-block">';
            if (title.includes('cms-2026-v8-full')) {
                html += '<p style="font-size: 1.05em; color: #fff; font-weight: 500; margin-bottom: 12px; line-height: 1.6;" class="desk-col-2">Au cœur de l\'atelier nomade se dresse <strong>cms-2026-v8-full</strong>, un système de gestion de contenu sur-mesure et ultra-léger pensé pour s\'affranchir des lourdeurs du web traditionnel. Conçu pour fonctionner de manière autonome sur un serveur local XAMPP hébergé au creux d\'une clé USB, ce projet incarne la quintessence du développement Flat-file en PHP vanille, garantissant une souveraineté technique totale et une réactivité immédiate sans aucune dépendance cloud.</p>';
            } else if (title.includes('dashboard-designer')) {
                html += '<p style="font-size: 1.05em; color: #fff; font-weight: 500; margin-bottom: 12px; line-height: 1.6;" class="desk-col-2">Le module <strong>dashboard-designer</strong> redéfinit l\'ergonomie de pilotage de l\'atelier nomade. En fusionnant l\'esthétique rédactionnelle de la grande presse et la rigueur d\'un tableau de bord technique, le module permet d\'orchestrer, de structurer et de visualiser l\'ensemble des projets stockés sur la clé USB avec une élégance et une fluidité absolues.</p>';
            } else if (title.includes('wordpress-portable')) {
                html += '<p style="font-size: 1.05em; color: #fff; font-weight: 500; margin-bottom: 12px; line-height: 1.6;">Instance WordPress totalement encapsulée et autonome, <strong>wordpress-portable</strong> embarque toute la puissance du CMS le plus populaire du web directement au creux de votre clé USB, sans installation lourde sur la machine hôte.</p>';
            } else if (title.includes('mon-site')) {
                html += '<p style="font-size: 1.05em; color: #fff; font-weight: 500; margin-bottom: 12px; line-height: 1.6;">Résultat direct de l\'architecture créée avec <strong>Skeletor</strong>, <strong>mon-site</strong> concrétise l\'exportation du squelette pour l\'afficher et le vérifier directement dans le navigateur en conditions réelles.</p>';
            } else if (title.includes('user_journey')) {
                html += '<p style="font-size: 1.05em; color: #fff; font-weight: 500; margin-bottom: 12px; line-height: 1.6;">Pensé pour façonner et orchestrer les parcours utilisateurs, <strong>user_journey-v1.0</strong> est le seul outil de cette clé entièrement conçu pour l\'UX-UI pure. Destiné en priorité absolue aux web designers et aux spécialistes de l\'expérience utilisateur, il fournit l\'écosystème visuel et fonctionnel idéal pour concevoir, prototyper et évaluer les interfaces avec une finesse absolue.</p>';
            } else if (title.includes('texturor')) {
                html += '<p style="font-size: 1.05em; color: #fff; font-weight: 500; margin-bottom: 12px; line-height: 1.6;">Conçu comme un CodePen <em>home made</em> au cœur de l\'atelier, <strong>texturor</strong> est taillé pour prototyper et tester du code en un clin d\'œil. Doté d\'une capacité redoutable pour enregistrer et organiser tes snippets favoris, il se révèle également parfaitement responsive pour effectuer des tests et des ajustements en ligne directement depuis ton mobile.</p>';
            } else if (title.includes('personator-v1.2')) {
                html += '<p style="font-size: 1.05em; color: #fff; font-weight: 500; margin-bottom: 12px; line-height: 1.6;">Atelier d\'incarnation et de génération de profils, <strong>personator-v1.2</strong> donne vie à vos applications en peuplant instantanément vos bases ou vos maquettes avec des données utilisateur sur-mesure, réalistes et percutantes.</p>';
            } else if (title.includes('modulor')) {
                html += '<p style="font-size: 1.05em; color: #fff; font-weight: 500; margin-bottom: 12px; line-height: 1.6;">Laboratoire visuel et interactif de l\'atelier, <strong>modulor</strong> propose l\'interface idéale pour tester à la volée des mises en page, expérimenter des structures d\'UI et sculpter des composants en direct sans contrainte technique lourde.</p>';
            } else if (title.includes('skeletor-v1.0-o2switch')) {
                html += '<p style="font-size: 1.05em; color: #fff; font-weight: 500; margin-bottom: 12px; line-height: 1.6;">Version dopée à la production de l\'atelier, <strong>skeletor-v1.0-o2switch</strong> reprend la logique ludique et l\'enregistrement de trames de son aîné pour l\'ériger en rampe de lancement vers le serveur distant. Il évite les manipulations fastidieuses et sécurise d\'un bloc le passage de la clé USB nomade à l\'hébergeur o2switch, sans friction ni perte de temps.</p>';
            } else if (title.includes('skeletor-v1.0')) {
                html += '<p style="font-size: 1.05em; color: #fff; font-weight: 500; margin-bottom: 12px; line-height: 1.6;">Couteau suisse du développeur nomade, <strong>skeletor-v1.0</strong> transforme la corvée des clics répétés en un jeu d\'enfant. Fini les « nouveau dossier », « nouveau fichier index.php » et l\'arborescence à recréer à la main : il déploie en un clin d\'œil toute la trame de base indispensable pour lancer un nouveau site web, rendant la création de projets à la fois ludique, instantanée et redoutablement efficace.</p>';
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
                if (details.niveau2.fonctionnalites && details.niveau2.fonctionnalites.length > 0) {
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

            if (projectName === 'skeletor-v1.0' || projectName === 'skeletor-v1.0-o2switch' || title.includes('skeletor')) {
                mediaWrapper.innerHTML = `<img id="overlay-img" src="images/capture-skeletor.png" alt="Aperçu Skeletor" style="width: 100%; height: auto; border-radius: 8px; display: block;">`;
            } else if (projectName === 'personator-v1.2' || title.includes('personator')) {
                mediaWrapper.innerHTML = `<img id="overlay-img" src="images/capture-personator.png" alt="Aperçu Personator" style="width: 100%; height: auto; border-radius: 8px; display: block;">`;
            } else {
                let finalImgSrc = imgSrc || `dashboard-designer/assets/img/photo-640x480.png`;
                if (details && details.niveau1 && details.niveau1.image && !title.includes('skeletor') && !title.includes('personator')) {
                    finalImgSrc = `dashboard-designer/assets/img/${details.niveau1.image}`;
                }
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

        // Fonctions pour l'overlay de zoom des images du journal (fond noir dense, curseur SVG rond miniature 24x24, scrollbar masquée)
        function openJournalImage(src) {
            document.getElementById('journal-overlay-img').src = src;
            document.getElementById('journal-img-overlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeJournalImage() {
            document.getElementById('journal-img-overlay').classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    </script>

</body>
</html>