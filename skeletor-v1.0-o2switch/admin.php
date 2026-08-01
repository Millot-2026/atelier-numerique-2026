<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

$backupDir = 'core/backups/';

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
        td { padding: 12px 10px; border-bottom: 1px solid #333; }
        .actions-cell { white-space: nowrap; }
        .btn { display: inline-block; padding: 6px 12px; text-decoration: none; border-radius: 3px; font-size: 0.85rem; font-weight: bold; }
        .btn-load { background: #e67e22; color: white; }
        .btn-delete { background: #c0392b; color: white; margin-left: 5px; }
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
            <p style="color: #c0392b; font-weight: bold; background: #321; padding: 10px; border-radius: 4px;">Projet supprimé.</p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Nom du fichier JSON</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($backups as $file): ?>
                <tr>
                    <td><?php echo str_replace('.json', '', $file); ?></td>
                    <td class="actions-cell">
                        <a href="generator.php?load=<?php echo urlencode($file); ?>" class="btn btn-load">CHARGER</a>
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

</body>
</html>