<?php
session_start();
require_once '../config/db_connect.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['recipenecklace_users_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

// ตรวจสอบว่ามีการส่งข้อมูลมาถูกต้องหรือไม่
if (!isset($_POST['necklace_id']) || empty($_POST['necklace_id'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่ได้ระบุรหัสสร้อย']);
    exit;
}

if (!isset($_POST['users_id']) || !is_array($_POST['users_id']) || empty($_POST['users_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเลือกผู้ใช้ที่ต้องการแชร์']);
    exit;
}

$necklace_id = (int)$_POST['necklace_id'];
$users_ids = $_POST['users_id'];
$current_user_id = (int)$_SESSION['recipenecklace_users_id']; // เก็บ user ID ปัจจุบัน

try {
    $pdo->beginTransaction();
    
    // เตรียมคำสั่ง SQL สำหรับตรวจสอบการแชร์ที่มีอยู่แล้ว
    $checkStmt = $pdo->prepare("
        SELECT COUNT(*) FROM necklace_sharing 
        WHERE nd_id = :necklace_id AND users_id = :user_id
    ");
    
    // เตรียมคำสั่ง SQL สำหรับเพิ่มการแชร์ (ไม่รวมฟิลด์ sharing_id เพราะควรเป็น AUTO_INCREMENT)
    $insertStmt = $pdo->prepare("
        INSERT INTO necklace_sharing (nd_id, users_id, sharing_by) 
        VALUES (:necklace_id, :user_id, :sharing_by)
    ");
    
    // วนลูปผ่านผู้ใช้ที่ต้องการแชร์
    $added = 0;
    foreach ($users_ids as $user_id) {
        // ตรวจสอบว่ามีการแชร์อยู่แล้วหรือไม่
        $checkStmt->execute([
            'necklace_id' => $necklace_id,
            'user_id' => $user_id
        ]);
        
        if ($checkStmt->fetchColumn() == 0) {
            // ถ้ายังไม่มีการแชร์ ให้เพิ่มใหม่
            $insertStmt->execute([
                'necklace_id' => $necklace_id,
                'user_id' => $user_id,
                'sharing_by' => $current_user_id  // บันทึก ID ของผู้แชร์
            ]);
            $added++;
        }
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => "เพิ่มการแชร์จำนวน $added รายการ"
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
?>