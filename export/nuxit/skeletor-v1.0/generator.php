<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'core/skeletor.php';

$backupDir = 'core/backups/';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

$loadedData = 'null';
$currentLoadedFile = '';
if (isset($_GET['load']) && !empty($_GET['load'])) {
    $currentLoadedFile = basename($_GET['load']);
    $fileToLoad = $backupDir . $currentLoadedFile;
    if (file_exists($fileToLoad) && !is_dir($fileToLoad)) {
        $loadedData = file_get_contents($fileToLoad);
    }
}

$backups = array_diff(scandir($backupDir), ['.', '..']);
$statusMessage = "";

// SAUVEGARDER CONFIG
if (isset($_POST['save_config'])) {
    $rawName = !empty($_POST['config_name']) ? $_POST['config_name'] : 'sans-nom-' . date('Y-m-d_H-i');
    $name = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $rawName), '-'));
    file_put_contents($backupDir . $name . '.json', json_encode($_POST));
    $statusMessage = "Configuration '$name' sauvegardée !";
    $currentLoadedFile = $name . '.json';
    $loadedData = file_get_contents($backupDir . $currentLoadedFile);
}

// SAUVEGARDER SOUS
if (isset($_POST['save_as_config'])) {
    $rawName = !empty($_POST['new_config_name']) ? $_POST['new_config_name'] : 'sans-nom-' . date('Y-m-d_H-i');
    $name = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $rawName), '-'));
    file_put_contents($backupDir . $name . '.json', json_encode($_POST));
    $statusMessage = "Configuration '$name' sauvegardée sous un nouveau nom !";
    $currentLoadedFile = $name . '.json';
    $loadedData = file_get_contents($backupDir . $currentLoadedFile);
}

// Recherche récursive d'un nœud parent et insertion d'un élément dans l'arbre
function skeletor_insert(&$arr, $parentName, $itemName, $isDir) {
    foreach ($arr as $key => &$value) {
        if ($key === $parentName && is_array($value)) {
            if ($isDir) {
                if (!isset($value[$itemName])) {
                    $value[$itemName] = [];
                }
            } else {
                $value[] = $itemName;
            }
            return true;
        }
        if (is_array($value) && skeletor_insert($value, $parentName, $itemName, $isDir)) {
            return true;
        }
    }
    return false;
}

