<?php
set_time_limit(0);

$sourceDir = __DIR__;
$destDir = __DIR__ . '\\export\\o2switch';

$excludedFiles = ['admin.php', 'generator.php', 'generator copy.php', 'export-nuxit.php', 'export-o2switch.php', 'export-firebase.php', 'verification.bat', 'lancer-export.bat'];
$excludedDirs = [
    'core', 'server', 'sql', 'Data', 'export', '_firebase_build', 'firebase-builder', 
    '.git', 'trash temp', '000-ARCHIVE-FIN-DE-FORMATION'
];

function recursiveCopy($src, $dst, $excludedFiles, $excludedDirs) {
    if (!is_dir($src)) return;
    if (!file_exists($dst)) { @mkdir($dst, 0777, true); }
    $dir = opendir($src);
    if ($dir === false) return;
    while(($file = readdir($dir)) !== false) {
        if(($file != '.') && ($file != '..')) {
            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . $file;
            if (is_dir($srcPath)) {
                if (!in_array($file, $excludedDirs)) {
                    recursiveCopy($srcPath, $dstPath, $excludedFiles, $excludedDirs);
                }
            } else {
                if (!in_array($file, $excludedFiles)) { @copy($srcPath, $dstPath); }
            }
        }
    }
    closedir($dir);
}

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
}

recursiveCopy($sourceDir, $destDir, $excludedFiles, $excludedDirs);
echo "Export o2switch réussi avec le journal principal dans : " . $destDir;
?>