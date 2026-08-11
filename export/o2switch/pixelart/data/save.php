<?php
$dataDir = __DIR__ . '/data';
$jsonFile = $dataDir . '/projects.json';

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

if (!file_exists($jsonFile)) {
    file_put_contents($jsonFile, json_encode([], JSON_PRETTY_PRINT));
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    header('Content-Type: application/json');
    echo file_get_contents($jsonFile);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input && isset($_POST['data'])) {
        $input = json_decode($_POST['data'], true);
    }

    $action = isset($input['action']) ? $input['action'] : '';
    $projects = json_decode(file_get_contents($jsonFile), true);
    if (!is_array($projects)) $projects = [];

    // Gestion de l'upload d'image d'arrière-plan
    $bgUrl = isset($input['background']) ? $input['background'] : '';
    if (isset($_FILES['bg_file']) && $_FILES['bg_file']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['bg_file']['name'], PATHINFO_EXTENSION);
        $fileName = 'bg_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        $uploadPath = $dataDir . '/' . $fileName;
        if (move_uploaded_file($_FILES['bg_file']['tmp_name'], $uploadPath)) {
            $bgUrl = 'data/' . $fileName;
        }
    }

    if ($action === 'save') {
        $id = isset($input['id']) && !empty($input['id']) ? $input['id'] : uniqid('proj_');
        $name = isset($input['name']) ? trim($input['name']) : 'Projet sans nom';
        
        $projectData = [
            'id' => $id,
            'name' => $name,
            'width' => intval($input['width']),
            'height' => intval($input['height']),
            'pixels' => $input['pixels'],
            'background' => $bgUrl,
            'bgOpacity' => floatval($input['bgOpacity'] ?? 0.5),
            'palette' => $input['palette'] ?? ['#000000'],
            'activeSwatchIndex' => intval($input['activeSwatchIndex'] ?? 0),
            'detailsState' => $input['detailsState'] ?? null, // Persistance des blocs ouverts/fermés
            'sectionsOrder' => $input['sectionsOrder'] ?? null, // Persistance de l'ordre des blocs (drag & drop)
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Mettre à jour ou insérer
        $found = false;
        foreach ($projects as &$p) {
            if ($p['id'] === $id) {
                $p = $projectData;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $projects[] = $projectData;
        }

        file_put_contents($jsonFile, json_encode($projects, JSON_PRETTY_PRINT));
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'id' => $id]);
        exit;
    }

    if ($action === 'delete') {
        $id = isset($input['id']) ? $input['id'] : '';
        $projects = array_values(array_filter($projects, function($p) use ($id) {
            return $p['id'] !== $id;
        }));
        file_put_contents($jsonFile, json_encode($projects, JSON_PRETTY_PRINT));
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
}
?>