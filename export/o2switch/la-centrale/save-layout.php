<?php
header('Content-Type: application/json');

$json_file = 'layout-order.json';

// Receive POST data
$data = json_decode(file_get_contents('php://input'), true);

if ($data && isset($data['order']) && is_array($data['order'])) {
    $success = file_put_contents($json_file, json_encode($data['order'], JSON_PRETTY_PRINT));
    if ($success) {
        echo json_encode(['status' => 'success', 'message' => 'Layout saved']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to write to file']);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>
