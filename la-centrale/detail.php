<?php
/* ============================================================
   LA-CENTRALE — Page complète et intégrée au journal
   ============================================================ */
$slug       = 'la-centrale';
$title      = 'la-centrale';
$subtitle   = 'Hub de pilotage centralisé, agrégateur de modules et station de travail nomade';
$statusKey  = 'operational';
$technos    = ['PHP', 'JavaScript', 'HTML5', 'CSS3', 'Flat-File'];
$screenshot = '../images/capture-workstation-dashboard.png';
$appHref    = 'la-centrale/';
$isStatic   = false;
$basePath   = '../';

$pitch = 'La Centrale (Workstation) constitue le cœur névralgique et l\'agrégateur universel de l\'atelier nomade. Conçu pour centraliser l\'accès à l\'ensemble des modules de l\'écosystème, il met à disposition un tableau de bord modulaire regroupant l\'accès direct aux applications, des widgets d\'information et des outils d\'aide au design.';

// C'est ici que l'on définit les sections pour qu'elles soient dans le journal
$sections = [
    [
        'title'      => '1. Gestion des Personas',
        'figure'     => '../images/capture-personator.png',
        'figcaption' => 'fig. 1 — Interface Personator v1.2 pour la modélisation des profils utilisateurs',
        'body'       => '<p>Le module Personator permet de définir et de structurer les besoins utilisateurs. Il assure que chaque projet est parfaitement aligné avec les contraintes et objectifs réels.</p>',
    ],
    [
        'title'      => '2. Édition Pixel Art (Bureau)',
        'figure'     => '../images/capture-pixelart-desktop.png',
        'figcaption' => 'fig. 2 — Éditeur PixelArt en mode bureau : cockpit de design complet',
        'body'       => '<p>L\'espace de travail dédié au pixel art permet une création fluide avec une gestion précise de la grille, des couleurs et des outils de dessin professionnels.</p>',
    ],
    [
        'title'      => '3. Édition Pixel Art (Nomade/Mobile)',
        'figure'     => '../images/capture-pixelart-mobile.png',
        'figcaption' => 'fig. 3 — Adaptation mobile de PixelArt pour le travail en situation nomade',
        'body'       => '<p>Cette version est optimisée pour garantir une ergonomie parfaite sur écrans tactiles et petits formats, sans perdre aucune fonctionnalité de création.</p>',
    ],
    [
        'title'      => '4. Génération d\'arborescence',
        'figure'     => '../images/capture-skeletor.png',
        'figcaption' => 'fig. 4 — Skeletor v1.0 : le générateur d\'arborescence pour initialiser vos projets',
        'body'       => '<p>Skeletor finalise votre workflow en générant automatiquement la structure de dossier et de fichiers propre à chaque nouvelle application, garantissant une cohérence totale dans l\'atelier.</p>',
    ],
];

require __DIR__ . '/../partials/page-detail.php';