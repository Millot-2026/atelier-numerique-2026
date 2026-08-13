<?php
/* ============================================================
   PALETTOR / DETAIL.PHP — Page de détail du projet
   ============================================================ */

$slug      = 'palettor';
$title     = 'palettor';
$subtitle  = 'Générateur et gestionnaire de palettes de couleurs pour le design web';
$statusKey = 'operational';
$technos   = ['JavaScript', 'CSS Custom Properties', 'Canvas API', 'JSON'];
$screenshot = 'images/images-workstation/01-header.png';
$appHref   = 'palettor/';
$isStatic  = false;
$basePath  = '../';

$pitch = 'Palettor est né d\'un constat simple : les outils de gestion de palettes disponibles en ligne sont trop lourds, trop connectés, trop dépendants d\'un abonnement. Ce générateur de palettes autonome permet de créer, éditer, exporter et appliquer des harmonies colorées directement depuis le navigateur, sans aucune connexion réseau, avec une précision professionnelle.';

$sections = [
    [
        'title'      => 'Génération de Palettes',
        'figure'     => 'images/images-workstation/01-header.png',
        'figcaption' => 'fig: interface de création de palette — sélecteur HSL',
        'body'       => '<p>L\'outil propose plusieurs modes de génération harmonique : complémentaire, triadique, tétradique, analogique et monochrome. Chaque palette est définie par un triplet HSL, permettant un contrôle précis de la teinte, de la saturation et de la luminosité sans passer par des codes hexadécimaux cryptiques.</p>
<ul>
<li>Modes harmoniques : complémentaire, triade, tétrade, analogique, monochrome</li>
<li>Prévisualisation temps réel sur des composants UI types</li>
<li>Export en formats CSS Custom Properties, SCSS et JSON</li>
</ul>',
    ],
    [
        'title'      => 'Intégration avec l\'Atelier',
        'body'       => '<p>Palettor s\'intègre nativement avec les autres outils de l\'atelier via un format JSON commun. Les palettes créées peuvent être directement importées dans Skeletor pour la génération de thèmes CSS, ou dans Texturor pour l\'harmonie typographique.</p>
<p>Un système de bibliothèque locale permet de sauvegarder jusqu\'à 50 palettes dans le localStorage du navigateur, avec export/import via des fichiers JSON portables.</p>',
    ],
];

$isStatic = defined('FIREBASE_STATIC') && FIREBASE_STATIC;
$basePath = '../'; // fixe: depuis {slug}/ on remonte toujours d'un niveau

require_once __DIR__ . '/../partials/page-detail.php';
