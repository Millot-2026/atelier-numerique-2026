# Guide de Fabrication : Version en Ligne (Firebase) de la suite Outils

## 1. Objectif du projet
* **Référence absolue :** La clé USB reste la version "full option" et autonome (XAMPP / PHP / Fichiers plats).
* **Version en ligne (Firebase) :** Une déclinaison sécurisée, consultable et interactive sans base de données serveur persistante, immunisée contre les bots.

## 2. Stratégie de sécurité par type d'outil
* **Sites de simple consultation :** Affichage statique en lecture seule pour le grand public.
* **Utilitaires dynamiques (ex: Skeletor) :** 
  * Interface d'administration vide par défaut à chaque chargement en ligne.
  * Zéro écriture/stockage direct sur le serveur Firebase.
  * Fonctionnement par **Import / Export** de fichiers de configuration (JSON / Archive ZIP) gérés directement par le navigateur de l'utilisateur.

## 3. Étapes de préparation du chantier
1. **Mise au point de la structure de la clé :** Analyse ciblée, niveau par niveau, des dossiers et fichiers sources pour identifier clairement ce qui doit être exporté ou exclu.
2. **Nettoyage :** Isoler les codes sources et retirer tous les scripts PHP d'écriture serveur non compatibles avec Firebase Hosting.
3. **Refonte Client-Side (pour les utilitaires) :** Intégrer la lecture/écriture des fichiers JSON via JavaScript pur (FileReader / Téléchargement direct).
4. **Configuration Firebase :** Préparer le fichier `firebase.json` pour un déploiement d'un site statique purement sécurisé.
5. **Validation :** Tester le workflow complet (visiteur vs utilisateur nomade avec son fichier JSON).

---

## 4. Synthèse des tâches effectuées
* [x] Mise au point et analyse de la structure de la clé USB : Scan du dossier `skeletor-v1.0` (`admin.php`, `generator.php`, `index.php`, `core`, `dicts`, `export`, `assets`, etc.).
* [x] Rédaction et validation du cahier des charges initial
* [ ] Isolation et nettoyage des codes sources (suppression du PHP serveur) : Analyse des scripts PHP de Skeletor à neutraliser.
* [ ] Adaptation des utilitaires pour l'import/export de fichiers JSON en JavaScript pur
* [ ] Configuration du fichier `firebase.json`
* [ ] Déploiement et tests de validation sur Firebase Hosting