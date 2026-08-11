# SOS Diagnostic Expert : Affichage des images de l'export autonome

## 1. Inadéquation des chemins HTML vs Structure de l'archive

**Analyse de la structure actuelle :**
- Le PHP copie physiquement `_www/images/` vers `dossier-final-export-o2switch/images/`. Cette copie **fonctionne parfaitement** (les fichiers sont bien présents au bit près).
- Le HTML génère des balises `<img src="images/images-cms/01-desktop-capture-cms-hero.png">`. Ce chemin relatif strict **est techniquement parfait** pour un dossier autonome. 

**Pourquoi cela casse-t-il alors ? (La cause racine)**
Il y a trois raisons simultanées à cet échec :
1. **L'omission de 8 projets sur 11 (Code PHP tronqué)** : Dans votre dernier correctif, vous avez supprimé le bloc `elseif` dynamique qui générait les images des autres projets (Skeletor, Personator, etc.). Pour ces projets, **aucune balise `<img>` n'est générée** dans le HTML. Ils sont donc invisibles.
2. **L'oubli des assets du Dashboard** : Votre `recursiveCopy` copie `_www/images/`, mais il **ne copie pas** les images de fallback situées dans `_www/dashboard-designer/assets/img/`. Si le HTML tente de les charger localement, elles sont physiquement absentes de l'archive.
3. **Le piège du "Trailing Slash" (Contexte réseau)** : Si vous visualisez l'archive sur XAMPP via l'URL `http://localhost/.../dossier-final-export-o2switch` (sans le `/` final), le navigateur considère que vous êtes dans le dossier parent (`export-vers-o2switch`). Le chemin relatif `images/...` est alors résolu à côté du dossier final, provoquant une erreur 404. Il faut accéder directement à `/index.php` ou ajouter le `/` final.

---

## 2. Conflit de contexte d'exécution (URL courante vs DIR)

**Les chemins relatifs doivent-ils être préfixés par un attribut de racine (`/`) ?**
**NON, SURTOUT PAS.** 
Si vous utilisez `/images/...`, cela pointera vers la racine absolue du domaine web courant. 
- Sur XAMPP, cela ciblera `http://localhost/images/` (ce qui échouera si l'atelier est dans un sous-dossier de `htdocs`). 
- Sur o2switch, cela obligerait l'archive à être déployée **strictement à la racine public_html**, ce qui brise le concept d'"archive autonome" (qui doit pouvoir fonctionner n'importe où, même dans un sous-dossier ou sur une clé USB via un simple double-clic).

**La règle d'or (Chemin relatif strict) :**
La syntaxe `src="images/images-cms/..."` (ou `src="./images/..."`) est la seule syntaxe valide pour garantir une portabilité à 100%.

---

## 3. Validation de la boucle d'injection

**Concordance des noms de fichiers :**
Les chaînes concaténées dans vos clauses `if` (`01-desktop-capture-cms-hero.png`, `01-header.png`, etc.) correspondent au caractère près aux fichiers listés sur le disque dur. Il n'y a pas d'erreur typographique.

**Le vrai problème de la boucle :**
Coder les balises `<img>` en dur via une série de `if ($p['name'] === '...')` détruit l'évolutivité du script et masque la source du problème pour les autres projets.

---

## 4. La Solution Technique Exacte (Méthode de résolution)

Pour résoudre définitivement ce bug, le futur code correctif devra appliquer cette stratégie en 3 étapes :

1. **Double Copie Physique :**
   Le script `export-journal.php` devra exécuter `recursiveCopy` sur `_www/images/`, mais **aussi** sur `_www/dashboard-designer/assets/img/` (en le copiant vers `dossier-final-export-o2switch/images/dashboard/` par exemple).

2. **Restauration de la boucle HTML Dynamique :**
   Au lieu de `if/elseif` codés en dur, la boucle PHP doit utiliser systématiquement `$details['niveau1']['image']`.
   
3. **Génération d'un chemin unifié :**
   Le script vérifiera d'où provient l'image (dossier CMS, Modulor, ou Dashboard) et écrira un chemin relatif strict (ex: `src="images/cms/mon-image.png"` ou `src="images/dashboard/mon-image.png"`).

*J'attends votre lecture de ce diagnostic et votre feu vert (validation) pour procéder à la réécriture complète de la boucle et de la logique de copie dans `export-journal.php`.*
