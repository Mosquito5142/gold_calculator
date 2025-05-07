<?php
session_start();
require_once '../config/db_connect.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['recipenecklace_users_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

// ตรวจสอบว่ามีการส่ง sharing_id มาหรือไม่
if (!isset($_POST['sharing_id']) || empty($_POST['sharing_id'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่ได้ระบุรหัสการแชร์']);
    exit;
}

$sharing_id = $_POST['sharing_id'];
$current_user_id = $_SESSION['recipenecklace_users_id'];
$user_level = $_SESSION['recipenecklace_users_level'];

try {
    // ตรวจสอบว่าเป็นการแชร์ที่ผู้ใช้มีสิทธิ์ลบหรือไม่
    $check_stmt = $pdo->prepare("
        SELECT ns.*, nd.updated_users_id 
        FROM necklace_sharing ns
        JOIN necklace_detail nd ON ns.nd_id = nd.necklace_detail_id
        WHERE ns.sharing_id = :sharing_id
    ");
    $check_stmt->execute(['sharing_id' => $sharing_id]);
    $sharing = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    // ถ้าไม่พบข้อมูลการแชร์
    if (!$sharing) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลการแชร์']);
        exit;
    }

    // ลบข้อมูลการแชร์
    $delete_stmt = $pdo->prepare("DELETE FROM necklace_sharing WHERE sharing_id = :sharing_id");
    $delete_stmt->execute(['sharing_id' => $sharing_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'ลบการแชร์เรียบร้อยแล้ว'
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
?>