<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

require_once 'core/skeletor.php';

$backupDir = 'core/backups/';
if (!is_dir($backupDir)) { mkdir($backupDir, 0777, true); }

$loadedData = 'null';
if (isset($_GET['load']) && !empty($_GET['load'])) {
    $fileToLoad = $backupDir . basename($_GET['load']);
    if (file_exists($fileToLoad) && !is_dir($fileToLoad)) { 
        $loadedData = file_get_contents($fileToLoad); 
    }
}

$backups = array_diff(scandir($backupDir), ['.', '..']);

$projectName = !empty($_POST['config_name']) ? basename($_POST['config_name']) : "MonNouveauProjet";
$app = new Skeletor($projectName);
$statusMessage = "";


// Fonction utilitaire : copie récursive d'un dossier
function recursive_copy($src, $dst) {
    if (!is_dir($src)) return;
    if (!is_dir($dst)) mkdir($dst, 0777, true);
    $dir = opendir($src);
    while (($file = readdir($dir)) !== false) {
        if ($file === '.' || $file === '..') continue;
        $s = $src . '/' . $file;
        $d = $dst . '/' . $file;
        is_dir($s) ? recursive_copy($s, $d) : copy($s, $d);
    }
    closedir($dir);
}

// Génération de l'index.html statique autonome du parcours
function buildJourneyHtml($projectName, $data) {
    $rows = '';
    if (!empty($data['time'])) {
        foreach ($data['time'] as $i => $time) {
            $timeEnd  = htmlspecialchars($data['time_end'][$i] ?? '', ENT_QUOTES, 'UTF-8');
            $context  = htmlspecialchars($data['context'][$i] ?? '', ENT_QUOTES, 'UTF-8');
            $action   = htmlspecialchars($data['title'][$i] ?? '', ENT_QUOTES, 'UTF-8');
            $timeHtml = htmlspecialchars($time, ENT_QUOTES, 'UTF-8');
            $range    = $timeEnd ? "$timeHtml &rarr; $timeEnd" : $timeHtml;
            $rows .= "
            <div class='step'>
                <div class='step-time'>$range</div>
                <div class='step-body'>
                    <strong class='step-context'>$context</strong>
                    <p class='step-action'>$action</p>
                </div>
            </div>";
        }
    }
    $name = htmlspecialchars($projectName, ENT_QUOTES, 'UTF-8');
    return "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Parcours : $name</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; color: #2c3e50; margin: 0; padding: 20px; width: 100%; }
        h1 { text-align: center; color: #e67e22; font-size: 1.6rem; margin-bottom: 30px; }
        .journey { width: 100%; max-width: 100%; margin: 0 auto; display: flex; flex-direction: column; gap: 12px; }
        .step { display: flex; gap: 16px; background: white; border-radius: 10px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 5px solid #e67e22; width: 100%; }
        .step-time { flex-shrink: 0; font-weight: bold; font-size: 0.85rem; color: #e67e22; min-width: 110px; padding-top: 2px; }
        .step-body { flex: 1; }
        .step-context { display: block; font-size: 0.9rem; color: #7f8c8d; margin-bottom: 4px; }
        .step-action { margin: 0; font-size: 1rem; color: #2c3e50; }
        @media print { body { background: white; } .step { box-shadow: none; border: 1px solid #ddd; } }
    </style>
</head>
<body>
    <h1>Parcours : $name</h1>
    <div class='journey'>$rows</div>
</body>
</html>";
}

if (isset($_POST['save_config'])) {
    $name = !empty($_POST['config_name']) ? basename($_POST['config_name']) : 'sans-nom-' . date('Y-m-d_H-i');
    file_put_contents($backupDir . $name . '.json', json_encode($_POST));
    $statusMessage = "Configuration '$name' sauvegardée !";
}

if (isset($_POST['generate'])) {
    $finalName = !empty($_POST['config_name'])
        ? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['config_name'])))
        : 'mon-parcours';

    $jsonPath = $backupDir . $finalName . '.json';
    file_put_contents($jsonPath, json_encode($_POST));

    $localExportDir = __DIR__ . '/export/' . $finalName . '/';
    if (!is_dir($localExportDir)) mkdir($localExportDir, 0777, true);
    $meta = json_encode(['source' => 'user-journey', 'generated_at' => date('c')], JSON_PRETTY_PRINT);
    file_put_contents($localExportDir . '_meta.json', $meta);
    copy($jsonPath, $localExportDir . $finalName . '.json');
    $htmlContent = buildJourneyHtml($finalName, $_POST);
    file_put_contents($localExportDir . 'index.html', $htmlContent);

    $globalExportDir = dirname(__DIR__) . '/export/';
    if (!is_dir($globalExportDir)) mkdir($globalExportDir, 0777, true);
    recursive_copy($localExportDir, $globalExportDir . $finalName . '/');

    $statusMessage = "✅ Parcours '$finalName' exporté (local + _www/export/) !";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Journey v1.0</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; background: #1a1a1a; color: #eee; padding: 20px; margin: 0; width: 100%; box-sizing: border-box; }
        .container { width: 100%; max-width: 100%; margin: 0; background: #222; padding: 20px; border: 1px solid #333; border-radius: 8px; box-sizing: border-box; }
        h1 { font-size: 1.5rem; text-align: center; }
        
        .row { display: flex; flex-wrap: nowrap; align-items: center; margin-bottom: 10px; background: #2a2a2a; padding: 8px; border-radius: 4px; gap: 8px; width: 100%; }
        .drag-handle { cursor: grab; padding: 5px; color: #666; font-size: 20px; user-select: none; flex-shrink: 0; }
        
        .input-group { display: flex; gap: 8px; flex-grow: 1; min-width: 0; width: 100%; }
        
        select, input[type="text"] { 
            padding: 8px; background: #333; color: #fff; border: 1px solid #444; border-radius: 4px; 
            box-sizing: border-box; width: 100%; min-width: 0; flex-basis: 0; 
        }
        
        .level-select { flex: 1; }
        .title-input { flex: 2; }
        .parent-selector { flex: 1; }
        
        .btn-remove { 
            background: #c0392b; color: white; border: none; padding: 8px 12px; 
            cursor: pointer; font-weight: bold; border-radius: 4px; flex-shrink: 0; 
        }
        
        .btn-add { background: #333; border: 1px dashed #555; width: 100%; padding: 15px; cursor: pointer; color: #aaa; margin: 20px 0; border-radius: 4px; }
        .btn-submit { background: #e67e22; color: #fff; border: none; padding: 15px; width: 100%; cursor: pointer; font-weight: bold; border-radius: 4px; font-size: 1rem; }
        
        .status-bar { color: orange; font-weight: bold; margin-bottom: 15px; text-align: center; }
        .admin-link { display: inline-block; margin-bottom: 20px; color: orange; text-decoration: none; font-size: 0.9rem; border: 1px solid orange; padding: 5px 12px; border-radius: 3px; }
        .load-zone { margin-bottom: 20px; background: #333; padding: 15px; border-radius: 4px; width: 100%; }
        .load-form { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; width: 100%; }
        .save-zone { background: #2a2a2a; padding: 15px; margin-bottom: 20px; border: 1px solid #444; display: flex; flex-wrap: wrap; gap: 10px; border-radius: 4px; width: 100%; }
        .config-name-input { flex: 1 1 250px; padding: 10px; background: #111; color: orange; border: 1px solid #555; }
        .btn-save { background: #e67e22; color: white; border: none; padding: 10px 20px; cursor: pointer; font-weight: bold; border-radius: 4px; flex: 0 1 auto; }
        .header-main { display: flex; align-items: center; margin-bottom: 20px; position: relative; width: 100%; }
        .header-main h1 { flex: 1; text-align: center; margin: 0; transform: translateX(-35px); pointer-events: none; }
    </style>
</head>
<body>
<div class="container">
    <div class="header-main">
        <a href="./admin.php" class="admin-link">Admin</a>
        <h1>User Journey v1.0</h1>
    </div>
    <?php if($statusMessage): ?><div class="status-bar"><?php echo $statusMessage; ?></div><?php endif; ?>
    <div class="load-zone">
        <form method="GET" class="load-form">
            <label>Charger :</label>
            <select name="load" style="flex-grow: 1; min-width: 150px;">
                <option value="">-- Projet vierge --</option>
                <?php foreach ($backups as $file): ?>
                    <option value="<?php echo $file; ?>" <?php echo (isset($_GET['load']) && $_GET['load'] == $file) ? 'selected' : ''; ?>>
                        <?php echo str_replace('.json', '', $file); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" style="padding: 10px 20px; cursor:pointer;">OK</button>
            <a href="?" style="color: orange; text-decoration: none; font-size: 0.8rem; font-weight: bold; padding: 5px;">CLEAR</a>
        </form>
    </div>
    <form method="POST" id="main-form">
        <div id="inputs-container" style="width: 100%;"></div>
        <button type="button" class="btn-add" onclick="addRow()">+ Ajouter une étape</button>
        <div class="save-zone">
            <input type="text" name="config_name" placeholder="Nom du projet" class="config-name-input" value="<?php echo isset($_GET['load']) ? str_replace('.json', '', $_GET['load']) : ''; ?>">
            <button type="submit" name="save_config" class="btn-save">💾 SAUVEGARDER</button>
        </div>
        <div class="save-zone">
            <button type="submit" name="generate" class="btn-submit">FINALISER LE PARCOURS</button>
            <?php if (isset($_GET['load'])): ?><a href="presentation.php?project=<?php echo str_replace('.json', '', $_GET['load']); ?>" target="_blank" class="admin-link" style="margin-left:10px;">👀 Voir la présentation</a><?php endif; ?>
        </div>
    </form>
    <div id="journey-preview" style="margin-top: 30px; padding: 20px; background: #222; border: 2px solid #333; border-radius: 8px; width: 100%;">
        <h3 style="color: #e67e22; margin-top: 0;">👁️ Aperçu du parcours</h3>
        <div id="preview-list" style="display: flex; flex-direction: column; gap: 10px; width: 100%;"></div>
    </div>
</div>
<script>
let dragSrcEl = null;
function handleDragStart(e) { dragSrcEl = this; e.dataTransfer.effectAllowed = 'move'; }
function handleDragOver(e) { e.preventDefault(); }
function handleDrop(e) { e.stopPropagation(); if (dragSrcEl !== this) { const list = this.parentNode; const allNodes = Array.from(list.children); if (allNodes.indexOf(dragSrcEl) < allNodes.indexOf(this)) { list.insertBefore(dragSrcEl, this.nextSibling); } else { list.insertBefore(dragSrcEl, this); } updatePreview(); } }
function updatePreview() { const previewList = document.getElementById('preview-list'); previewList.innerHTML = ''; document.querySelectorAll('.row').forEach((row) => { const start = row.querySelector('[name="time[]"]').value || '...'; const end = row.querySelector('[name="time_end[]"]').value || '...'; const context = row.querySelector('[name="context[]"]').value || '...'; const action = row.querySelector('[name="title[]"]').value || '...'; const card = document.createElement('div'); card.style.background = '#2a2a2a'; card.style.padding = '10px'; card.style.borderRadius = '4px'; card.style.borderLeft = '4px solid #e67e22'; card.style.width = '100%'; card.innerHTML = `<strong>${start} - ${end}</strong> | <em>${context}</em> : ${action}`; previewList.appendChild(card); }); }
function addRow() { const container = document.getElementById('inputs-container'); const newRow = document.createElement('div'); newRow.className = 'row'; newRow.draggable = true; newRow.innerHTML = `<div class="drag-handle">☰</div><div class="input-group"><input type="text" name="time[]" placeholder="Début" class="level-select"><input type="text" name="time_end[]" placeholder="Fin" class="level-select"><input type="text" name="context[]" placeholder="Contexte" class="parent-selector"><input type="text" name="title[]" placeholder="Action" class="title-input"></div><button type="button" class="btn-remove" onclick="this.closest('.row').remove(); updatePreview();">X</button>`; newRow.addEventListener('dragstart', handleDragStart); newRow.addEventListener('dragover', handleDragOver); newRow.addEventListener('drop', handleDrop); newRow.querySelectorAll('input').forEach(input => { input.addEventListener('input', updatePreview); }); container.appendChild(newRow); updatePreview(); }
const data = <?php echo $loadedData; ?>;
window.onload = () => { if(data && data.time) { document.getElementById('inputs-container').innerHTML = ''; data.time.forEach((val, i) => { addRow(); const rows = document.querySelectorAll('.row'); const r = rows[rows.length - 1]; r.querySelector('[name="time[]"]').value = val; r.querySelector('[name="time_end[]"]').value = (data.time_end && data.time_end[i]) ? data.time_end[i] : ''; r.querySelector('[name="context[]"]').value = data.context[i] || ''; r.querySelector('[name="title[]"]').value = data.title[i] || ''; }); updatePreview(); } else { addRow(); } };
</script>
</body>
</html>