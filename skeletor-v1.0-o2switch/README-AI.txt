# Skeletor v1.0 - Contexte Technique & Règles de Développement

## 1. Description du Projet
Skeletor v1.0 est un générateur d'arborescence web (HTML, CSS, JavaScript, PHP) avec gestion locale des sauvegardes au format JSON (dossier /core/backups/) et module d'administration pour la gestion et le nettoyage des projets.

## 2. Structure & Fichiers Clés
- generator.php : Interface principale de création d'arborescence et de sauvegarde.
- admin.php : Interface d'administration pour lister, charger ou supprimer les fichiers de configuration JSON.
- core/skeletor.php : Classe principale gérant la génération physique des dossiers et fichiers.
- core/backups/ : Dossier de stockage des configurations JSON.

## 3. Charte Graphique & UI (Règles Absolues)
- Fond général : #1a1a1a (sombre).
- Conteneurs : #222 avec bordures sombres.
- Boutons et accents : Orange (#f39c12).
- Titres principaux (H1) : Couleur #f39c12, taille 1.5em, parfaitement alignés et harmonisés entre l'application et l'administration.
- Bouton de retour dans l'admin : Intégré en haut à droite dans l'en-tête, stylisé en bouton assorti.

## 4. Contraintes de Code & Méthode de Travail
- Pas de refactorisation massive non demandée.
- Ne jamais toucher au texte de référence LOREM (le cas échéant).
- Conserver les commentaires d'origine dans le code.
- Ne pas proposer de commandes "git status" automatiques non sollicitées.
- Fournir le code complet corrigé si une modification est demandée, avec des explications simples et directes.