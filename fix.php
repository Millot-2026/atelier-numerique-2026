<?php
$files = [
    'cms-2026-v8-full/detail.php', 'personator-v1.2/detail.php', 'user_journey-v1.0/detail.php',
    'modulor/detail.php', 'wordpress-portable/detail.php', 'texturor/detail.php',
    'la-centrale/detail.php', 'pixelart/detail.php', 'palettor/detail.php', 'workstation/detail.php'
];
foreach($files as $f) {
    $path = 'f:/_www/' . $f;
    if (file_exists($path)) {
        $c = file_get_contents($path);
        $c = str_replace("require_once __DIR__ . '/../partials/page-detail.php';", "require __DIR__ . '/../partials/page-detail.php';", $c);
        $c = str_replace("require_once __DIR__ . '/../partials/page-pixelart.php';", "require __DIR__ . '/../partials/page-pixelart.php';", $c);
        file_put_contents($path, $c);
    }
}
echo "Done";
