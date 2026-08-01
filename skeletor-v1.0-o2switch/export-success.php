<?php
// Point d'entrée de l'export Nuxit - Page de confirmation visuelle
$dossierExport = './export-nuxit';
$cheminAbsolu = realpath($dossierExport);
$cheminAbsoluWindows = str_replace('/', '\\', $cheminAbsolu);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Export Nuxit Réussi</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; color: #333; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 600px; text-align: center; }
        h1 { color: #27ae60; margin-top: 0; }
        p { line-height: 1.6; }
        .folder-btn { display: inline-block; margin-top: 20px; padding: 12px 20px; background-color: #3498db; color: #fff; text-decoration: none; border-radius: 4px; font-weight: bold; cursor: pointer; border: none; transition: background 0.2s; }
        .folder-btn:hover { background-color: #2980b9; }
        .path { background: #f8f9fa; padding: 15px; border: 1px solid #ddd; border-radius: 4px; font-family: monospace; word-break: break-all; margin-top: 20px; text-align: left; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Export réussi !</h1>
        <p>Le dossier <strong>"export-nuxit"</strong> a été généré avec succès et est prêt à être déployé sur votre hébergement Nuxit.</p>
        
        <div class="path">
            <strong>Chemin du dossier :</strong><br>
            <?php echo $cheminAbsoluWindows; ?>
        </div>

        <button class="folder-btn" onclick="navigator.clipboard.writeText('<?php echo addslashes($cheminAbsoluWindows); ?>'); alert('Chemin copié dans le presse-papier !');">Copier le chemin</button>
    </div>
</body>
</html>