// GÉNÉRER L'ARBORESCENCE
if (isset($_POST['generate']) && isset($_POST['level']) && isset($_POST['content'])) {
    $rawName = !empty($_POST['config_name']) ? $_POST['config_name'] : 'mon-site';
    $finalName = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $rawName), '-'));
    if (empty($finalName)) { $finalName = 'mon-site'; }

    file_put_contents($backupDir . $finalName . '.json', json_encode($_POST));

    $skeletor = new Skeletor($finalName);
    
    $tree = [];
    $levels = $_POST['level'];
    $contents = $_POST['content'];
    $extensions = $_POST['extension'] ?? [];
    $parents = $_POST['parent'] ?? [];

    $siteName = $finalName;
    foreach ($levels as $i => $type) {
        if ($type === 'site' && !empty($contents[$i])) {
            $siteName = $contents[$i];
            break;
        }
    }

    $tree[$siteName] = [];

    $pendingFolders = [];
    foreach ($levels as $i => $type) {
        $name = trim($contents[$i] ?? '');
        if ($name === '' || $type === 'site' || $type === 'page') continue;
        $par = $parents[$i] ?? 'Racine';
        $pendingFolders[] = ['name' => $name, 'parent' => $par];
    }

    $maxIterations = 50;
    while (!empty($pendingFolders) && $maxIterations > 0) {
        $maxIterations--;
        $progress = false;

        foreach ($pendingFolders as $index => $folder) {
            $name = $folder['name'];
            $par = $folder['parent'];

            if ($par === 'Racine' || $par === '-- Parent --' || $par === $siteName || empty($par)) {
                if (!isset($tree[$siteName][$name])) {
                    $tree[$siteName][$name] = [];
                }
                unset($pendingFolders[$index]);
                $progress = true;
            } else {
                if (skeletor_insert($tree[$siteName], $par, $name, true)) {
                    unset($pendingFolders[$index]);
                    $progress = true;
                }
            }
        }

        if (!$progress) break;
    }

    foreach ($levels as $i => $type) {
        $name = trim($contents[$i] ?? '');
        if ($name === '' || $type !== 'page') continue;
        
        $ext = isset($extensions[$i]) && !empty($extensions[$i]) ? $extensions[$i] : '.php';
        
        $extensionsList = ['.php', '.html', '.css', '.js', '.json', '.sass', '.scss', '.md', '.sql'];
        foreach ($extensionsList as $e) {
            if (str_ends_with(strtolower($name), $e)) {
                $name = substr($name, 0, -strlen($e));
                break;
            }
        }
        
        $fullName = $name . $ext;
        $par = $parents[$i] ?? 'Racine';

        if ($par === 'Racine' || $par === '-- Parent --' || $par === $siteName || empty($par)) {
            $tree[$siteName][] = $fullName;
        } else {
            if (!skeletor_insert($tree[$siteName], $par, $fullName, false)) {
                $tree[$siteName][] = $fullName;
            }
        }
    }

    $skeletor->arborate($tree);

    $statusMessage = "✅ ARBORESCENCE GÉNÉRÉE (LOCAL + _www/export/) : /export/$finalName !";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skeletor Loc</title>
    <style>
        * { box-sizing: border-box; }
        body { background-color: #1a1a1a; color: #eee; font-family: sans-serif; margin: 0; padding: 0; touch-action: manipulation; }
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; width: 100%; }
        .header-main { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; position: relative; gap: 10px; }
        .admin-link, .home-link { display: inline-flex; align-items: center; justify-content: center; padding: 5px 15px; border: 1px solid #f39c12; color: #f39c12; text-decoration: none; border-radius: 4px; font-size: 0.8em; z-index: 10; height: 35px; }
        .admin-link:hover, .home-link:hover { background-color: #f39c12; color: #fff; }
        .header-main h1 { flex: 1; text-align: center; margin: 0; color: #f39c12; font-size: 1.5em; }
        .load-zone, .save-zone-container { background: #222; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #333; }
        .load-form, .save-zone { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .load-form select, .save-zone input[type="text"], .save-as-box input[type="text"] { flex: 1; min-width: 150px; height: 40px; line-height: 40px; padding: 0 10px; background: #111; color: #fff; border: 1px solid #444; border-radius: 4px; }
        .btn-ok, .btn-save, .btn-save-as, .btn-validate { height: 40px; padding: 0 20px; cursor: pointer; border: none; border-radius: 4px; font-weight: bold; }
        .btn-ok { background: #eee; color: #333; }
        .btn-save { background: #f39c12; color: white; }
        .btn-save-as, .btn-validate { background: #27ae60; color: white; }
        .save-as-box { display: none; margin-top: 10px; gap: 10px; align-items: center; width: 100%; }
        .clear-btn { color: #f39c12; font-weight: bold; text-decoration: none; font-size: 0.9em; display: inline-flex; align-items: center; height: 40px; padding: 0 10px; }
        .row { display: flex; gap: 10px; margin-bottom: 10px; align-items: stretch; background: #222; padding: 10px; border-radius: 4px; flex-wrap: wrap; position: relative; }
        .row.dragging { opacity: 0.4; }
        .drag-handle { display: flex; align-items: center; cursor: grab; color: #555; font-size: 20px; padding: 0 5px; touch-action: none; }
        .input-group { display: flex; gap: 10px; flex: 1; flex-wrap: wrap; min-width: 250px; }
        .level-select, .content-input, .ext-select, .parent-select, .btn-remove { height: 40px; line-height: 40px; border: 1px solid #444; border-radius: 4px; background: #333; color: #fff; font-size: 14px; }
        .level-select { flex: 1 1 130px; padding: 0 10px; }
        .content-input { flex: 2 1 150px; padding: 0 10px; background: #111; }
        .ext-select { flex: 0 0 95px; padding: 0 5px; background: #222; display: none; }
        .parent-select { flex: 1 1 130px; padding: 0 10px; display: none; }
        .btn-remove { background: #c0392b; color: white; border: none; width: 40px; min-width: 40px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .btn-submit { width: 100%; padding: 15px; background: #f39c12; color: white; border: none; font-weight: bold; font-size: 1.1em; border-radius: 4px; cursor: pointer; text-transform: uppercase; }

        @media screen and (max-width: 768px) {
            .header-main { flex-wrap: nowrap; justify-content: space-between; }
            .header-main h1 { font-size: 1.5em; text-align: center; flex: 1; margin: 0 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .admin-link { flex-shrink: 0; }
            .home-link { flex-shrink: 0; }
            .load-form { flex-wrap: nowrap; }
            .load-form select { min-width: 0; flex: 1; }
            .btn-ok, .clear-btn { flex-shrink: 0; }
            .row { flex-direction: row; align-items: center; }
            .input-group { flex-direction: column; width: auto; flex: 1; }
            .level-select, .content-input, .ext-select, .parent-select { width: 100%; flex: none; height: 40px; }
            .btn-remove { width: 40px; min-width: 40px; height: 40px; }
            .save-zone { flex-direction: column; align-items: stretch; }
            .save-zone input[type="text"] { width: 100%; height: 40px; margin-left: 0 !important; }
            .btn-save, .btn-save-as { width: 100%; height: 40px; text-align: center; justify-content: center; margin-left: 0 !important; }
            .save-as-box { flex-direction: column; }
            .save-as-box input[type="text"], .btn-validate { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-main">
            <a href="./admin.php" class="admin-link">Admin</a>
            <h1>Skeletor Loc</h1>
            <a href="../" class="home-link">Home</a>
        </div>
        <div class="load-zone">
            <form method="GET" class="load-form">
                <span style="color:#ccc;">Charger :</span>
                <select name="load">
                    <option value="">-- Projet vierge --</option>
                    <?php foreach ($backups as $file): ?>
                        <option value="<?php echo $file; ?>" <?php echo ($currentLoadedFile == $file) ? 'selected' : ''; ?>>
                            <?php echo str_replace('.json', '', $file); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-ok">OK</button>
                <a href="generator.php" class="clear-btn">CLEAR</a>
            </form>
        </div>
        <?php if ($statusMessage): ?>
            <div style="padding: 15px; margin-bottom: 20px; background: #27ae60; color: white; border-radius: 4px; text-align: center;">
                <?php echo $statusMessage; ?>
            </div>
        <?php endif; ?>
        <form method="POST" id="main-form">
            <div id="inputs-container"></div>
            <button type="button" onclick="addRow()" style="width:100%; padding:12px; margin-bottom:20px; background: #333; color: #999; border: 1px dashed #555; cursor:pointer; border-radius:4px;">+ Ajouter une ligne</button>
            <div class="save-zone-container">
                <div class="save-zone">
                    <input type="text" id="config-name-input" name="config_name" placeholder="Nom du site" value="<?php echo !empty($currentLoadedFile) ? str_replace('.json', '', $currentLoadedFile) : ''; ?>">
                    <button type="submit" name="save_config" class="btn-save">SAUVEGARDER</button>
                    <button type="button" onclick="toggleSaveAs()" class="btn-save-as">SAUVEGARDER SOUS</button>
                </div>
                <div id="save-as-container" class="save-as-box">
                    <input type="text" id="new-config-name-input" name="new_config_name" placeholder="Nom de la copie...">
                    <button type="submit" name="save_as_config" class="btn-validate">VALIDER</button>
                </div>
            </div>
            <button type="submit" name="generate" class="btn-submit">GÉNÉRER L'ARBORESCENCE</button>
        </form>
    </div>
<script>
    function toggleSaveAs() {
        const box = document.getElementById('save-as-container');
        if (box.style.display === 'flex') {
            box.style.display = 'none';
        } else {
            box.style.display = 'flex';
            document.getElementById('new-config-name-input').focus();
        }
    }

    function syncSiteName() {
        const rows = document.querySelectorAll('.row');
        const saveInput = document.getElementById('config-name-input');
        rows.forEach(r => {
            const type = r.querySelector('.level-select').value;
            if (type === 'site') {
                const val = r.querySelector('.content-input').value.trim();
                if (val) {
                    saveInput.value = val;
                }
            }
        });
        updateAllFieldsVisibility();
    }

    function updateAllFieldsVisibility() {
        const rows = document.querySelectorAll('.row');
        let folders = [];
        
        rows.forEach(r => {
            const type = r.querySelector('.level-select').value;
            const val = r.querySelector('.content-input').value.trim();
            if ((type === 'folder' || type === 'subfolder' || type === 'site') && val) {
                folders.push(val);
            }
        });

        rows.forEach(r => {
            const type = r.querySelector('.level-select').value;
            const parentSel = r.querySelector('.parent-select');
            const extSel = r.querySelector('.ext-select');

            if (type === 'page') {
                extSel.style.display = 'block';
            } else {
                extSel.style.display = 'none';
            }

            if (type === 'page' || type === 'folder' || type === 'subfolder') {
                parentSel.style.display = 'block';
                const currentVal = parentSel.getAttribute('data-selected') || parentSel.value;
                parentSel.innerHTML = '<option value="Racine">Racine</option>';
                folders.forEach(f => {
                    const opt = document.createElement('option');
                    opt.value = f;
                    opt.textContent = f;
                    if (f === currentVal) opt.selected = true;
                    parentSel.appendChild(opt);
                });
                if (currentVal) parentSel.value = currentVal;
            } else {
                parentSel.style.display = 'none';
            }
        });
    }

    let draggedRow = null;

    function addRow(lvlVal = 'folder', contentVal = '', extVal = '.php', parentVal = 'Racine') {
        const container = document.getElementById('inputs-container');
        const row = document.createElement('div');
        row.className = 'row';
        row.draggable = true;
        
        row.innerHTML = `
            <div class="drag-handle" title="Glisser pour déplacer">☰</div>
            <div class="input-group">
                <select name="level[]" class="level-select" onchange="syncSiteName()">
                    <option value="site" ${lvlVal === 'site' ? 'selected' : ''}>Nom du site</option>
                    <option value="folder" ${lvlVal === 'folder' ? 'selected' : ''}>Dossier</option>
                    <option value="subfolder" ${lvlVal === 'subfolder' ? 'selected' : ''}>Sous-dossier</option>
                    <option value="page" ${lvlVal === 'page' ? 'selected' : ''}>Pages / Fichiers</option>
                </select>
                <input type="text" name="content[]" class="content-input" value="${contentVal}" placeholder="Nom..." oninput="syncSiteName()">
                <select name="extension[]" class="ext-select">
                    <option value=".php" ${extVal === '.php' ? 'selected' : ''}>.php</option>
                    <option value=".html" ${extVal === '.html' ? 'selected' : ''}>.html</option>
                    <option value=".css" ${extVal === '.css' ? 'selected' : ''}>.css</option>
                    <option value=".js" ${extVal === '.js' ? 'selected' : ''}>.js</option>
                    <option value=".json" ${extVal === '.json' ? 'selected' : ''}>.json</option>
                    <option value=".sass" ${extVal === '.sass' ? 'selected' : ''}>.sass</option>
                    <option value=".scss" ${extVal === '.scss' ? 'selected' : ''}>.scss</option>
                    <option value=".md" ${extVal === '.md' ? 'selected' : ''}>.md</option>
                    <option value=".sql" ${extVal === '.sql' ? 'selected' : ''}>.sql</option>
                </select>
                <select name="parent[]" class="parent-select" data-selected="${parentVal}" onchange="this.setAttribute('data-selected', this.value)">
                    <option value="Racine">Racine</option>
                </select>
            </div>
            <button type="button" class="btn-remove" onclick="this.closest('.row').remove(); syncSiteName();">X</button>
        `;

        row.addEventListener('dragstart', function(e) {
            draggedRow = this;
            e.dataTransfer.effectAllowed = 'move';
            setTimeout(() => this.classList.add('dragging'), 0);
        });

        row.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            draggedRow = null;
            clearDragOverStyles();
            syncSiteName();
        });

        row.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            if (this !== draggedRow) {
                const rect = this.getBoundingClientRect();
                const midpoint = rect.top + rect.height / 2;
                clearDragOverStyles();
                if (e.clientY < midpoint) {
                    this.style.borderTop = '2px solid #38bdf8';
                } else {
                    this.style.borderBottom = '2px solid #38bdf8';
                }
            }
        });

        row.addEventListener('dragleave', function() {
            clearDragOverStyles();
        });

        row.addEventListener('drop', function(e) {
            e.preventDefault();
            clearDragOverStyles();
            if (this !== draggedRow) {
                const rect = this.getBoundingClientRect();
                const midpoint = rect.top + rect.height / 2;
                if (e.clientY < midpoint) {
                    container.insertBefore(draggedRow, this);
                } else {
                    container.insertBefore(draggedRow, this.nextSibling);
                }
            }
        });

        const handle = row.querySelector('.drag-handle');
        handle.addEventListener('touchstart', function(e) {
            draggedRow = row;
            row.classList.add('dragging');
            e.preventDefault();
        }, { passive: false });

        document.addEventListener('touchmove', function(e) {
            if (!draggedRow) return;
            e.preventDefault();
            const touch = e.touches[0];
            const targetElement = document.elementFromPoint(touch.clientX, touch.clientY);
            const targetRow = targetElement ? targetElement.closest('.row') : null;

            clearDragOverStyles();
            if (targetRow && targetRow !== draggedRow) {
                const rect = targetRow.getBoundingClientRect();
                const midpoint = rect.top + rect.height / 2;
                if (touch.clientY < midpoint) {
                    targetRow.style.borderTop = '2px solid #38bdf8';
                } else {
                    targetRow.style.borderBottom = '2px solid #38bdf8';
                }
            }
        }, { passive: false });

        document.addEventListener('touchend', function(e) {
            if (!draggedRow) return;
            const touch = e.changedTouches[0];
            const targetElement = document.elementFromPoint(touch.clientX, touch.clientY);
            const targetRow = targetElement ? targetElement.closest('.row') : null;

            if (targetRow && targetRow !== draggedRow) {
                const rect = targetRow.getBoundingClientRect();
                const midpoint = rect.top + rect.height / 2;
                if (touch.clientY < midpoint) {
                    container.insertBefore(draggedRow, targetRow);
                } else {
                    container.insertBefore(draggedRow, targetRow.nextSibling);
                }
            }

            draggedRow.classList.remove('dragging');
            draggedRow = null;
            clearDragOverStyles();
            syncSiteName();
        });

        container.appendChild(row);
        
        const sel = row.querySelector('.parent-select');
        sel.value = parentVal;
        syncSiteName();
    }

    function clearDragOverStyles() {
        document.querySelectorAll('.row').forEach(r => {
            r.style.borderTop = '1px solid transparent';
            r.style.borderBottom = '1px solid transparent';
        });
    }

    window.onload = () => {
        const data = <?php echo $loadedData; ?>;
        if (data && data.level) {
            data.level.forEach((lvl, i) => {
                let contentRaw = data.content[i] || "";
                let extRaw = ".php";
                
                const extensionsList = ['.php', '.html', '.css', '.js', '.json', '.sass', '.scss', '.md', '.sql'];
                for (let ext of extensionsList) {
                    if (contentRaw.toLowerCase().endsWith(ext)) {
                        contentRaw = contentRaw.slice(0, -ext.length);
                        extRaw = ext;
                        break;
                    }
                }

                if (data.extension && data.extension[i]) {
                    extRaw = data.extension[i];
                }

                addRow(lvl, contentRaw, extRaw, data.parent ? (data.parent[i] || 'Racine') : 'Racine');
            });
            setTimeout(() => {
                syncSiteName();
            }, 50);
        } else {
            addRow('site', 'mon-site', '.php', 'Racine');
            addRow('page', 'index', '.php', 'Racine');
            addRow('folder', 'assets', '.php', 'Racine');
            addRow('folder', 'css', '.css', 'assets');
            addRow('page', 'styles', '.css', 'css');
            addRow('folder', 'scss', '.scss', 'assets');
            addRow('page', 'styles', '.scss', 'scss');
            addRow('folder', 'sass', '.sass', 'assets');
            addRow('page', 'styles', '.sass', 'sass');
            addRow('folder', 'js', '.js', 'assets');
            addRow('page', 'script', '.js', 'js');
            addRow('page', 'contact', '.php', 'Racine');
            addRow('page', 'mentions-legales', '.php', 'Racine');
        }
    };
</script>
</body>
</html>