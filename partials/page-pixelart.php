<?php
/* ============================================================
   PIXELART / DETAIL.PHP — Page de détail du projet Pixel Art
   ============================================================ */

$slug       = 'pixelart';
$title      = 'pixelart';
$subtitle   = 'Éditeur de pixel art et générateur de sprites pour le web et le jeu';
$statusKey  = 'progress';
$technos    = ['JavaScript', 'Canvas API', 'CSS', 'HTML5', 'IndexedDB'];
$screenshot = 'images/capture-pixelart-desktop.png';
$appHref    = 'pixelart/';
$isStatic   = false;
$basePath   = '../';

$pitch = 'Pixelart est un éditeur de pixel art entièrement navigateur, conçu pour créer des icônes, des sprites et des tilesets directement exploitables dans des projets web et des jeux indépendants. Sa grille de dessin fluide et ses outils d’édition précis — crayon, gomme, remplissage, sélection — permettent de produire des visuels pixel-perfect sans jamais sortir du navigateur.';

$sections = [
    [
        'title'      => 'Éditeur et Cockpit de Pilotage',
        'figure'     => 'images/capture-pixelart-desktop.png',
        'figcaption' => 'fig. 2 — Interface principale et zone de dessin de l\'éditeur',
        'body'       => '<p>L\'espace de travail bureau organise l\'éditeur autour d\'un cockpit structuré : la zone de gauche regroupe les outils, la palette de couleurs dynamique et les réglages de grille, tandis que le centre accueille le canevas de dessin sur fond damier. La barre de contrôle des arrière-plans modèles est fixée proprement au bas de la zone de réglages.</p>
<ul>
<li>Outils intégrés : crayon, gomme, remplissage au seing et pipette de couleur</li>
<li>Gestion dynamique des nuances et palettes personnalisables</li>
<li>Configuration rapide de la résolution de grille (8x8 à 64x64)</li>
</ul>',
    ],
    [
        'title'      => 'Mode Nomade et Adaptation Mobile',
        'figure'     => 'images/capture-pixelart-mobile.png',
        'figcaption' => 'fig. 3 — Adaptation de l\'interface en mode responsive et écrans tactiles',
        'body'       => '<p>Pensé pour le développement nomade sur clé USB, l\'outil intègre une adaptation fluide sur les petits écrans et terminaux tactiles, garantissant une ergonomie irréprochable et un confort de tracé en toutes circonstances.</p>',
    ],
    [
        'title'      => 'Exportation et Intégration',
        'body'       => '<p>Les créations peuvent être exportées instantanément au format PNG transparent. Les données de projets et les configurations d\'arrière-plan sont sauvegardées en local de manière persistante, permettant de reprendre son travail à tout moment sans dépendance réseau.</p>',
    ],
];

$isStatic = defined('FIREBASE_STATIC') && FIREBASE_STATIC;
$basePath = '../';

require_once __DIR__ . '/../partials/page-detail.php';