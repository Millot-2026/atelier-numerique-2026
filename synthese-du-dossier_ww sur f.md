# 🗂️ Synthèse des projets — `F:\_www`

> Généré automatiquement le **03/08/2026** — Analyse complète du répertoire `F:\_www` (clé USB).

---

## Vue d'ensemble

| # | Dossier | Nom du projet | Type | Technologie |
|---|---------|--------------|------|-------------|
| 1 | `index.php` *(racine)* | Tableau de bord central | Lanceur / Hub | PHP |
| 2 | `dashboard-designer` | WORKSTATION Dashboard | Application | PHP + JS + CSS |
| 3 | `cms-2026-v8-full` | CMS 2026 — Gestion de Projets & Études de cas | CMS éditorial | PHP (flat-file) |
| 4 | `modulor` | Modulor Workstation — Éditeur de Pages Modulaire | Éditeur visuel | PHP + JSON |
| 5 | `personator-v1.2` | Personator v1.2 — Générateur de Personas UX | Outil UX | PHP + JSON |
| 6 | `skeletor-v1.0` | Skeletor v1.0 — Générateur d'Architecture de Dossiers | Outil de scaffolding | PHP + JSON |
| 7 | `skeletor-v1.0-o2switch` | Skeletor v1.0 (variante o2switch) | Outil de scaffolding (déploiement) | PHP + JSON + BAT |
| 8 | `texturor` | Texturor — Laboratoire de Textures & Styles CSS | Outil de design | HTML + JS + CSS |
| 9 | `user_journey-v1.0` | User Journey v1.0 — Cartographie des Parcours Utilisateur | Outil UX | PHP + JS + JSON |
| 10 | `wordpress-portable` | WordPress Portable — Instance CMS Locale | CMS | PHP + WordPress + MySQL |
| 11 | `mon-site` | Mon Site — Site vierge | Gabarit | PHP + CSS |
| 12 | `mon-premier-site` | Mon Premier Site — Projet en Construction | Gabarit | PHP |
| 13 | `projet-client` | Projet Client — Espace de Travail Dédié | Gabarit client | PHP |

---

## Fiches détaillées

---

### 1. 🏠 Tableau de bord central — `index.php` (racine)

**Fichier principal :** `F:\_www\index.php`

**But / Objectif :**
Page d'accueil centrale du serveur local. Elle scanne automatiquement le dossier `_www` et liste tous les projets sous forme de cartes cliquables. Chaque carte affiche le nom du projet, son statut (en cours, terminé, etc.) et une miniature. Permet de naviguer rapidement entre tous les projets hébergés en local depuis un seul point d'entrée.

**Technologie :**
- `PHP` (scan de répertoire, gestion des statuts via `statuses.json`)
- `HTML` / `CSS` / `JavaScript`
- Données persistées en `JSON` (`statuses.json`, `projects.json`)

---

### 2. 🖥️ WORKSTATION Dashboard — `dashboard-designer`

**Fichier principal :** `F:\_www\dashboard-designer\index.php`

**But / Objectif :**
Tableau de bord personnel de type "cockpit" pour le développeur web, nommé **WORKSTATION**. Agrège en un seul écran une série de widgets utiles au quotidien : horloge, météo, palette de couleurs, gestionnaire de projets avec chronomètre Start/Stop, générateur de lorem ipsum, convertisseur px→rem, éditeur CodePen intégré, bloc-notes persistant, et lanceur rapide vers les projets locaux. Le scan automatique du dossier `_www` alimente la liste des projets actifs.

**Technologie :**
- `PHP` (rendu serveur, scan de répertoires, inclusion de widgets modulaires)
- `JavaScript` (LocalStorage pour la persistance, logique des widgets)
- `CSS` (système de grille en "3 Tiers" avec thème sombre)

---

### 3. 📰 CMS 2026 — `cms-2026-v8-full`

**Fichier principal :** `F:\_www\cms-2026-v8-full\index.php`

**But / Objectif :**
Interface d'administration flat-file conçue pour la gestion complète de projets éditoriaux (articles, études de cas). La page d'accueil présente une grille de cartes organisées par ordre naturel, chacune affichant titre, catégorie, date et résumé. En mode local, un panneau d'administration permet de créer, éditer ou supprimer des projets directement. En production, seul le contenu public est affiché. Un pipeline d'export génère un livrable statique prêt à déployer sur hébergement mutualisé.

