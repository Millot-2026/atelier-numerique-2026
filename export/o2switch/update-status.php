<?php
if (isset($_POST['project']) && isset($_POST['status'])) {
    $projectName = basename($_POST['project']);
    $newStatus = $_POST['status'];
    
    $jsonFile = 'statuses.json';
    $statuses = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
    
    $statuses[$projectName] = $newStatus;
    
    file_put_contents($jsonFile, json_encode($statuses, JSON_PRETTY_PRINT));
    echo json_encode(['success' => true]);
    exit;
}
echo json_encode(['success' => false]);