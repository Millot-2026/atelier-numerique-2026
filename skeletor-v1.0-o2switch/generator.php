<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'core/skeletor.php';

// GÉNÉRER ET TÉLÉCHARGER LE ZIP (Zéro stockage persistant sur le serveur)
if (isset($_POST['generate']) && isset($_POST['level']) && isset($_POST['content'])) {
    // Vérification du Honeypot anti-spam
    if (!empty($_POST['website_hp'])) {
        die("Spam détecté.");
    }

    $rawName = !empty($_POST['config_name']) ? $_POST['config_name'] : 'mon-site';
    $finalName = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $rawName), '-'));
    if (empty($finalName)) { $finalName = 'mon-site'; }

    // Préparation des données JSON pour l'archivage client
    $jsonData = json_encode($_POST);

    // Création d'un espace temporaire unique pour la génération du ZIP
    $tmpDir = sys_get_temp_dir() . '/' . uniqid('skeletor_', true);
    if (!is_dir($tmpDir)) {
        mkdir($tmpDir, 0777, true);
    }
    $exportPath = $tmpDir . '/' . $finalName;
    if (!is_dir($exportPath)) {
        mkdir($exportPath, 0777, true);
    }

    $tree = [];
    $levels = $_POST['level'];
    $contents = $_POST['content'];
    $parents = $_POST['parent'] ?? [];

    $siteName = $finalName;
    foreach ($levels as $i => $type) {
        if ($type === 'site' && !empty($contents[$i])) {
            $siteName = $contents[$i];
            break;
        }
    }

    $tree[$siteName] = [];

    foreach ($levels as $i => $type) {
        $name = trim($contents[$i] ?? '');
        if ($name === '') continue;
        $par = $parents[$i] ?? '-- Parent --';

        if ($type === 'site') {
            continue;
        } elseif ($type === 'folder' || $type === 'subfolder') {
            if ($par === '-- Parent --' || $par === $siteName) {
                $tree[$siteName][$name] = [];
            } else {
                $tree[$siteName][$par][$name] = [];
            }
        } elseif ($type === 'page') {
            if ($par === '-- Parent --' || $par === $siteName) {
                $tree[$siteName][] = $name;
            } else {
                if (!isset($tree[$siteName][$par])) {
                    $tree[$siteName][$par] = [];
                }
                $tree[$siteName][$par][] = $name;
            }
        }
    }

    // Fonction récursive pour créer physiquement l'arborescence dans le dossier temporaire
    function createTempArbor($base, $structure) {
        foreach ($structure as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    file_put_contents($base . '/' . $value, '');
                } else {
                    $subPath = $base . '/' . $key;
                    if (!is_dir($subPath)) mkdir($subPath, 0777, true);
                    createTempArbor($subPath, $value);
                }
            }
        }
    }

    createTempArbor($exportPath, $tree);

    // On ajoute aussi le fichier JSON de configuration dans le ZIP pour que le client puisse le réimporter plus tard !
    file_put_contents($exportPath . '/' . $finalName . '.json', $jsonData);

    // Compression en ZIP
    $zipFileName = sys_get_temp_dir() . '/' . $finalName . '_' . date('Y-m-d') . '.zip';
    $zip = new ZipArchive();
    if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($exportPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($exportPath) + 1);
                $zip->addFile($filePath, $finalName . '/' . $relativePath);
            }
        }
        $zip->close();
    }

    // Envoi du ZIP au navigateur pour téléchargement immédiat
    if (file_exists($zipFileName)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($zipFileName) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($zipFileName));
        readfile($zipFileName);

        @unlink($zipFileName);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skeletor v1.0 - O2Switch Public</title>
    <style>
        * { box-sizing: border-box; }
        body { background-color: #1a1a1a; color: #eee; font-family: sans-serif; margin: 0; padding: 0; touch-action: manipulation; }
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; width: 100%; }
        .header-main { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; position: relative; gap: 10px; }
        
        .admin-link, .home-link { display: inline-flex; align-items: center; justify-content: center; padding: 5px 15px; border: 1px solid #f39c12; color: #f39c12; text-decoration: none; border-radius: 4px; font-size: 0.8em; z-index: 10; height: 35px; visibility: hidden !important; opacity: 0 !important; pointer-events: none !important; }
        
        .header-main h1 { flex: 1; text-align: center; margin: 0; color: #f39c12; font-size: 1.5em; }
        .load-zone, .save-zone-container { background: #222; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #333; }
        .load-form, .save-zone { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .load-form select, .save-zone input[type="text"] { flex: 1; min-width: 150px; height: 40px; line-height: 40px; padding: 0 10px; background: #111; color: #fff; border: 1px solid #444; border-radius: 4px; }
        .btn-ok, .btn-save, .btn-delete { height: 40px; padding: 0 20px; cursor: pointer; border: none; border-radius: 4px; font-weight: bold; }
        .btn-ok { background: #eee; color: #333; }
        .btn-save { background: #f39c12; color: white; }
        .btn-delete { background: #c0392b; color: white; padding: 0 10px; }
        .clear-btn { color: #f39c12; font-weight: bold; text-decoration: none; font-size: 0.9em; display: inline-flex; align-items: center; height: 40px; padding: 0 10px; }
        .row { display: flex; gap: 10px; margin-bottom: 10px; align-items: stretch; background: #222; padding: 10px; border-radius: 4px; flex-wrap: wrap; position: relative; }
        .row.dragging { opacity: 0.4; }
        .drag-handle { display: flex; align-items: center; cursor: grab; color: #555; font-size: 20px; padding: 0 5px; touch-action: none; }
        .input-group { display: flex; gap: 10px; flex: 1; flex-wrap: wrap; min-width: 250px; }
        .level-select, .content-input, .parent-select, .btn-remove { height: 40px; line-height: 40px; border: 1px solid #444; border-radius: 4px; background: #333; color: #fff; font-size: 14px; }
        .level-select { flex: 1 1 140px; padding: 0 10px; }
        .content-input { flex: 2 1 180px; padding: 0 10px; background: #111; }
        .parent-select { flex: 1 1 140px; padding: 0 10px; display: none; }
        .btn-remove { background: #c0392b; color: white; border: none; width: 40px; min-width: 40px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .btn-submit { width: 100%; padding: 15px; background: #f39c12; color: white; border: none; font-weight: bold; font-size: 1.1em; border-radius: 4px; cursor: pointer; text-transform: uppercase; }
        .hp-field { display: none !important; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-main">
            <a href="./admin.php" class="admin-link">Admin</a>
            <h1>Skeletor v1.0</h1>
            <a href="../" class="home-link">Home</a>
        </div>
        
        <div class="load-zone">
            <div class="load-form">
                <span style="color:#ccc;">Mes projets (Navigateur) :</span>
                <select id="local-load-select">
                    <option value="">-- Projet vierge --</option>
                </select>
                <button type="button" class="btn-ok" onclick="loadLocalProject()">Charger</button>
                <button type="button" class="btn-delete" onclick="deleteLocalProject()" title="Supprimer ce projet">🗑️</button>
                <a href="generator.php" class="clear-btn">NOUVEAU</a>
            </div>
        </div>

        <div id="status-message" style="display:none; padding: 15px; margin-bottom: 20px; background: #27ae60; color: white; border-radius: 4px; text-align: center;"></div>

        <form method="POST" id="main-form">
            <div class="hp-field">
                <label>Ne remplissez pas ce champ si vous êtes humain :</label>
                <input type="text" name="website_hp" value="">
            </div>

            <div id="inputs-container"></div>
            <button type="button" onclick="addRow()" style="width:100%; padding:12px; margin-bottom:20px; background: #333; color: #999; border: 1px dashed #555; cursor:pointer; border-radius:4px;">+ Ajouter une ligne</button>
            
            <div class="save-zone-container">
                <div class="save-zone">
                    <input type="text" id="config-name-input" name="config_name" placeholder="Nom du projet">
                    <button type="button" onclick="saveLocalProject()" class="btn-save">💾 SAUVEGARDER (Local)</button>
                </div>
            </div>
            
            <button type="submit" name="generate" class="btn-submit">GÉNÉRER ET TÉLÉCHARGER LE ZIP</button>
        </form>
    </div>
<script>
    function showStatus(msg) {
        const box = document.getElementById('status-message');
        box.textContent = msg;
        box.style.display = 'block';
        setTimeout(() => box.style.display = 'none', 3000);
    }

    function refreshLocalStorageSelect(selectedName = '') {
        const select = document.getElementById('local-load-select');
        select.innerHTML = '<option value="">-- Projet vierge --</option>';
        
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key.startsWith('skeletor_')) {
                const projectName = key.replace('skeletor_', '');
                const opt = document.createElement('option');
                opt.value = projectName;
                opt.textContent = projectName;
                if (projectName === selectedName) opt.selected = true;
                select.appendChild(opt);
            }
        }
    }

    function saveLocalProject() {
        const projectNameInput = document.getElementById('config-name-input').value.trim();
        if (!projectNameInput) {
            alert('Veuillez donner un nom à votre projet pour le sauvegarder.');
            return;
        }
        
        const formData = {};
        const rows = document.querySelectorAll('.row');
        formData.level = [];
        formData.content = [];
        formData.parent = [];

        rows.forEach(r => {
            formData.level.push(r.querySelector('.level-select').value);
            formData.content.push(r.querySelector('.content-input').value);
            formData.parent.push(r.querySelector('.parent-select').value);
        });

        localStorage.setItem('skeletor_' + projectNameInput, JSON.stringify(formData));
        refreshLocalStorageSelect(projectNameInput);
        showStatus("Configuration '" + projectNameInput + "' sauvegardée dans votre navigateur !");
    }

    function loadLocalProject() {
        const select = document.getElementById('local-load-select');
        const projectName = select.value;
        if (!projectName) {
            window.location.href = 'generator.php';
            return;
        }

        const dataString = localStorage.getItem('skeletor_' + projectName);
        if (dataString) {
            const data = JSON.parse(dataString);
            document.getElementById('inputs-container').innerHTML = '';
            document.getElementById('config-name-input').value = projectName;

            if (data && data.level) {
                data.level.forEach((lvl, i) => {
                    addRow(lvl, data.content[i] || "", data.parent[i] || '-- Parent --');
                });
                setTimeout(() => {
                    const rows = document.querySelectorAll('.row');
                    rows.forEach((r, i) => {
                        if (data.parent && data.parent[i]) {
                            const sel = r.querySelector('.parent-select');
                            sel.value = data.parent[i];
                            sel.setAttribute('data-selected', data.parent[i]);
                        }
                    });
                    syncSiteName();
                }, 50);
            }
            showStatus("Configuration '" + projectName + "' chargée !");
        }
    }

    function deleteLocalProject() {
        const select = document.getElementById('local-load-select');
        const projectName = select.value;
        if (!projectName) return;

        if (confirm("Voulez-vous vraiment supprimer ce projet de votre navigateur ?")) {
            localStorage.removeItem('skeletor_' + projectName);
            refreshLocalStorageSelect();
            document.getElementById('config-name-input').value = '';
            showStatus("Projet supprimé.");
        }
    }

    function syncSiteName() {
        const rows = document.querySelectorAll('.row');
        const saveInput = document.getElementById('config-name-input');
        rows.forEach(r => {
            const type = r.querySelector('.level-select').value;
            if (type === 'site') {
                const val = r.querySelector('.content-input').value.trim();
                if (val && !saveInput.value) {
                    saveInput.value = val;
                }
            }
        });
        updateAllParentSelects();
    }

    function updateAllParentSelects() {
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
            if (type === 'page' || type === 'folder' || type === 'subfolder') {
                parentSel.style.display = 'block';
                const currentVal = parentSel.getAttribute('data-selected') || parentSel.value;
                parentSel.innerHTML = '<option value="-- Parent --">-- Parent --</option>';
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

    function addRow(lvlVal = 'folder', contentVal = '', parentVal = '-- Parent --') {
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
                <select name="parent[]" class="parent-select" data-selected="${parentVal}" onchange="this.setAttribute('data-selected', this.value)">
                    <option value="-- Parent --">-- Parent --</option>
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
        refreshLocalStorageSelect();
        addRow('site', '');
    };
</script>
</body>
</html>