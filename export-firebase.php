<?php
set_time_limit(0);

$sourceDir = __DIR__;
$destDir = __DIR__ . "/export/firebase";

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

// 1. Nettoyage complet du dossier cible Firebase
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

// 3. Génération de l'index principal (Le Journal) à la racine de Firebase
define('FIREBASE_STATIC', true);
ob_start();
include $sourceDir . "/index.php";
$htmlContent = ob_get_clean();

// Adaptation des chemins et des liens
$htmlContent = str_replace('dashboard-designer/assets/img/', 'images/', $htmlContent);
$htmlContent = str_replace('/skeletor-v1.0/', 'skeletor-v1.0/', $htmlContent);
$htmlContent = str_replace('http://localhost/_www/skeletor-v1.0/', 'skeletor-v1.0/', $htmlContent);

// NETTOYAGE CHIRURGICAL PAR CHAÎNE (PRÉSERVE 100% LE DOM ET LE MENU BURGER)
function cleanMenuSection($html, $titleText, $newLiContent) {
    $pos = strpos($html, $titleText);
    if ($pos !== false) {
        $ulPos = strpos($html, '<ul', $pos);
        if ($ulPos !== false) {
            $ulEndPos = strpos($html, '</ul>', $ulPos);
            if ($ulEndPos !== false) {
                $length = ($ulEndPos + 5) - $ulPos;
                $replacement = '<ul>' . $newLiContent . '</ul>';
                $html = substr_replace($html, $replacement, $ulPos, $length);
            }
        }
    }
    return $html;
}

$htmlContent = cleanMenuSection($htmlContent, 'Navigation', '<li><a href="#">Titre 1</a></li>');
$htmlContent = cleanMenuSection($htmlContent, 'Applications', '<li><a href="skeletor-v1.0/generator.php">skeletor-v1.0</a></li>');
$htmlContent = cleanMenuSection($htmlContent, 'Outils', '<li><a href="#">À propos</a></li>');

file_put_contents($destDir . "/index.html", $htmlContent);

// 4. Copie complète de Skeletor
$skeletorDest = $destDir . "/skeletor-v1.0";
if (is_dir($sourceDir . '/skeletor-v1.0')) {
    copyDirectory($sourceDir . '/skeletor-v1.0', $skeletorDest);
}

// 5. Création d'un index.html de redirection dans le dossier skeletor-v1.0
$redirectHtml = '<meta http-equiv="refresh" content="0;url=generator.php">';
file_put_contents($skeletorDest . "/index.html", $redirectHtml);

// 6. Ajustement du titre de Skeletor en production ("Skeletor v1.0")
$exportedGenerator = $skeletorDest . "/generator.php";
if (file_exists($exportedGenerator)) {
    $genContent = file_get_contents($exportedGenerator);
    $genContent = preg_replace('/<title>.*?<\/title>/i', '<title>Skeletor v1.0</title>', $genContent);
    file_put_contents($exportedGenerator, $genContent);
}

$journalUrl = "http://localhost/_www/export/firebase/index.html";
$skeletorUrl = "http://localhost/_www/export/firebase/skeletor-v1.0/generator.php";

echo "<h3>Export Firebase mis à jour avec succès !</h3>";
echo "<p><a href=\"" . $journalUrl . "\" target=\"_blank\" style=\"font-size: 1.1rem;\">👉 Ouvrir le Journal (Accueil)</a></p>";
echo "<p><a href=\"" . $skeletorUrl . "\" target=\"_blank\" style=\"font-size: 1.1rem;\">👉 Ouvrir Skeletor pour le vérifier</a></p>";
?>