<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
require_once 'core/skeletor.php';
$backupDir = 'core/backups/';
if (!is_dir($backupDir)) { mkdir($backupDir, 0777, true); }$loadedData = 'null';
if (isset($_GET['load']) && !empty($_GET['load'])) {$file = $backupDir . basename($_GET['load']);
    if (file_exists($file)) { $loadedData = file_get_contents($file); }
}
$backups = array_diff(scandir($backupDir), ['.', '..']);

$statusMessage = "";
if (isset($_POST['save_config'])) {$name = !empty($_POST['config_name']) ? basename($_POST['config_name']) : 'save-' . date('Y-m-d');
    file_put_contents($backupDir . $name . '.json', json_encode($_POST));
    $statusMessage = "Configuration '$name' sauvegardée !";
}
if (isset($_POST['generate'])) {$statusMessage = "✅ Parcours enregistré !";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/style.css">
    <title>User Journey v1.0</title>
    <link rel="stylesheet" href="../global.css">
</head>
<body>
<div class="container">
    <div class="header-main">
        <a href="admin.php" class="admin-link">Admin</a>
        <h1>User Journey v1.0</h1>
    </div>

    <?php if($statusMessage): ?>
        <div class="status-bar"><?php echo $statusMessage; ?></div>
    <?php endif; ?>

    <div class="load-zone">
        <form method="GET" class="load-form">
            <label>Charger :</label>
            <select name="load" style="flex-grow: 1; min-width: 150px;">
                <option value="">-- Projet vierge --</option>
                <?php foreach ($backups as$file): ?>
                    <option value="<?php echo $file; ?>" <?php echo (isset($_GET['load']) && $_GET['load'] ==$file) ? 'selected' : ''; ?>>
                        <?php echo str_replace('.json', '', $file); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" style="padding: 10px 20px; cursor:pointer;">OK</button>
            <a href="?" style="color: orange; text-decoration: none; font-size: 0.8rem; font-weight: bold; padding: 5px;">CLEAR</a>
        </form>
    </div>

    <form method="POST" id="main-form">
        <div id="inputs-container"></div>
        <button type="button" class="btn-add" onclick="addRow()">+ Ajouter une étape</button>

        <div class="save-zone">
            <input type="text" name="config_name" placeholder="Nom du projet" class="config-name-input" value="<?php echo isset($_GET['load']) ? str_replace('.json', '', $_GET['load']) : ''; ?>">
            <button type="submit" name="save_config" class="btn-save">💾 SAUVEGARDER</button>
        </div>

        <div class="save-zone" style="margin-bottom: 0;">
            <button type="submit" name="generate" class="btn-submit">FINALISER LE PARCOURS</button>
            <?php if (isset($_GET['load'])): ?>
                <a href="presentation.php?project=<?php echo str_replace('.json', '', $_GET['load']); ?>" target="_blank" class="admin-link" style="margin-left:10px;">👀 Voir la présentation</a>
            <?php endif; ?>
        </div>
    </form>

    <div id="journey-preview" style="margin-top: 15px; padding: 15px; background: #222; border: 2px solid #333; border-radius: 8px;">
        <h3 style="color: #e67e22; margin-top: 0; margin-bottom: 10px;">👁️ Aperçu du parcours</h3>
        <div id="preview-list" style="display: flex; flex-direction: column; gap: 10px;"></div>
    </div>
</div>

<script>window.initialData = <?php echo $loadedData; ?>;</script>
<script src="script.js"></script>
</body>
</html>