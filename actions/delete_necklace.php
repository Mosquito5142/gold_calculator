<?php
require '../config/db_connect.php';
header('Content-Type: application/json');

try {
    $pdo->beginTransaction();
    $id = $_GET['id'];

    // First, get the image filename from the database
    $stmt = $pdo->prepare("SELECT image FROM necklace_detail WHERE necklace_detail_id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();

    // Delete related records first (foreign key constraints)
    $pdo->prepare("DELETE FROM necklace_proportions WHERE necklace_detail_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM necklace_tbs WHERE necklace_detail_id = ?")->execute([$id]);
    
    // Delete main record
    $pdo->prepare("DELETE FROM necklace_detail WHERE necklace_detail_id = ?")->execute([$id]);

    // Delete the image file if it exists
    if (!empty($image)) {
        $image_path = dirname(dirname(__FILE__)) . '/uploads/img/necklace_detail/' . $image;
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}