<?php
/**
 * Calendar Toggle API
 */
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['calendar']) && in_array($data['calendar'], ['gregorian', 'jalali'])) {
        $_SESSION['calendar'] = $data['calendar'];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid calendar type']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
