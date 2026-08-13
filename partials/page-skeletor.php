<?php
/* ============================================================
   SKELETOR / DETAIL.PHP — Page de détail du projet Skeletor
   ============================================================ */

$slug       = 'skeletor-v1.0';
$title      = 'skeletor-v1.0';
$subtitle   = 'Générateur d\'arborescence et structureur de projets web nomades';
$statusKey  = 'operational';
$technos    = ['PHP', 'JavaScript', 'HTML5', 'CSS3', 'Flat-File'];
$screenshot = 'images/capture-skeletor.png';
$appHref    = 'skeletor-v1.0/';
$isStatic   = false;
$basePath   = '../';

$pitch = 'Skeletor v1.0 est un outil de pilotage et de génération d\'arborescence conçu pour initialiser instantanément la structure de vos projets web. En s\'appuyant sur une interface modulaire en ligne, il permet d\'agencer l\'arborescence des dossiers, des pages PHP, des styles CSS et des ressources SCSS avant de déployer l\'ensemble sur le système de fichiers local sans aucune dépendance cloud.';

$sections = [
    [
        'title'      => 'Interface de Composition et Blocs Modulaires',
        'figure'     => 'images/capture-skeletor.png',
        'figcaption' => 'fig. 2 — Tableau de bord principal de Skeletor et agencement des blocs d\'arborescence',
        'body'       => '<p>L\'interface principale offre une vue d\'ensemble claire pour modéliser l\'architecture du site. Chaque ligne représente un composant (dossier, fichier PHP, feuille de style CSS ou SCSS) rattaché dynamiquement à un parent au sein de l\'arborescence.</p>
<ul>
<li>Ajout, suppression et réorganisation fluide des lignes de structure</li>
<li>Sélection des extensions et des emplacements cibles (racine ou sous-dossiers)</li>
<li>Sauvegarde rapide et chargement de projets vierges ou pré-configurés</li>
</ul>',
    ],
    [
        'title'      => 'Génération Automatisée et Déploiement Local',
        'body'       => '<p>Une fois la structure validée, le bouton de génération déploie instantanément l\'arborescence complète sur le serveur local (XAMPP / clé USB). Cet automatiseur garantit un gain de temps précieux et un respect absolu des conventions de nommage pour tous les nouveaux développements nomades.</p>',
    ],
];

$isStatic = defined('FIREBASE_STATIC') && FIREBASE_STATIC;
$basePath = '../';

require_once __DIR__ . '/../partials/page-detail.php';