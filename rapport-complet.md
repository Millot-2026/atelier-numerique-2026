# Rapport Technique — Écosystème de Développement Nomade (Clé USB F:)

> **Généré le :** 04/08/2026 — Analyse directe et exhaustive du contenu de la clé USB `F:\`  
> **Périmètre :** Architecture globale, arborescence, rôles, scripts, statuts et liens entre modules

---

## Table des matières

1. [Vue d'ensemble de la clé](#1-vue-densemble-de-la-clé)
2. [Architecture globale](#2-architecture-globale)
3. [Couche Infrastructure (Serveur & Navigateur)](#3-couche-infrastructure-serveur--navigateur)
4. [Tableau de bord central — `indexv2.php`](#4-tableau-de-bord-central--indexv2php)
5. [Suite d'outils UX/Dev (Générateurs)](#5-suite-doutils-uxdev-générateurs)
6. [Modules de productivité](#6-modules-de-productivité)
7. [Répertoire d'exports (`/export/`)](#7-répertoire-dexports-export)
8. [Sites et gabarits de projets](#8-sites-et-gabarits-de-projets)
9. [Données persistantes et fichiers de référence](#9-données-persistantes-et-fichiers-de-référence)
10. [Stratégie de déploiement en ligne (Firebase / o2switch)](#10-stratégie-de-déploiement-en-ligne-firebase--o2switch)
11. [Suivi des statuts des modules](#11-suivi-des-statuts-des-modules)
12. [Liens et dépendances entre composants](#12-liens-et-dépendances-entre-composants)

---

## 1. Vue d'ensemble de la clé

| Propriété | Valeur |
|---|---|
| **Lettre de lecteur** | `F:\` |
| **Type** | Clé USB nomade (environnement de développement portable) |
| **Serveur local** | XAMPP Windows (dans `F:\server\xampp-windows\`) |
| **Racine web** | `F:\_www\` → `http://localhost/_www/` |
| **Navigateur portable** | Google Chrome Portable (`F:\core\chrome-lecteur-pc\`) |
| **Paradigme** | Zéro dépendance système — fonctionne sur n'importe quel PC Windows sans installation |
| **Gestion de version** | Git distribué (dépôt `.git` dans `_www` et dans chaque module) |
| **Base de données (si besoin)** | MySQL local via XAMPP — dump SQL dans `F:\sql\mich8332_wp569.sql` |

### Arborescence racine `F:\`

```
F:\
├── _www/                    ← Racine du serveur web local (DocumentRoot)
├── core/                    ← Outils portables (navigateur, VS Code)
│   ├── chrome-lecteur-pc/   ← Chrome Portable + lecteur.bat (lanceur)
│   ├── chrome-web-pc/       ← Chrome Portable (mode navigation normale)
│   ├── vscode/              ← VS Code Portable
│   └── MAC/                 ← Ressources spécifiques Mac
├── server/                  ← Serveurs web portables
│   ├── xampp-windows/       ← XAMPP pour Windows (Apache + PHP + MySQL)
│   ├── mac-server-runtime/  ← Runtime serveur pour macOS
│   ├── mac-tools/           ← Utilitaires Mac
│   └── backup/              ← Sauvegardes serveur
├── Data/                    ← Profils Chrome et données de session
│   └── profile/
├── sql/                     ← Dumps de bases de données
│   └── mich8332_wp569.sql   ← Dump WordPress (1,7 Mo)
├── _firebase_build/         ← Build de déploiement Firebase Hosting
├── firebase-builder/        ← Scripts de construction Firebase
├── 000-ARCHIVE-FIN-DE-FORMATION/ ← Archives historiques
├── READ-ME.md               ← Stratégie sécurité & modèle d'export
├── CONTEXTE.md              ← Contexte projet et arborescence clé
├── index.php                ← Redirection / point d'entrée racine
└── export-nuxit.php         ← Script d'export vers hébergement Nuxit
```

---

## 2. Architecture globale

La clé est organisée en **3 couches fonctionnelles** :

```
┌─────────────────────────────────────────────────────────────────────┐
│  COUCHE 1 — INFRASTRUCTURE                                          │
│  server/xampp-windows/ (Apache + PHP)                               │
│  core/chrome-lecteur-pc/ (navigateur portable)                      │
└──────────────────────────────┬──────────────────────────────────────┘
                               │ http://localhost/_www/
┌──────────────────────────────▼──────────────────────────────────────┐
│  COUCHE 2 — HUB CENTRAL                                             │
│  _www/indexv2.php  (tableau de bord, navigation, statuts)           │
│  _www/index.php    (version V1 du hub)                              │
└──────────────────────────────┬──────────────────────────────────────┘
                               │ inclut / pointe vers
┌──────────────────────────────▼──────────────────────────────────────┐
│  COUCHE 3 — MODULES & PROJETS                                       │
│                                                                     │
│  GÉNÉRATEURS UX/DEV          MODULES PRODUCTIVITÉ  SITES/GABARITS  │
│  ├── personator-v1.2/        ├── dashboard-designer/ ├── mon-site/  │
│  ├── skeletor-v1.0/          ├── modulor/            ├── mon-prem../│
│  ├── skeletor-v1.0-o2switch/ ├── cms-2026-v8-full/   ├── projet-cl./│
│  ├── user_journey-v1.0/      └── texturor/           └── wordpress-/│
│  └── export/  ← sorties des générateurs                            │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 3. Couche Infrastructure (Serveur & Navigateur)

### 3.1 Serveur Web — XAMPP Windows

- **Chemin :** `F:\server\xampp-windows\`
- **Rôle :** Fournit Apache (HTTP), PHP et MySQL en environnement portable
- **DocumentRoot :** Pointe sur `F:\_www\`
- **URL locale :** `http://localhost/_www/`
- **Démarrage :** Via `F:\01-xampp-control.exe.lnk` (raccourci XAMPP Control Panel)

### 3.2 Navigateur Portable — Chrome

- **Chemin :** `F:\core\chrome-lecteur-pc\`
- **Fichier lanceur :** `lecteur.bat`
  - Lance Chrome en mode `--app` (interface épurée, sans barre d'adresse)
  - URL cible : `http://localhost/_www/index.php`
- **Raccourci racine :** `F:\GoogleChrome.lnk` → version mode navigation standard
- **Usage :** Permet d'utiliser l'atelier depuis n'importe quel PC Windows sans rien installer

### 3.3 Éditeur de Code — VS Code Portable

- **Chemin :** `F:\core\vscode\`
- **Usage :** Développement et édition des fichiers depuis la clé

---

## 4. Tableau de bord central — `indexv2.php`

> **Chemin :** `F:\_www\indexv2.php`  
> **URL :** `http://localhost/_www/indexv2.php`  
> **Taille :** ~2 001 lignes / ~104 Ko

C'est le **cockpit principal de l'atelier**. Il remplace progressivement `index.php` (V1) et constitue la version évoluée du hub.

### 4.1 Fonctionnalités du tableau de bord

| Fonctionnalité | Description |
|---|---|
| **Scan dynamique** | Scanne `_www/` et liste tous les projets sous forme de cartes cliquables |
| **Gestion des statuts** | Lecture/écriture de `statuses.json` — badges cyclables (validé / opérationnel / en cours) |
| **Mega menu navigation** | Menu hamburger donnant accès aux sections du tableau par ancre |
| **Barre sticky** | Barre fixe (`.sticky-header-bar`) apparaissant au scroll via `IntersectionObserver` |
| **Section V1** | `<details>` listant les projets via boucle PHP directe |
| **Section V2** | `<details>` incluant `partials/section-v2.php` (cartes enrichies avec overlay de détails) |
| **Section Exports** | `<details open>` listant dynamiquement les dossiers de `_www/export/` avec badges types |
| **Overlay de détails** | Modale fullscreen (`#modulor-overlay`) affichant pitch, technos, contexte, fonctionnalités |
| **Suppression d'export** | Bouton `×` sur chaque carte d'export → AJAX `POST` → PHP supprime le dossier |
| **Format journalistique** | Mise en page inspirée de la grande presse (typographie, colonnes, rubriques) |

### 4.2 Architecture PHP de `indexv2.php`

```
Lignes 1–45    : Gestionnaires AJAX (delete_export + changement de statut)
Lignes 46–100  : Scan des projets, lecture statuses.json et projects.json
Lignes 100–1100: CSS complet inline (variables CSS, composants, responsive)
Lignes 1100+   : HTML — En-tête journalistique, sections, mega menu
Lignes 1449+   : Section V1 (Mes Projets Nomades)
Lignes 1495+   : Section V2 (Lanceur Projets, cartes enrichies)
Lignes 1510+   : Section Exports (scan /export/, détection badge 3 niveaux)
Lignes 1593+   : Overlay modal
Lignes 1607+   : JavaScript (deleteExport, carousel, mega menu, sticky bar, openOverlay)
```

### 4.3 Détection de badge des exports (logique 3 niveaux)

La section finale de `indexv2.php` identifie le type de chaque projet exporté par ordre de priorité décroissante :

1. **Niveau 1 (priorité absolue)** : lecture de `_meta.json` → champ `"source": "personator|skeletor|user-journey"`
2. **Niveau 2 (heuristique structure)** : présence de `persona.json`, `persona.html`, `journey.json`, ou `journey.html`
3. **Niveau 3 (fallback nom)** : `strpos()` sur le nom du dossier (mots-clés : `persona`, `personnator`, `journey`, `user-journey`)

### 4.4 Fichiers partiels inclus

- **`partials/section-v1.php`** *(non utilisé directement par indexv2)* — Logique V1 historique
- **`partials/section-v2.php`** — Rendu des cartes enrichies (inclus à la ligne ~1504)

### 4.5 Fichiers satellites de `indexv2.php`

| Fichier | Rôle |
|---|---|
| `statuses.json` | Persistance des statuts de chaque projet |
| `update-status.php` | Endpoint AJAX de mise à jour des statuts |
| `dashboard-designer/data/projects.json` | Métadonnées enrichies (pitch, technos, architecture) pour l'overlay |
| `images/` | Captures d'écran utilisées dans les overlays et les cartes |

---

## 5. Suite d'outils UX/Dev (Générateurs)

### 5.1 Personator v1.2 — Générateur de Personas UX

| Propriété | Valeur |
|---|---|
| **Chemin** | `F:\_www\personator-v1.2\` |
| **URL** | `http://localhost/_www/personator-v1.2/generator.php` |
| **Statut** | ✅ `validated` |
| **Stack** | PHP + JSON + HTML/CSS/JS |

**Rôle :** Création et export de fiches personas UX/UI. Formulaire structuré multi-niveaux (profil, motivations, frustrations, habitudes). Import de photo. Sauvegarde/rechargement de configuration JSON.

**Structure interne :**
```
personator-v1.2/
├── generator.php         ← Interface principale (formulaire + génération)
├── admin.php             ← Administration des personas sauvegardés
├── index.php             ← Redirection vers generator.php
├── info.json             ← Métadonnées du module (titre, description)
├── core/
│   ├── personator.php    ← Logique métier de génération
│   ├── exporter.php      ← Logique d'export vers /export/ + écriture _meta.json
│   ├── backups.php       ← Gestion des sauvegardes JSON
│   ├── backups/          ← Fichiers de configuration sauvegardés
│   └── .htaccess         ← Protection du répertoire core
├── dicts/                ← Dictionnaires de données (listes de valeurs)
├── assets/               ← CSS, JS, images du module
├── export/               ← Exports locaux au module (non versionnés)
└── _preview.js           ← Prévisualisation en temps réel
```

**Export :** Génère un dossier dans `F:\_www\export\` avec `_meta.json` → `{"source": "personator", ...}`.

---

### 5.2 Skeletor v1.0 — Générateur d'Architecture de Dossiers

| Propriété | Valeur |
|---|---|
| **Chemin** | `F:\_www\skeletor-v1.0\` |
| **URL** | `http://localhost/_www/skeletor-v1.0/generator.php` |
| **Statut** | ✅ `validated` |
| **Stack** | PHP + JSON + HTML/CSS/JS |

**Rôle :** Scaffolding — création d'arborescences de fichiers et dossiers via formulaire dynamique. Les structures sont nommables, sauvegardables en JSON et rechargées en un clic.

**Structure interne :**
```
skeletor-v1.0/
├── generator.php             ← Interface principale
├── admin.php                 ← Administration des templates
├── index.php                 ← Redirection
├── info.json                 ← Métadonnées
├── export-nuxit.php          ← Export adapté hébergeur Nuxit/o2switch
├── export-success.php        ← Page de confirmation après export
├── lancer-export.bat         ← Script BAT Windows pour lancement export
├── verification.bat          ← Script de vérification de l'arborescence
├── core/
│   ├── skeletor.php          ← Logique métier de génération
│   ├── backups.php           ← Gestion des sauvegardes
│   ├── backups/              ← Fichiers JSON sauvegardés
│   └── .htaccess
├── dicts/                    ← Dictionnaires de types de fichiers
├── assets/                   ← CSS, JS, images
└── export/                   ← Sorties locales
```

**Variante déploiement :** `skeletor-v1.0-o2switch/` — Clone adapté pour hébergement mutualisé (sans `.git`, avec `export-nuxit.php` intégré et scripts de vérification BAT).

---

### 5.3 User Journey v1.0 — Cartographie des Parcours Utilisateur

| Propriété | Valeur |
|---|---|
| **Chemin** | `F:\_www\user_journey-v1.0\` |
| **URL** | `http://localhost/_www/user_journey-v1.0/user_generator.php` |
| **Statut** | ✅ `validated` |
| **Stack** | PHP + JS + JSON + HTML/CSS |

**Rôle :** Modélisation de User Journey Maps. Formulaire dynamique avec ajout ligne par ligne des étapes du parcours. Aperçu en temps réel. Sauvegarde/rechargement JSON. Vue de présentation dédiée.

**Structure interne :**
```
user_journey-v1.0/
├── user_generator.php    ← Interface principale (formulaire)
├── presentation.php      ← Vue de présentation (livrable client)
├── admin.php             ← Administration des journeys sauvegardés
├── index.php             ← Page d'accueil du module
├── script.js             ← Logique dynamique du formulaire
├── info.json             ← Métadonnées
├── core/                 ← Logique serveur
├── dicts/                ← Données de référence
├── assets/               ← Ressources statiques
└── export/               ← Sorties locales
```

---

### 5.4 Texturor — Laboratoire de Textures CSS

| Propriété | Valeur |
|---|---|
| **Chemin** | `F:\_www\texturor\` |
| **URL** | `http://localhost/_www/texturor/index.html` |
| **Statut** | ✅ `validated` |
| **Stack** | HTML pur + JavaScript + CSS/SCSS (100% client-side, aucun PHP) |

**Rôle :** Catalogue et éditeur de recettes de textures CSS. Filtres par forme/couleur/matière/ambiance. Éditeur SCSS intégré. Copie en un clic. Persistance en LocalStorage. Export en dossier.

**Structure interne :**
```
texturor/
├── index.html            ← Application complète (self-contained)
├── data/                 ← Catalogue de textures (JSON)
├── js/                   ← Logique catalogue, filtres, LocalStorage
├── scss/                 ← Sources SCSS
└── static/               ← Assets statiques
```

> **Note :** Seul module de la suite entièrement statique — déployable tel quel sur Firebase Hosting sans adaptation.

---

## 6. Modules de productivité

### 6.1 Dashboard Designer — WORKSTATION

| Propriété | Valeur |
|---|---|
| **Chemin** | `F:\_www\dashboard-designer\` |
| **URL** | `http://localhost/_www/dashboard-designer/index.php` |
| **Statut** | ✅ `validated` |
| **Stack** | PHP + JS (LocalStorage) + CSS |

**Rôle :** Tableau de bord cockpit personnel de type "Workstation". Agrège en un seul écran une série de widgets utiles au développement quotidien.

**Widgets disponibles (répertoire `widgets/`) :**

| Fichier widget | Rôle |
|---|---|
| `w-clock.php` | Horloge en temps réel |
| `w-weather.php` | Météo en direct |
| `w-calendar.php` | Calendrier |
| `w-notes.php` | Bloc-notes persistant (LocalStorage) |
| `w-palette.php` | Palette de couleurs |
| `w-px-to-rem.php` | Convertisseur px → rem |
| `w-lorem.php` | Générateur de Lorem Ipsum |
| `w-codepen.php` | Éditeur CodePen intégré |
| `w-fonts-tester.php` | Testeur de polices |
| `w-lab.php` | Laboratoire de code (éditeur live) |
| `w-lab-preview.php` | Prévisualisation du laboratoire |
| `w-session.php` | Chronomètre Start/Stop de session |
| `w-projets.php` | Lanceur rapide vers les projets locaux |
| `w-roadmap.php` | Suivi de roadmap / tâches |
| `w-veille.php` | Bloc de veille technologique |
| `w-resources.php` | Ressources et liens utiles |
| `w-add-project.php` | Ajout rapide d'un projet |

**Données persistées :**
- `data/projects.json` — Métadonnées enrichies des projets (alimentent aussi les overlays de `indexv2.php`)

---

### 6.2 CMS 2026 v8-full — CMS Éditorial Flat-File

| Propriété | Valeur |
|---|---|
| **Chemin** | `F:\_www\cms-2026-v8-full\` |
| **URL** | `http://localhost/_www/cms-2026-v8-full/index.php` |
| **Statut** | ✅ `validated` |
| **Stack** | PHP flat-file (sans SQL) + HTML/CSS/JS |

**Rôle :** CMS complet pour la gestion d'articles et d'études de cas. Grille de cards avec CRUD complet en local. Protection par filtrage IP en production. Pipeline d'export statique.

**Architecture interne :**
```
cms-2026-v8-full/
├── index.php               ← Accueil public (liste des articles)
├── article.php             ← Vue d'un article
├── articles.php            ← Liste des articles (vue alternative)
├── methodologie.php        ← Page statique méthodologie
├── admin.php               ← Interface d'administration (CRUD)
├── admin/                  ← Sous-modules d'administration
├── content/                ← Articles (dossiers flat-file, chacun = 1 article)
├── core/
│   ├── config.php          ← BASE_URL, ASSETS_URL, constantes système
│   └── PUSH.md             ← Protocole Git
├── includes/               ← Fichiers PHP partagés (header, footer)
├── assets/                 ← CSS, JS, images
├── EXPORT_PRODUCTION_NUXIT/ ← Livrable statique versionné pour production
├── MANUEL-TECHNIQUE.md     ← Documentation complète du CMS
├── branches.md             ← Historique des branches Git
└── lexique.md              ← Dictionnaire des concepts
```

**Principe de sécurité :** En production, les fichiers `data.php` sont protégés par filtrage IP. Surface d'attaque nulle (aucun formulaire exposé en production).

---

### 6.3 Modulor Workstation — Éditeur de Pages Modulaire

| Propriété | Valeur |
|---|---|
| **Chemin** | `F:\_www\modulor\` |
| **URL** | `http://localhost/_www/modulor/index.php` |
| **Statut** | ✅ `validated` |
| **Stack** | PHP + JSON + HTML/CSS/JS |

**Rôle :** Éditeur visuel de pages basé sur des blocs modulaires. Lignes configurables (1, 2 ou 3 colonnes). Skin Engine (6 thèmes). Persistance JSON. Export de la composition en dossier complet.

**Blocs disponibles (répertoire `blocks/`) :**
- `w-notes/` — Bloc de notes texte
- `w-codepen/` — Extrait de code / éditeur CodePen
- `w-lorem/` — Générateur Lorem Ipsum
- `w-fontawesome/` — Explorateur d'icônes FontAwesome

**Skin Engine :** 6 thèmes graphiques commutables à la volée — Cyber, Neumorph, Skeuomorph, Blueprint, Terminal, V6.

---

## 7. Répertoire d'exports (`/export/`)

> **Chemin :** `F:\_www\export\`  
> **URL de base :** `http://localhost/_www/export/`

Ce dossier est la **destination commune** de tous les générateurs. Chaque export crée un sous-dossier autonome.

### 7.1 Contenu actuel

| Dossier | Source (`_meta.json`) | Contenu | Statut |
|---|---|---|---|
| `bio-christophe-millot/` | `personator` | `index.html` + `_meta.json` | ✅ Index trouvé |
| `new/` | `skeletor` | `index.php`, `contact.php`, `mentions-legales.php`, `assets/`, `_meta.json` | ✅ Index trouvé |

### 7.2 Structure type d'un export

Chaque dossier d'export contient systématiquement :
- `_meta.json` — Fichier de signature : `{"source": "personator|skeletor|user-journey", "generated_at": "ISO-8601"}`
- `index.html` ou `index.php` — Point d'entrée du projet exporté
- Ressources propres au projet (CSS, images, etc.)

### 7.3 Gestion depuis le tableau de bord

- **Affichage :** Section `"Mes Projets Générés & Exports"` de `indexv2.php` (ligne 1510+)
- **Suppression :** Bouton `×` → `fetch()` POST → handler PHP ligne 22 de `indexv2.php` → `rmdir()` récursif
- **Badge auto-détecté :** Via `_meta.json` (Niveau 1) ou heuristique fichier/nom (Niveaux 2-3)

---

## 8. Sites et gabarits de projets

### 8.1 WordPress Portable

| Propriété | Valeur |
|---|---|
| **Chemin** | `F:\_www\wordpress-portable\` |
| **URL** | `http://localhost/_www/wordpress-portable/` |
| **Stack** | WordPress + PHP + MySQL (via XAMPP) |
| **Dump BDD** | `F:\sql\mich8332_wp569.sql` (1,7 Mo) |

Installation WordPress complète en local. `wp-config.php` configuré pour la BDD locale. Répertoire `wp-admin.zip` (35 Mo) présent pour restauration.

---

### 8.2 Gabarits de démarrage

| Dossier | Rôle | Statut |
|---|---|---|
| `mon-site/` | Gabarit vierge minimaliste (PHP + CSS) | Structurel |
| `mon-premier-site/` | Marqueur "En construction" — à remplacer | `operational` |
| `projet-client/` | Espace de travail client — page d'attente propre | Structurel |
| `Projet-demo-pour-site-en-ligne/` | Démo pour site mis en ligne (avec `img/`) | Démo |

---

## 9. Données persistantes et fichiers de référence

### 9.1 `statuses.json` — Statuts des projets

**Chemin :** `F:\_www\statuses.json`

```json
{
  "cms-2026-v8-full":    "validated",
  "user_journey-v1.0":  "validated",
  "skeletor-v1.0":      "validated",
  "personator-v1.2":    "validated",
  "texturor":           "validated",
  "dashboard-designer": "validated",
  "mon-premier-site":   "operational",
  "modulor":            "validated",
  "images":             "progress"
}
```

- **`validated`** → Badge vert "✅ Validé" (module stable et fonctionnel)
- **`operational`** → Badge orange "⚡ Opérationnel" (en service, perfectible)
- **`progress`** → Badge rouge "🔴 En cours" (en développement)
- Mis à jour en temps réel par `update-status.php` (AJAX depuis les cartes)

---

### 9.2 `dashboard-designer/data/projects.json`

**Chemin :** `F:\_www\dashboard-designer\data\projects.json`  
**Rôle :** Métadonnées enrichies des projets pour alimenter les overlays de `indexv2.php`.

Structure par projet : `niveau1` (pitch + technos), `niveau2` (contexte + fonctionnalités), `niveau3` (architecture + environnement + roadmap).

---

### 9.3 `_meta.json` (dans chaque export)

**Présent dans :** chaque sous-dossier de `_www/export/`  
**Format :** `{"source": "personator|skeletor|user-journey", "generated_at": "2026-08-04T13:10:52+02:00"}`  
**Rôle :** Signature d'origine — permet au tableau de bord d'identifier le type de chaque export sans ambiguïté.

---

### 9.4 `info.json` (dans chaque module)

**Présent dans :** tous les modules générateurs  
**Format :** `{"title": "...", "description": "...", "screenshot": "..."}`  
**Rôle :** Métadonnées lisibles par le hub (`indexv2.php`) pour afficher description et capture dans les overlays.

---

## 10. Stratégie de déploiement en ligne (Firebase / o2switch)

### 10.1 Philosophie — Règle d'or : Zéro stockage serveur

> *Source : `F:\READ-ME.md`*

- **Pas de stockage persistant sur l'hébergeur** (protection o2switch contre saturation et failles)
- **Honeypot anti-spam** dans les formulaires de génération (champ caché invisible)
- **Double option client :**
  - **Option A** — Impression/Diffusion : HTML propre avec `@media print`
  - **Option B** — Archivage/Réutilisation : export JSON localStorage (rechargeable l'année suivante)

### 10.2 Cible Firebase Hosting

> *Source : `F:\_www\consigne.md`*

| Étape | Statut |
|---|---|
| Analyse de la structure de la clé | ✅ Réalisé |
| Rédaction cahier des charges | ✅ Réalisé |
| Nettoyage et isolation des sources | ⬜ À faire |
| Adaptation client-side (FileReader / téléchargement JSON) | ⬜ À faire |
| Configuration `firebase.json` | ⬜ À faire |
| Déploiement et validation | ⬜ À faire |

**Principes de la version Firebase :**
- Sites de consultation : statiques, lecture seule
- Utilitaires dynamiques (Skeletor, etc.) : import/export JSON par le navigateur — zéro écriture serveur
- Dossier `F:\_firebase_build\` : répertoire de build cible

### 10.3 Variante o2switch (Skeletor déjà déployé)

`skeletor-v1.0-o2switch/` contient la variante déjà adaptée à un hébergement mutualisé Nuxit/o2switch avec `export-nuxit.php` et scripts `verification.bat`.

---

## 11. Suivi des statuts des modules

| # | Module | Dossier | Statut | Stack principale |
|---|---|---|---|---|
| 1 | **Hub V1** | `index.php` | ✅ Stable | PHP |
| 2 | **Hub V2** | `indexv2.php` | 🔄 Actif (évolutions régulières) | PHP + JS |
| 3 | **Workstation Dashboard** | `dashboard-designer/` | ✅ `validated` | PHP + JS |
| 4 | **CMS 2026** | `cms-2026-v8-full/` | ✅ `validated` | PHP flat-file |
| 5 | **Modulor** | `modulor/` | ✅ `validated` | PHP + JSON |
| 6 | **Personator v1.2** | `personator-v1.2/` | ✅ `validated` | PHP + JSON |
| 7 | **Skeletor v1.0** | `skeletor-v1.0/` | ✅ `validated` | PHP + JSON |
| 8 | **Skeletor o2switch** | `skeletor-v1.0-o2switch/` | ✅ Stable | PHP + BAT |
| 9 | **Texturor** | `texturor/` | ✅ `validated` | HTML/JS pur |
| 10 | **User Journey v1.0** | `user_journey-v1.0/` | ✅ `validated` | PHP + JS |
| 11 | **WordPress Portable** | `wordpress-portable/` | 🟡 Local uniquement | WordPress + MySQL |
| 12 | **Mon Premier Site** | `mon-premier-site/` | 🟡 `operational` | PHP |
| 13 | **Mon Site** | `mon-site/` | 🔵 Gabarit | PHP + CSS |
| 14 | **Projet Client** | `projet-client/` | 🔵 Gabarit | PHP |

---

## 12. Liens et dépendances entre composants

```
indexv2.php
├── lit ──────────────→ statuses.json             (statuts projets)
├── lit ──────────────→ dashboard-designer/data/projects.json  (overlay détails)
├── écrit ────────────→ statuses.json             (via update-status.php AJAX)
├── supprime ─────────→ _www/export/[dossier]/    (via AJAX delete_export)
├── inclut ───────────→ partials/section-v2.php   (cartes enrichies)
├── affiche ──────────→ images/                   (captures d'écran overlays)
└── scanne ───────────→ _www/export/              (section "Projets Générés")
    └── lit ──────────→ export/[proj]/_meta.json  (détection badge type)

Générateurs (personator / skeletor / user_journey)
├── écrivent ─────────→ _www/export/[nom-projet]/ (dossier complet)
│   ├── index.html / index.php
│   └── _meta.json     {"source": "personator|skeletor|user-journey"}
└── lisent ───────────→ core/backups/*.json        (configurations sauvegardées)

dashboard-designer/
├── scanne ───────────→ _www/                     (liste des projets actifs)
└── écrit ────────────→ data/projects.json         (métadonnées projets pour overlays)

cms-2026-v8-full/
├── lit/écrit ────────→ content/[article]/data.php  (flat-file, pas de SQL)
└── génère ───────────→ EXPORT_PRODUCTION_NUXIT/    (livrable statique)

wordpress-portable/
└── nécessite ────────→ MySQL (XAMPP) + sql/mich8332_wp569.sql
```

---

*Rapport généré par analyse directe des fichiers — Clé USB F:\ — 04/08/2026*
