<?php
// Script d'exportation par Aspiration (SSG) — Correction des chemins d'images et autonomie totale
$baseDir = __DIR__; 
$targetDir = $baseDir . '/dossier-final-export-o2switch';
$imagesTargetDir = $targetDir . '/images';

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}
if (!is_dir($imagesTargetDir)) {
    mkdir($imagesTargetDir, 0777, true);
}

function recursiveCopy($source, $destination) {
    if (!is_dir($destination)) {
        mkdir($destination, 0777, true);
    }
    $dir = opendir($source);
    while (($file = readdir($dir)) !== false) {
        if ($file != '.' && $file != '..') {
            $srcPath = $source . '/' . $file;
            $destPath = $destination . '/' . $file;
            if (is_dir($srcPath)) {
                recursiveCopy($srcPath, $destPath);
            } else {
                copy($srcPath, $destPath);
            }
        }
    }
    closedir($dir);
}

$rootDir = dirname(dirname(__DIR__)); // Remonte à _www/

// Copie de tout le dossier images global
$sourceImagesDir = $rootDir . '/images';
if (is_dir($sourceImagesDir)) {
    recursiveCopy($sourceImagesDir, $imagesTargetDir);
}

// Copie des assets du Dashboard
$sourceDashboardImgDir = $rootDir . '/dashboard-designer/assets/img';
$targetDashboardImgDir = $imagesTargetDir . '/dashboard';
if (is_dir($sourceDashboardImgDir)) {
    recursiveCopy($sourceDashboardImgDir, $targetDashboardImgDir);
}

// Aspiration du HTML rendu par le moteur local avec le mode export
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$scriptPath = dirname(dirname($_SERVER['SCRIPT_NAME']));
$localUrl = "{$protocol}://{$host}{$scriptPath}/../index.php?mode=export";

$context = stream_context_create([
    "http" => [
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
    ]
]);

$perfectHtml = @file_get_contents($localUrl, false, $context);

if ($perfectHtml === false) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Impossible d aspirer le rendu local via URL.']);
    exit;
}

// Correction automatique des chemins d'images pour l'archive autonome
$perfectHtml = str_replace('dashboard-designer/assets/img/', 'images/dashboard/', $perfectHtml);
$perfectHtml = str_replace('/dashboard-designer/assets/img/', 'images/dashboard/', $perfectHtml);

// Sauvegarde du fichier HTML final dans l'archive
file_put_contents($targetDir . '/index.php', $perfectHtml);

header('Content-Type: application/json');
echo json_encode(['success' => true]);
exit;