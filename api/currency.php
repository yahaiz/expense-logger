<?php
/**
 * Currency Toggle API
 */
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['currency']) && in_array($data['currency'], ['USD', 'تومان', 'هزار تومان'])) {
        $_SESSION['currency'] = $data['currency'];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid currency']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}