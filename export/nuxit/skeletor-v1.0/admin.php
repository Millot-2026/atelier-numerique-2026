<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

$backupDir = 'core/backups/';
$exportDir = 'export/';

function zipFolder($source, $destination) {
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }
    $zip = new ZipArchive();
    if (!$zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        return false;
    }
    $source = realpath($source);
    if (is_dir($source)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($files as $file) {
            $file = realpath($file);
            $relativePath = substr($file, strlen($source) + 1);
            if (is_dir($file)) {
                $zip->addEmptyDir($relativePath);
            } else if (is_file($file)) {
                $zip->addFile($file, $relativePath);
            }
        }
    }
    return $zip->close();
}

if (isset($_GET['download'])) {
    $projectName = basename($_GET['download']);
    $folderToZip = $exportDir . $projectName;
    $zipFile = $exportDir . $projectName . '.zip';

    if (is_dir($folderToZip)) {
        if (zipFolder($folderToZip, $zipFile)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $projectName . '.zip"');
            header('Content-Length: ' . filesize($zipFile));
            readfile($zipFile);
            unlink($zipFile);
            exit;
        }
    }
}

if (isset($_GET['delete'])) {
    $fileToDelete = $backupDir . basename($_GET['delete']);
    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
        header("Location: admin.php?status=deleted");
        exit;
    }
}

$backups = array_diff(scandir($backupDir), ['.', '..']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personator Admin</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; background: #1a1a1a; color: #eee; margin: 0; padding: 0; }
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; width: 100%; }
        .admin-box { background: #222; padding: 15px; border-radius: 4px; border: 1px solid #333; margin-top: 0; }
        .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 2px solid #f39c12; padding-bottom: 10px; flex-wrap: wrap; gap: 10px; position: relative; }
        h1 { color: #f39c12; margin: 0; flex: 1; font-size: 1.5em; }
        .back-link { display: inline-flex; align-items: center; justify-content: center; padding: 5px 15px; border: 1px solid #f39c12; color: #f39c12; text-decoration: none; border-radius: 4px; font-size: 0.8em; height: 35px; }
        .back-link:hover { background-color: #f39c12; color: #fff; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; background: #333; padding: 10px; color: #aaa; }
        td { padding: 12px 10px; border-bottom: 1px solid #333; vertical-align: middle; }
        .actions-cell { white-space: nowrap; text-align: right; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; text-decoration: none; border-radius: 3px; font-size: 0.85rem; font-weight: bold; }
        .btn-load { background: #e67e22; color: white; }
        .btn-delete { background: #c0392b; color: white; margin-left: 5px; }
        .btn-folder { background: #333; color: #f39c12; border: 1px solid #444; margin-left: 5px; padding: 6px 10px; }
        .btn-folder:hover { background: #f39c12; color: white; border-color: #f39c12; }
        .btn-zip { background: #27ae60; color: white; margin-left: 5px; padding: 6px 10px; }
        .btn-zip:hover { background: #2ecc71; }
    </style>
</head>
<body>

<div class="container">
    <div class="admin-box">
        <div class="admin-header">
            <h1>Gestion des projets</h1>
            <a href="generator.php" class="back-link">Retour</a>
        </div>

        <?php if(isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
            <div id="status-message" style="color: #fff; font-weight: bold; background: #c0392b; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center;">Projet supprimé.</div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Nom du projet</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($backups as $file): 
                    $projectName = str_replace('.json', '', $file);
                    $exportPath = $exportDir . $projectName;
                    $hasExport = is_dir($exportPath);
                ?>
                <tr>
                    <td><?php echo $projectName; ?></td>
                    <td class="actions-cell">
                        <a href="generator.php?load=<?php echo urlencode($file); ?>" class="btn btn-load">CHARGER</a>
                        <?php if ($hasExport): ?>
                            <a href="<?php echo $exportPath; ?>" target="_blank" class="btn btn-folder" title="Ouvrir le dossier du projet dans un nouvel onglet">📁</a>
                            <a href="admin.php?download=<?php echo urlencode($projectName); ?>" class="btn btn-zip" title="Télécharger le projet (.zip)">📦 ZIP</a>
                        <?php endif; ?>
                        <a href="admin.php?delete=<?php echo urlencode($file); ?>" class="btn btn-delete" onclick="return confirm('Supprimer ce projet définitivement ?')">SUPPRIMER</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($backups)): ?>
                <tr>
                    <td colspan="2" style="text-align: center; color: #666; padding: 40px;">Aucune sauvegarde trouvée dans /core/backups/</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    setTimeout(() => {
        const msg = document.getElementById('status-message');
        if (msg) {
            msg.style.transition = 'opacity 0.5s ease';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 500);
        }
    }, 10000);
</script>

</body>
</html>