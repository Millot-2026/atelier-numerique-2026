<?php
set_time_limit(0);

$sourceDir = __DIR__;
$destDir = __DIR__ . "/export/nuxit";

function copyDirectory($src, $dst) {
    if (!is_dir($src)) return;
    if (!file_exists($dst)) { @mkdir($dst, 0777, true); }
    $dir = opendir($src);
    if ($dir === false) return;
    while (($file = readdir($dir)) !== false) {
        if ($file !== '.' && $file !== '..') {
            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . $file;
            if (is_dir($srcPath)) {
                copyDirectory($srcPath, $dstPath);
            } else {
                @copy($srcPath, $dstPath);
            }
        }
    }
    closedir($dir);
}

// 1. Nettoyage complet du dossier cible Nuxit
if (file_exists($destDir)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($destDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iterator as $path) {
        if ($path->isDir()) {
            @rmdir($path->getRealPath());
        } else {
            @unlink($path->getRealPath());
        }
    }
} else {
    @mkdir($destDir, 0777, true);
}

// 2. Copie des images indispensables
if (is_dir($sourceDir . '/images')) {
    copyDirectory($sourceDir . '/images', $destDir . '/images');
}
if (is_dir($sourceDir . '/dashboard-designer/assets/img')) {
    copyDirectory($sourceDir . '/dashboard-designer/assets/img', $destDir . '/images');
}

// 3. Génération de l'index principal (Le Journal) à la racine de Nuxit
define('FIREBASE_STATIC', true);
ob_start();
include $sourceDir . "/index.php";
$htmlContent = ob_get_clean();

$htmlContent = str_replace('dashboard-designer/assets/img/', 'images/', $htmlContent);
$htmlContent = str_replace('/skeletor-v1.0/', 'skeletor-v1.0/', $htmlContent);
$htmlContent = str_replace('http://localhost/_www/skeletor-v1.0/', 'skeletor-v1.0/', $htmlContent);

file_put_contents($destDir . "/index.html", $htmlContent);

// 4. Copie complète de Skeletor
$skeletorDest = $destDir . "/skeletor-v1.0";
if (is_dir($sourceDir . '/skeletor-v1.0')) {
    copyDirectory($sourceDir . '/skeletor-v1.0', $skeletorDest);
}

$redirectHtml = '<meta http-equiv="refresh" content="0;url=generator.php">';
file_put_contents($skeletorDest . "/index.html", $redirectHtml);

$journalUrl = "http://localhost/_www/export/nuxit/index.html";
$skeletorUrl = "http://localhost/_www/export/nuxit/skeletor-v1.0/generator.php";

echo "<h3>Export Nuxit mis à jour avec succès !</h3>";
echo "<p><a href=\"" . $journalUrl . "\" target=\"_blank\" style=\"font-size: 1.1rem;\">👉 Ouvrir le Journal Nuxit (Accueil)</a></p>";
echo "<p><a href=\"" . $skeletorUrl . "\" target=\"_blank\" style=\"font-size: 1.1rem;\">👉 Ouvrir Skeletor Nuxit</a></p>";
?>