**Technologie :**
- `PHP` (architecture flat-file, sans base de données SQL)
- Données en fichiers `data.php`
- `HTML` / `CSS` / `JavaScript`
- Export statique pour déploiement sur hébergement mutualisé (Nuxit)

---

### 4. 🧱 Modulor Workstation — `modulor`

**Fichier principal :** `F:\_www\modulor\index.php`

**But / Objectif :**
Environnement de composition visuelle basé sur des blocs modulaires. Permet de construire des pages en assemblant des lignes configurables (1, 2 ou 3 colonnes) contenant différents types de modules : notes textuelles, extraits de code CodePen, blocs lorem ipsum, explorateurs d'icônes FontAwesome, fils de discussion. Un Skin Engine intégré permet de basculer entre 6 thèmes graphiques (Cyber, Neumorph, Skeuomorph, Blueprint, Terminal, V6). La composition est persistée en JSON et exportable.

**Technologie :**
- `PHP` (rendu serveur, gestion des blocs)
- `JSON` (persistance des compositions)
- `HTML` / `CSS` / `JavaScript`
- Système de thèmes multiples (Skin Engine)

---

### 5. 🎭 Personator v1.2 — `personator-v1.2`

**Fichier principal :** `F:\_www\personator-v1.2\generator.php`

**But / Objectif :**
Outil de création et d'export de fiches personas UX/UI. Propose un formulaire structuré par niveaux de contenu pour définir profil, motivations, frustrations et habitudes d'un utilisateur type. La photo du persona peut être importée directement. Les configurations sont sauvegardables en JSON. La génération produit un dossier d'export complet contenant la fiche persona mise en forme, prête pour un livrable client.

**Technologie :**
- `PHP` (génération des fiches, gestion du formulaire, export)
- `JSON` (sauvegarde/rechargement des configurations)
- `HTML` / `CSS` / `JavaScript`
- Export de dossier complet

---

### 6. 🦴 Skeletor v1.0 — `skeletor-v1.0`

**Fichier principal :** `F:\_www\skeletor-v1.0\generator.php`

**But / Objectif :**
Outil de scaffolding pour la création rapide d'arborescences de fichiers et dossiers de projet. L'interface permet de définir une structure hiérarchique à plusieurs niveaux via un formulaire dynamique. Les configurations peuvent être nommées, sauvegardées en JSON et rechargées. La génération exporte l'arborescence complète dans un répertoire dédié. Idéal pour initialiser des projets web ou des livrables clients avec une structure cohérente.

