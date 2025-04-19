<?php
require '../config/db_connect.php';
require '../functions/management_necklace_detail.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing ID']);
    exit;
}

$id = $_GET['id'];
$detail = get_necklace_detail_by_id($pdo, $id);

if ($detail) {
    echo json_encode(['success' => true, 'data' => $detail]);
} else {
    echo json_encode(['success' => false, 'message' => 'Record not found']);
}