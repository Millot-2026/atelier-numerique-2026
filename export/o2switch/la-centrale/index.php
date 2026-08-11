<?php
$json_file = 'layout-order.json';
$default_order = ['personator', 'pixelart', 'skeletor', 'texturor', 'user_journey'];

$order = $default_order;
if (file_exists($json_file)) {
    $content = file_get_contents($json_file);
    $saved_order = json_decode($content, true);
    if (is_array($saved_order) && !empty($saved_order)) {
        $order = $saved_order;
    }
}

$modulesInfo = [
    'personator' => [
        'title' => 'Personnator v1.2',
        'icon' => '👤',
        'path' => 'personator-v1.2/index.php'
    ],
    'pixelart' => [
        'title' => 'Pixel Art',
        'icon' => '🎨',
        'path' => 'pixelart/index.php'
    ],
    'skeletor' => [
        'title' => 'Skeletor v1.0',
        'icon' => '💀',
        'path' => 'skeletor-v1.0/index.php'
    ],
    'texturor' => [
        'title' => 'Texturor',
        'icon' => '🧱',
        'path' => 'texturor/index.html'
    ],
    'user_journey' => [
        'title' => 'User Journey v1.0',
        'icon' => '🗺️',
        'path' => 'user_journey-v1.0/index.php'
    ]
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Centrale - Dashboard Unifié</title>
    <link rel="stylesheet" href="global.css">
    <!-- SortableJS for drag and drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
</head>
<body>

<div class="centrale-container">
    <header class="centrale-header">
        <h1>La Centrale</h1>
        <p>Dashboard Unifié</p>
    </header>

    <div id="modules-container">
        <?php foreach ($order as $moduleKey): ?>
            <?php if (isset($modulesInfo[$moduleKey])): ?>
                <?php $mod = $modulesInfo[$moduleKey]; ?>
                <div class="module-item" data-id="<?php echo htmlspecialchars($moduleKey); ?>">
                    <details class="module-details" id="details-<?php echo htmlspecialchars($moduleKey); ?>">
                        <summary class="module-summary">
                            <span class="drag-handle" title="Déplacer">⠿</span>
                            <span class="module-icon"><?php echo $mod['icon']; ?></span>
                            <span class="module-title"><?php echo htmlspecialchars($mod['title']); ?></span>
                        </summary>
                        <div class="module-content">
                            <iframe src="<?php echo htmlspecialchars($mod['path']); ?>" class="module-iframe" title="<?php echo htmlspecialchars($mod['title']); ?>" loading="lazy"></iframe>
                        </div>
                    </details>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<script src="script.js"></script>
<script>
    // Restauration et sauvegarde instantanée de l'état des accordéons de La Centrale
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('#modules-container details').forEach(details => {
            const id = details.id;
            if (id) {
                const saved = localStorage.getItem('centrale_state_' + id);
                if (saved !== null) {
                    details.open = (saved === 'true');
                } else if (id === 'details-pixelart') {
                    details.open = true; // État par défaut initial pour pixelart si jamais rien en storage
                }

                details.addEventListener('toggle', () => {
                    localStorage.setItem('centrale_state_' + id, details.open);
                });
            }
        });
    });
</script>
</body>
</html>