**Technologie :**
- `PHP` (génération de l'arborescence sur le serveur)
- `JSON` (sauvegarde/chargement des templates)
- `HTML` / `CSS` / `JavaScript`

---

### 7. 🦴 Skeletor v1.0 o2switch — `skeletor-v1.0-o2switch`

**Fichier principal :** `F:\_www\skeletor-v1.0-o2switch\generator.php`

**But / Objectif :**
Variante de déploiement de Skeletor v1.0 adaptée à l'hébergeur o2switch. Mêmes fonctionnalités que la version standard, avec en plus un module d'export spécifique (`export-nuxit.php`) et des scripts de vérification/déploiement (`.bat`) pour faciliter la mise en ligne sur un hébergement mutualisé distant.

**Technologie :**
- `PHP` (génération d'arborescences, export adapté hébergeur)
- `JSON` (configurations)
- `HTML` / `CSS` / `JavaScript`
- Scripts `BAT` (automatisation déploiement Windows)

---

### 8. 🎨 Texturor — `texturor`

**Fichier principal :** `F:\_www\texturor\index.html`

**But / Objectif :**
Laboratoire de textures et styles CSS. Application de type catalogue permettant de parcourir, filtrer (par forme, couleur, matière, ambiance) et rechercher des recettes de textures CSS (backgrounds, patterns). Un éditeur SCSS/CSS intégré permet d'écrire, modifier et sauvegarder ses propres textures. Le code peut être copié en un clic. Les textures sont persistées localement et exportables en dossier complet.

**Technologie :**
- `HTML` pur (application 100% client-side, aucun PHP)
- `JavaScript` (logique catalogue, LocalStorage, filtres, copie)
- `CSS` / `SCSS` (styles et recettes de textures)

---

### 9. 🗺️ User Journey v1.0 — `user_journey-v1.0`

**Fichier principal :** `F:\_www\user_journey-v1.0\user_generator.php`

**But / Objectif :**
Application de modélisation de parcours utilisateurs (User Journey Map) pour équipes UX et produit. Propose un formulaire dynamique où chaque étape du parcours est ajoutée ligne par ligne, avec un aperçu en temps réel. Les parcours sont nommables, sauvegardables en JSON et rechargeables. Une vue de présentation dédiée (`presentation.php`) affiche le parcours finalisé dans un format propre pour un livrable client.

**Technologie :**
- `PHP` (rendu serveur, gestion des parcours)
- `JavaScript` (aperçu temps réel, dynamisme du formulaire)
- `JSON` (persistance des parcours)
- `HTML` / `CSS`

---

### 10. 📦 WordPress Portable — `wordpress-portable`

**Fichier principal :** `F:\_www\wordpress-portable\index.php`

**But / Objectif :**
Installation complète de WordPress en environnement local, configurée pour fonctionner hors ligne. Permet de développer, tester et prévisualiser un site WordPress intégralement sans hébergement distant. Inclut les répertoires `wp-admin`, `wp-content` et `wp-includes` dans leur version complète, ainsi qu'un `wp-config.php` pointant vers la base de données locale.

**Technologie :**
- `WordPress` (CMS complet, version portable)
- `PHP` (moteur WordPress)
- `MySQL` (base de données locale via XAMPP)
- Plugins et thèmes dans `wp-content`

---

### 11. 🌱 Mon Site — `mon-site`

**Fichier principal :** `F:\_www\mon-site\index.php`

**But / Objectif :**
Gabarit de site vierge à personnaliser. Page d'accueil minimaliste avec header, titre et contenu de base, liée à une feuille de style CSS. Sert de point de départ neutre pour démarrer rapidement un nouveau projet web sans structure préconçue.

**Technologie :**
- `PHP` (structure de page de base)
- `CSS` (feuille de style externe dans `assets/css/`)
- `HTML`

---

### 12. 🏗️ Mon Premier Site — `mon-premier-site`

**Fichier principal :** `F:\_www\mon-premier-site\index.php`

**But / Objectif :**
Page d'accueil provisoire d'un projet web en cours d'initialisation. Affiche un écran centré à fond sombre avec un titre, un message d'état et un badge « EN CONSTRUCTION » visible. Sert de marqueur de place : il suffit d'ajouter ses fichiers et de modifier `index.php` pour démarrer. Lien de retour vers le tableau de bord central en bas de page.

**Technologie :**
- `PHP`
- `HTML` / `CSS` intégré

---

### 13. 💼 Projet Client — `projet-client`

**Fichier principal :** `F:\_www\projet-client\index.php`

**But / Objectif :**
Dossier de projet client en cours de préparation. Affiche une page d'attente propre et professionnelle indiquant que l'environnement est configuré. Gabarit de base pour tout développement client sur ce serveur local : remplacer le contenu de `index.php` et organiser les ressources dans le dossier pour démarrer. Lien de retour vers le tableau de bord central.

**Technologie :**
- `PHP`
- `HTML` / `CSS`

---

## 📋 Contexte global

Ce répertoire `_www` est la **racine d'un serveur XAMPP portable** installé sur clé USB. Il constitue un environnement de développement web autonome et nomade comprenant :

- Un **hub central** (`index.php`) listant tous les projets
- Une suite d'**outils UX** propriétaires (Personator, User Journey, Skeletor, Modulor, Texturor)
- Un **CMS flat-file** maison (CMS 2026)
- Un **tableau de bord de productivité** (WORKSTATION Dashboard)
- Une **instance WordPress portable** pour les projets CMS
- Des **gabarits** prêts à personnaliser (mon-site, mon-premier-site, projet-client)

Un fichier `consigne.md` à la racine documente la stratégie de déploiement en ligne via **Firebase Hosting** pour transformer ces outils locaux en versions consultables en ligne, sans base de données SQL.
