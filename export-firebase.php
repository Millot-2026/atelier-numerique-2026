<?php
@ini_set('output_buffering', 'off');
@ini_set('implicit_flush', true);
@ob_end_flush();
set_time_limit(0);

$sourceDir = __DIR__;
$destDir = __DIR__ . "/export/firebase";
$isDeploying = isset($_GET['deploy']) && $_GET['deploy'] === '1';

$excludedFiles = ['admin.php', 'generator.php', 'generator copy.php', 'export-nuxit.php', 'export-o2switch.php', 'export-firebase.php', 'verification.bat', 'lancer-export.bat'];
$excludedDirs = [
    'core', 'server', 'sql', 'Data', 'export', '_firebase_build', 'firebase-builder', 
    '.git', 'trash temp', '000-ARCHIVE-FIN-DE-FORMATION', 'wordpress-portable'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Firebase en direct — L'Atelier Numérique</title>
    <style>
        :root { --bg-color: #0f172a; --card-bg: #1e293b; --text-main: #f8fafc; --text-muted: #94a3b8; --accent: #38bdf8; --border: #334155; }
        body { font-family: system-ui, sans-serif; background-color: var(--bg-color); color: var(--text-main); margin: 0; padding: 40px; display: flex; justify-content: center; align-items: center; min-height: 100vh; box-sizing: border-box; }
        .export-card { background-color: var(--card-bg); border: 1px solid var(--border); border-radius: 8px; padding: 30px; width: 100%; max-width: 650px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
        h1 { font-size: 1.4rem; margin-top: 0; color: var(--accent); border-bottom: 1px solid var(--border); padding-bottom: 15px; display: flex; align-items: center; gap: 12px; }
        h1 img { width: 28px; height: 28px; object-fit: contain; }
        .log-list { list-style: none; padding: 0; margin: 20px 0; font-size: 0.95rem; color: var(--text-muted); }
        .log-list li { margin-bottom: 10px; display: flex; gap: 8px; }
        .timer-box { background: #0f172a; border: 1px solid var(--border); border-radius: 6px; padding: 12px; margin-top: 15px; display: flex; justify-content: space-between; font-family: monospace; color: #f59e0b; }
        
        .progress-bar-container { background: linear-gradient(to right, #ef4444, #f59e0b, #22c55e); border: 1px solid var(--border); border-radius: 6px; height: 16px; margin-top: 8px; overflow: hidden; position: relative; }
        .progress-stripes { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: linear-gradient(45deg, rgba(255,255,255,.25) 25%, transparent 25%, transparent 50%, rgba(255,255,255,.25) 50%, rgba(255,255,255,.25) 75%, transparent 75%, transparent); background-size: 40px 40px; animation: move-stripes 1s linear infinite; }
        @keyframes move-stripes { from { background-position: 0 0; } to { background-position: 40px 0; } }
        .progress-mask { position: absolute; top: 0; right: 0; height: 100%; width: 100%; background-color: #0f172a; transition: width 0.3s linear; z-index: 2; }

        .actions-grid { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 8px; margin-top: 25px; }
        .btn { display: flex; align-items: center; justify-content: center; padding: 10px; border-radius: 6px; font-weight: 700; text-transform: uppercase; text-decoration: none; border: none; cursor: pointer; font-size: 0.70rem; letter-spacing: 0.5px; text-align: center; }
        .btn:hover { filter: brightness(1.1); transform: translateY(-1px); }
        .btn-primary { background-color: #334155; color: white; }
        .btn-deploy { background-color: #f59e0b; color: #0f172a; }
        .btn-local { background-color: #38bdf8; color: #0f172a; }
        .btn-success { background-color: #22c55e; color: #0f172a; }
    </style>
</head>
<body>
    <div class="export-card">
        <h1 id="export-title"><img src="images/micro-logo-firebase.svg" alt="Firebase"> Export Firebase en cours...</h1>
        <ul class="log-list" id="log-list"><li>⏳ Initialisation...</li></ul>
        <div class="timer-box">
            <span id="timer-label">⏳ Temps restant estimé :</span>
            <span id="deploy-timer" style="font-size: 1.2rem; font-weight: bold;">00:06</span>
        </div>
        <div class="progress-bar-container" id="progress-container">
            <div class="progress-stripes" id="progress-stripes"></div>
            <div id="progress-mask" class="progress-mask"></div>
        </div>
        <div id="actions-container" style="display: none;" class="actions-grid">
            <a href="export/firebase/index.html" target="_blank" class="btn btn-primary">Journal</a>
            <a href="export/firebase/index.html" target="_blank" class="btn btn-local">🖥️ Site Local</a>
            <a href="?deploy=1" class="btn btn-deploy">Déployer</a>
            <a href="https://la-centrale-36780.web.app" target="_blank" class="btn btn-success">Site Web</a>
        </div>
    </div>
    <script>
        let elapsed = 0;
        const total = 6; let remaining = total;
        const mask = document.getElementById('progress-mask');
        const timer = document.getElementById('deploy-timer');
        const stripes = document.getElementById('progress-stripes');
        const container = document.getElementById('progress-container');

        const interval = setInterval(() => {
            elapsed++;
            if (remaining > 0) remaining--;
            timer.textContent = String(Math.floor(remaining / 60)).padStart(2, '0') + ':' + String(remaining % 60).padStart(2, '0');
            mask.style.width = (remaining / total) * 100 + '%';
            
            if (remaining === 0) {
                clearInterval(interval);
                mask.style.width = '0%';
                stripes.style.display = 'none';
                container.style.background = '#22c55e';
                timer.textContent = '00:00';
                document.getElementById('timer-label').textContent = '✅ Opération terminée !';
                document.getElementById('export-title').innerHTML = '<img src="images/micro-logo-firebase.svg" alt="Firebase"> Export Firebase Réussi';
                document.getElementById('actions-container').style.display = 'grid';
            }
        }, 1000);

        function appendLog(icon, text) { 
            const li = document.createElement('li'); 
            li.innerHTML = `<span>${icon}</span><div>${text}</div>`; 
            document.getElementById('log-list').appendChild(li); 
        }
    </script>
</body>
</html>
<?php
flush();

function recursiveCopyFiltered($src, $dst, $excludedFiles, $excludedDirs) {
    if (!is_dir($src)) return;
    if (!file_exists($dst)) { @mkdir($dst, 0777, true); }
    $dir = opendir($src);
    if ($dir === false) return;
    while (($file = readdir($dir)) !== false) {
        if ($file !== '.' && $file !== '..') {
            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . $file;
            if (is_dir($srcPath)) {
                if (!in_array($file, $excludedDirs)) {
                    recursiveCopyFiltered($srcPath, $dstPath, $excludedFiles, $excludedDirs);
                }
            } else {
                if (!in_array($file, $excludedFiles)) {
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $allowed = ['html', 'css', 'js', 'png', 'jpg', 'jpeg', 'json', 'txt', 'scss', 'sass', 'ico', 'svg', 'jfif', 'webp'];
                    if ($ext !== 'php' && in_array($ext, $allowed)) {
                        @copy($srcPath, $dstPath);
                    }
                }
            }
        }
    }
    closedir($dir);
}

function deleteDirectory($dir) {
    if (!file_exists($dir)) return;
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            deleteDirectory($path);
        } else {
            @chmod($path, 0777);
            @unlink($path);
        }
    }
    @rmdir($dir);
}

if (file_exists($destDir)) {
    deleteDirectory($destDir);
}
echo "<script>appendLog('✅', 'Dossier cible nettoyé.');</script>";
flush();

recursiveCopyFiltered($sourceDir, $destDir, $excludedFiles, $excludedDirs);
echo "<script>appendLog('✅', 'Projets et ressources copiés avec filtrage.');</script>";
flush();

ob_start(); 
include $sourceDir . "/index.php"; 
$htmlContent = ob_get_clean();
file_put_contents($destDir . "/index.html", $htmlContent);
echo "<script>appendLog('✅', 'Page principale générée.');</script>";
flush();

// ─── Génération des pages de détail en index.html pour chaque sous-projet ───
if (!defined('FIREBASE_STATIC')) define('FIREBASE_STATIC', true);

$detailProjects = [
    'skeletor-v1.0', 'modulor', 'palettor', 'texturor',
    'personator-v1.2', 'user_journey-v1.0', 'pixelart',
    'cms-2026-v8-full', 'workstation', 'la-centrale'
];

$detailCount = 0;
foreach ($detailProjects as $proj) {
    $detailSrc = $sourceDir . '/' . $proj . '/detail.php';
    $detailDst = $destDir   . '/' . $proj . '/index.html';

    if (!file_exists($detailSrc)) continue;

    $dstSubDir = dirname($detailDst);
    if (!file_exists($dstSubDir)) @mkdir($dstSubDir, 0777, true);

    ob_start();
    include $detailSrc;
    $detailHtml = ob_get_clean();

    // Correction des chemins pour les sous-dossiers (remontée d'un niveau pour charger les assets et images)
    $detailHtml = str_replace(
        ['../index.php', 'detail.php', 'detail.html', 'images/', 'dashboard-designer/assets/img/'],
        ['../index.html', 'index.html', 'index.html', '../images/', '../dashboard-designer/assets/img/'],
        $detailHtml
    );

    file_put_contents($detailDst, $detailHtml);
    $detailCount++;
}
echo "<script>appendLog('✅', '$detailCount pages de détail générées en index.html.');</script>";
flush();

if ($isDeploying) {
    $cmd = 'cd /d "' . $sourceDir . '" && firebase deploy --non-interactive 2>&1';
    $deployOutput = shell_exec($cmd);
}
?>