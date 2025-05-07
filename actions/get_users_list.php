<?php
session_start();
require_once '../config/db_connect.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['recipenecklace_users_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

try {
    $current_user_id = $_SESSION['recipenecklace_users_id'];
    
    // ดึงข้อมูลผู้ใช้ทั้งหมดที่ไม่ใช่ตัวเอง และมีสถานะ active
    $stmt = $pdo->prepare("
        SELECT users_id, first_name, last_name, username, users_level, users_depart, users_status 
        FROM users 
        WHERE users_id != :current_user_id 
        AND users_status = 'Enable'
        ORDER BY first_name, last_name
    ");
    $stmt->execute(['current_user_id' => $current_user_id]);
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'users' => $users
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage()
    ]);
}
?>