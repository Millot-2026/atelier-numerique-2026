<?php
/* ============================================================
   WORKSTATION / DETAIL.PHP — Page de détail du projet
   ============================================================ */

$slug      = 'workstation';
$title     = 'Workstation';
$subtitle  = 'Le cockpit central de l\'atelier nomade';
$statusKey = 'operational';
$technos   = ['PHP', 'JavaScript', 'CSS Grid', 'API Météo'];
$screenshot = 'images/images-workstation/01-header.png';
$appHref   = '#'; // Workstation est intégré à l'index, pas une app séparée
$isStatic  = false;
$basePath  = '../';

$pitch = 'Workstation est le poste de commandement ultime de l\'Atelier Numérique. Loin d\'être un simple tableau de bord, il incarne la philosophie du nomadisme numérique : tout ce dont un développeur a besoin, concentré en un écran unique, accessible instantanément depuis une clé USB, sans aucun compte cloud ni connexion obligatoire.';

$sections = [
    [
        'title'      => 'Architecture & Conception',
        'figure'     => 'images/images-workstation/01-header.png',
        'figcaption' => 'fig: vue de l\'interface Workstation — bandeau principal',
        'body'       => '<p>Workstation repose sur une architecture flat-file en PHP pur, sans base de données ni framework externe. L\'ensemble des données de configuration est persisté dans un fichier <code>statuses.json</code> local, ce qui garantit une portabilité absolue : copier le dossier suffit à transporter l\'intégralité du tableau de bord.</p>
<ul>
<li>Chargement dynamique de la liste des projets par scan du répertoire racine</li>
<li>Système de presets de mise en page (5 emplacements, sauvegarde AJAX)</li>
<li>Grille CSS 12 colonnes reconfigurable en temps réel</li>
</ul>',
    ],
    [
        'title'      => 'Fonctionnalités Clés',
        'body'       => '<p>Le panneau de contrôle latéral permet à l\'opérateur d\'activer ou désactiver chaque projet du journal, d\'ajuster sa largeur de colonne (de 4 à 12 colonnes), et de réordonner les articles par glissement. Ces configurations sont enregistrées en session et persistées côté serveur via AJAX, sans rechargement de page.</p>
<p>Le méga-menu à trois colonnes catégorise les projets par domaine (Pilotage, Outils Créatifs, Templates) et offre un accès direct à chaque page de détail depuis n\'importe quelle vue du journal.</p>',
    ],
];

$isStatic = defined('FIREBASE_STATIC') && FIREBASE_STATIC;
$basePath = '../'; // fixe: depuis {slug}/ on remonte toujours d'un niveau

require_once __DIR__ . '/../partials/page-detail.php';
