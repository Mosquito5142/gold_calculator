<?php
require_once '../functions/check_login.php';
require_once '../config/db_connect.php';
require_once '../functions/management_percent_necklace.php';

header('Content-Type: application/json');

// Check if pn_id is provided
if (!isset($_POST['pn_id']) || empty($_POST['pn_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'รหัสสร้อยไม่ถูกต้อง'
    ]);
    exit;
}

$pn_id = $_POST['pn_id'];

try {
    // Get necklace information
    $sql = "SELECT * FROM percent_necklace WHERE pn_id = :pn_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['pn_id' => $pn_id]);
    $necklace = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$necklace) {
        echo json_encode([
            'success' => false,
            'message' => 'ไม่พบข้อมูลสร้อย'
        ]);
        exit;
    }
    
    // Get necklace details
    $details = get_percent_necklace_detail($pdo, $pn_id);
    
    echo json_encode([
        'success' => true,
        'necklace' => $necklace,
        'details' => $details
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage()
    ]);
}