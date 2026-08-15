<?php
/* ============================================================
   PALETTOR — Page de présentation complète du projet
   Utilise le template générique partials/page-detail.php
   ============================================================ */

$slug      = 'palettor';
$title     = 'palettor';
$subtitle  = 'Générateur et gestionnaire de palettes de couleurs pour le design web';
$statusKey = 'operational';
$technos   = ['JavaScript', 'CSS Custom Properties', 'Canvas API', 'JSON'];
$screenshot = 'images/capture-palettor.png';
$appHref   = 'palettor/';
$isStatic  = false;
$basePath  = '../';

$pitch = 'Palettor est né d\'un constat simple : les outils de gestion de palettes disponibles en ligne sont trop lourds, trop connectés, trop dépendants d\'un abonnement. Ce générateur de palettes autonome permet de créer, éditer, exporter et appliquer des harmonies colorées directement depuis le navigateur, sans aucune connexion réseau, avec une précision professionnelle.';

$sections = [
    [
        'title'      => 'Génération de Palettes et Harmonies',
        'body'       => '<p>L\'outil propose plusieurs modes de génération harmonique : complémentaire, triadique, tétradique, analogique et monochrome. Chaque palette est définie par un triplet OKLCH, permettant un contrôle précis de la luminosité perceptuelle, de la chroma et de la teinte — un espace colorimétrique bien supérieur au HSL classique.</p>
<ul>
<li>Modes harmoniques : complémentaire, triade, tétrade, analogique, monochrome</li>
<li>Prévisualisation temps réel sur des composants UI types</li>
<li>Export en formats CSS Custom Properties, SCSS et JSON</li>
<li>Extraction automatique des couleurs depuis un logo importé (Canvas API)</li>
</ul>',
    ],
    [
        'title'      => 'Extraction Chromatique depuis un Logo',
        'body'       => '<p>L\'import d\'un logo déclenche une analyse pixel par pixel via l\'API Canvas. L\'algorithme filtre automatiquement les blancs, transparences, tons neutres et artefacts d\'anti-aliasing pour extraire uniquement les couleurs "identifiantes" de la marque — les teintes distinctives qui portent l\'identité visuelle.</p>
<p>Un système de pondération empêche les couleurs dominantes d\'écraser les accents subtils (liserés, logos d\'icône). Le résultat est un tableau propre de codes HEX directement utilisables.</p>',
    ],
    [
        'title'      => 'Intégration avec l\'Atelier',
        'body'       => '<p>Palettor s\'intègre nativement avec les autres outils de l\'atelier via un format JSON commun. Les palettes créées peuvent être directement importées dans Skeletor pour la génération de thèmes CSS, ou dans Texturor pour l\'harmonie typographique. Un système de sauvegarde local (localStorage) garantit la persistance des palettes sans aucune dépendance réseau.</p>',
    ],
];

$isStatic = defined('FIREBASE_STATIC') && FIREBASE_STATIC;
$basePath = '../'; // fixe: depuis {slug}/ on remonte toujours d'un niveau

require __DIR__ . '/../partials/page-detail.php';