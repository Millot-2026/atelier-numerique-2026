<?php
// On définit la racine du projet actuel et le dossier de destination
$sourceDir = __DIR__;
$destDir   = __DIR__ . '/export-nuxit';

// ---------------------------------------------------------------
//  Fichiers et dossiers à exclure de l'export de production.
//
//  ✅ generator.php est INCLUS : c'est le point d'entrée principal.
//  ❌ admin.php exclu : interface de gestion interne (dev only).
//  ❌ _generator copy.php exclu : legacy.
//  ❌ export-nuxit.php exclu : script de packaging (inutile en prod).
//  ❌ verification.bat / lancer-export.bat exclus : outils Windows locaux.
// ---------------------------------------------------------------
$excludedFiles = [
    'admin.php',
    '_generator copy.php',
    'export-nuxit.php',
    'verification.bat',
    'lancer-export.bat',
    'export-nuxit - Raccourci.lnk',
    'desktop.ini',
    'README-AI.txt',
];
$excludedDirs = ['trash temp', '.git', 'export', 'export-nuxit', 'firebase-builder'];

// Fonction de copie récursive
function recursiveCopy($src, $dst, $excludedFiles, $excludedDirs) {
    if (!is_dir($src)) return;
    
    if (!file_exists($dst)) {
        mkdir($dst, 0777, true);
    }

    $dir = opendir($src);
    while(($file = readdir($dir)) !== false) {
        if(($file != '.') && ($file != '..')) {
            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . $file;

            if (is_dir($srcPath)) {
                if (!in_array($file, $excludedDirs)) {
                    recursiveCopy($srcPath, $dstPath, $excludedFiles, $excludedDirs);
                }
            } else {
                if (!in_array($file, $excludedFiles)) {
                    copy($srcPath, $dstPath);
                }
            }
        }
    }
    closedir($dir);
}

// Nettoyage préalable du dossier de destination s'il existe déjà
if (file_exists($destDir)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($destDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iterator as $path) {
        if ($path->isDir()) {
            rmdir($path->getRealPath());
        } else {
            unlink($path->getRealPath());
        }
    }
}

// Lancement de la copie propre
recursiveCopy($sourceDir, $destDir, $excludedFiles, $excludedDirs);

// ---------------------------------------------------------------
//  Post-traitement : fichiers à créer/écraser après la copie
// ---------------------------------------------------------------

// 1. Dossier export/ vide — requis par skeletor.php pour stocker les
//    arborescences générées. On crée juste le dossier + un .gitkeep.
$exportDir = $destDir . '/export';
if (!is_dir($exportDir)) {
    mkdir($exportDir, 0755, true);
}
file_put_contents($exportDir . '/.gitkeep', '');

// 2. index.php — Point d'entrée HTTP de l'app déployée.
//    Redirige vers generator.php (le vrai moteur de Skeletor).
//    Ainsi, accéder à skeletor.votredomaine.fr/ lance directement l'app.
$indexContent = <<<'PHP'
<?php
/**
 * index.php — Point d'entrée Skeletor v1.0
 * Redirige vers l'interface principale du générateur.
 */
header('Location: generator.php');
exit;
PHP;
file_put_contents($destDir . '/index.php', $indexContent);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Export Nuxit</title>
    <style>
        body { font-family: sans-serif; background: #1e1e1e; color: #fff; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: #2d2d2d; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); text-align: center; }
        h1 { color: #4CAF50; font-size: 22px; margin-bottom: 10px; }
        p { color: #ccc; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Export Nuxit Réussi !</h1>
        <p>Le site épuré a été généré avec succès dans le dossier <strong>export-nuxit</strong>.</p>
    </div>
</body>
</html>