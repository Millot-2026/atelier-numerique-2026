<?php
set_time_limit(0);

$sourceDir = __DIR__;
$destDir = __DIR__ . "/export/firebase";

function copyDirectory($src, $dst) {
    if (!is_dir($src)) return;
    if (!file_exists($dst)) { @mkdir($dst, 0777, true); }
    $dir = opendir($src);
    if ($dir === false) return;
    
    // Ignore les dossiers système/git et de configuration interne
    $ignore = ['.git', 'trash temp', 'export-nuxit'];

    while (($file = readdir($dir)) !== false) {
        if ($file !== '.' && $file !== '..' && !in_array($file, $ignore)) {
            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . $file;
            if (is_dir($srcPath)) {
                copyDirectory($srcPath, $dstPath);
            } else {
                // EXCLUSION ABSOLUE : Ni fichiers .php, ni fichiers système/exécutables interdits par Firebase Spark
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $allowed = ['html', 'css', 'js', 'png', 'jpg', 'jpeg', 'json', 'txt', 'scss', 'sass', 'ico', 'svg'];
                if ($ext !== 'php' && in_array($ext, $allowed)) {
                    @copy($srcPath, $dstPath);
                }
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
$htmlContent = cleanMenuSection($htmlContent, 'Applications', '<li><a href="skeletor-v1.0/generator.html">skeletor-v1.0</a></li>');
$htmlContent = cleanMenuSection($htmlContent, 'Outils', '<li><a href="#">À propos</a></li>');

file_put_contents($destDir . "/index.html", $htmlContent);

// 4. Copie complète de Skeletor (en ignorant les fichiers .php et .git)
$skeletorDest = $destDir . "/skeletor-v1.0";
if (is_dir($sourceDir . '/skeletor-v1.0')) {
    copyDirectory($sourceDir . '/skeletor-v1.0', $skeletorDest);
}

// 5. Création d'un index.html de redirection dans le dossier skeletor-v1.0
$redirectHtml = '<meta http-equiv="refresh" content="0;url=generator.html">';
file_put_contents($skeletorDest . "/index.html", $redirectHtml);

// 6. Gestion propre de Skeletor generator en .html pour Firebase (conversion du fichier exporté si présent)
$exportedGeneratorPhp = $skeletorDest . "/generator.php";
$exportedGeneratorHtml = $skeletorDest . "/generator.html";
if (file_exists($exportedGeneratorPhp)) {
    $genContent = file_get_contents($exportedGeneratorPhp);
    $genContent = preg_replace('/<title>.*?<\/title>/i', '<title>Skeletor v1.0</title>', $genContent);
    file_put_contents($exportedGeneratorHtml, $genContent);
    @unlink($exportedGeneratorPhp);
}

// 7. Copie automatique de firebase.json (racine → export/firebase/)
$firebaseJsonSrc = $sourceDir . '/firebase.json';
$firebaseJsonDst = $destDir . '/firebase.json';
$firebaseJsonCopied = false;
if (file_exists($firebaseJsonSrc)) {
    $firebaseJsonCopied = copy($firebaseJsonSrc, $firebaseJsonDst);
}

// 8. Génération des pages de détail statiques pour chaque projet
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

    $detailDestDir = $destDir . '/' . $dSlug;
    if (!file_exists($detailDestDir)) {
        @mkdir($detailDestDir, 0777, true);
    }

    ob_start();
    include $detailSrc;
    $detailHtml = ob_get_clean();

    $detailHtml = str_replace('dashboard-designer/assets/img/', '../images/', $detailHtml);
    $detailHtml = str_replace('src="images/', 'src="../images/', $detailHtml);

    file_put_contents($detailDestDir . '/detail.html', $detailHtml);
    $detailsGenerated[] = $dSlug;
}

$journalUrl  = "http://localhost/_www/export/firebase/index.html";
$skeletorUrl = "http://localhost/_www/export/firebase/skeletor-v1.0/generator.html";

echo "<h3>Export Firebase mis à jour avec succès !</h3>";
echo "<p><a href=\"" . $journalUrl . "\" target=\"_blank\" style=\"font-size: 1.1rem;\">👉 Ouvrir le Journal (Accueil)</a></p>";
echo "<p><a href=\"" . $skeletorUrl . "\" target=\"_blank\" style=\"font-size: 1.1rem;\">👉 Ouvrir Skeletor pour le vérifier</a></p>";
if ($firebaseJsonCopied) {
    echo "<p>✅ <code>firebase.json</code> copié automatiquement dans <code>export/firebase/</code>.</p>";
} else {
    echo "<p>⚠️ <code>firebase.json</code> introuvable à la racine du projet.</p>";
}
if (!empty($detailsGenerated)) {
    echo "<p>✅ Pages de détail générées (" . count($detailsGenerated) . ") : <code>" . implode('</code>, <code>', $detailsGenerated) . "</code></p>";
}
if (!empty($detailsFailed)) {
    echo "<p>⚠️ Pages de détail manquantes : <code>" . implode('</code>, <code>', $detailsFailed) . "</code></p>";
}
?>