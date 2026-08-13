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

// 5. Génération des pages de détail statiques pour chaque projet
// Chaque {slug}/detail.php est capturé via ob_start() et écrit en {slug}/detail.html dans le dossier Nuxit
$detailProjects = [
    'workstation',
    'la-centrale',
    'cms-2026-v8-full',
    'palettor',
    'modulor',
    'skeletor-v1.0',
    'personator-v1.2',
    'texturor',
    'user_journey-v1.0',
    'wordpress-portable',
    'pixelart',
];

$detailsGenerated = [];
$detailsFailed    = [];

foreach ($detailProjects as $dSlug) {
    $detailSrc = $sourceDir . '/' . $dSlug . '/detail.php';
    if (!file_exists($detailSrc)) {
        $detailsFailed[] = $dSlug;
        continue;
    }

    // Crée le dossier de destination si nécessaire
    $detailDestDir = $destDir . '/' . $dSlug;
    if (!file_exists($detailDestDir)) {
        @mkdir($detailDestDir, 0777, true);
    }

    // Capture de la sortie PHP en mode statique
    ob_start();
    // La constante FIREBASE_STATIC est déjà définie (étape 3)
    include $detailSrc;
    $detailHtml = ob_get_clean();

    // Adaptation des chemins d'images (dashboard-designer → images/ de l'export)
    $detailHtml = str_replace('dashboard-designer/assets/img/', '../images/', $detailHtml);
    $detailHtml = str_replace('src="images/', 'src="../images/', $detailHtml);

    file_put_contents($detailDestDir . '/detail.html', $detailHtml);
    $detailsGenerated[] = $dSlug;
}

$journalUrl  = "http://localhost/_www/export/nuxit/index.html";
$skeletorUrl = "http://localhost/_www/export/nuxit/skeletor-v1.0/generator.php";

echo "<h3>Export Nuxit mis à jour avec succès !</h3>";
echo "<p><a href=\"" . $journalUrl . "\" target=\"_blank\" style=\"font-size: 1.1rem;\">👉 Ouvrir le Journal Nuxit (Accueil)</a></p>";
echo "<p><a href=\"" . $skeletorUrl . "\" target=\"_blank\" style=\"font-size: 1.1rem;\">👉 Ouvrir Skeletor Nuxit</a></p>";
if (!empty($detailsGenerated)) {
    echo "<p>✅ Pages de détail générées (" . count($detailsGenerated) . ") : <code>" . implode('</code>, <code>', $detailsGenerated) . "</code></p>";
}
if (!empty($detailsFailed)) {
    echo "<p>⚠️ Pages de détail manquantes : <code>" . implode('</code>, <code>', $detailsFailed) . "</code></p>";
}
?>