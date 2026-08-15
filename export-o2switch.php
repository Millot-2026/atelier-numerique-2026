<?php
@ini_set('output_buffering', 'off');
@ini_set('implicit_flush', true);
@ob_end_flush();
set_time_limit(0);

$sourceDir = __DIR__;
$destDir = __DIR__ . '/export/o2switch';

$excludedFiles = ['admin.php', 'generator.php', 'generator copy.php', 'export-nuxit.php', 'export-o2switch.php', 'export-firebase.php', 'verification.bat', 'lancer-export.bat'];
$excludedDirs = [
    'core', 'server', 'sql', 'Data', 'export', '_firebase_build', 'firebase-builder', 
    '.git', 'trash temp', '000-ARCHIVE-FIN-DE-FORMATION'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export o2switch en direct — L'Atelier Numérique</title>
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #c084fc;
            --border: #334155;
        }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }
        .export-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 30px;
            width: 100%;
            max-width: 650px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        h1 {
            font-size: 1.4rem;
            margin-top: 0;
            color: var(--accent);
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 15px;
        }
        h1 img { width: 28px; height: 28px; object-fit: contain; }
        .log-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
        }
        .log-list li {
            margin-bottom: 10px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .timer-box {
            background: #0f172a;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 12px;
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: monospace;
            font-size: 1rem;
            color: #f59e0b;
        }
        
        .progress-bar-container {
            background: linear-gradient(to right, #ef4444, #f59e0b, #22c55e);
            border: 1px solid var(--border);
            border-radius: 6px;
            height: 16px;
            margin-top: 8px;
            overflow: hidden;
            position: relative;
        }
        
        .progress-stripes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: linear-gradient(
                45deg, 
                rgba(255,255,255,.25) 25%, 
                transparent 25%, 
                transparent 50%, 
                rgba(255,255,255,.25) 50%, 
                rgba(255,255,255,.25) 75%, 
                transparent 75%, 
                transparent
            );
            background-size: 40px 40px;
            animation: move-stripes 1s linear infinite;
        }
        @keyframes move-stripes { from { background-position: 0 0; } to { background-position: 40px 0; } }

        .progress-mask {
            position: absolute;
            top: 0;
            right: 0;
            height: 100%;
            width: 100%;
            background-color: #0f172a;
            transition: width 0.3s linear;
            z-index: 2;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 25px;
        }
        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            transition: filter 0.2s, transform 0.1s;
            cursor: pointer;
            border: none;
        }
        .btn:hover { filter: brightness(1.1); transform: translateY(-1px); }
        .btn-primary { background-color: #334155; color: var(--text-main); }
        .btn-success { background-color: #8e44ad; color: #ffffff; }
    </style>
</head>
<body>

    <div class="export-card">
        <h1 id="export-title"><img src="images/micro-logo-o2switch.svg" alt="o2switch"> Export o2switch en cours...</h1>
        
        <ul class="log-list" id="log-list">
            <li><span>⏳</span> <div>Initialisation et nettoyage du dossier cible...</div></li>
        </ul>

        <div id="deploy-timer-container" class="timer-box">
            <span id="timer-label">⏳ Temps restant estimé :</span>
            <span id="deploy-timer" style="font-size: 1.2rem; font-weight: bold;">03:56</span>
        </div>

        <div class="progress-bar-container" id="progress-container">
            <div class="progress-stripes" id="progress-stripes"></div>
            <div id="progress-mask" class="progress-mask"></div>
        </div>

        <div id="actions-container" style="display: none;" class="actions-grid">
            <a href="export/o2switch/index.php" target="_blank" class="btn btn-primary">← Ouvrir le Journal</a>
            <a href="https://sc1mich8332.universe.wf/" target="_blank" class="btn btn-success">🌐 Accès Direct Site</a>
        </div>
    </div>

    <script>
        let countdownInterval;
        const totalEstimatedSeconds = 236; 
        let secondsRemaining = totalEstimatedSeconds;

        function startCountdown() {
            const timerDisplay = document.getElementById('deploy-timer');
            const progressMask = document.getElementById('progress-mask');
            const stripes = document.getElementById('progress-stripes');
            const container = document.getElementById('progress-container');
            
            countdownInterval = setInterval(() => {
                if (secondsRemaining > 0) secondsRemaining--;
                
                let m = Math.floor(secondsRemaining / 60);
                let s = secondsRemaining % 60;
                timerDisplay.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                
                let percent = (secondsRemaining / totalEstimatedSeconds) * 100;
                progressMask.style.width = percent + '%';

                if (secondsRemaining === 0) {
                    clearInterval(countdownInterval);
                    progressMask.style.width = '0%';
                    stripes.style.display = 'none';
                    container.style.background = '#22c55e';
                    timerDisplay.textContent = '00:00';
                    document.getElementById('timer-label').textContent = '✅ Opération terminée !';
                    document.getElementById('export-title').innerHTML = '<img src="images/micro-logo-o2switch.svg" alt="o2switch"> Export o2switch Réussi';
                    document.getElementById('actions-container').style.display = 'grid';
                }
            }, 1000);
        }

        startCountdown();

        function appendLog(icon, htmlText) {
            const list = document.getElementById('log-list');
            const li = document.createElement('li');
            li.innerHTML = `<span>${icon}</span><div>${htmlText}</div>`;
            list.appendChild(li);
        }

        function finishExport() {
            clearInterval(countdownInterval);
            const mask = document.getElementById('progress-mask');
            const stripes = document.getElementById('progress-stripes');
            const container = document.getElementById('progress-container');
            
            mask.style.width = '0%';
            stripes.style.display = 'none';
            container.style.background = '#22c55e';
            document.getElementById('deploy-timer').textContent = '00:00';
            document.getElementById('timer-label').textContent = '✅ Opération terminée !';
            document.getElementById('export-title').innerHTML = '<img src="images/micro-logo-o2switch.svg" alt="o2switch"> Export o2switch Réussi';
            document.getElementById('actions-container').style.display = 'grid';
        }
    </script>
</body>
</html>
<?php
flush();

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
echo "<script>appendLog('✅', 'Ancien dossier cible nettoyé.');</script>";
flush();

recursiveCopy($sourceDir, $destDir, $excludedFiles, $excludedDirs);
echo "<script>appendLog('✅', '<strong>Destination :</strong> Journal principal exporté dans <code>" . htmlspecialchars($destDir, ENT_QUOTES, 'UTF-8') . "</code>.');</script>";
flush();

echo "<script>finishExport();</script>";
flush();
?>