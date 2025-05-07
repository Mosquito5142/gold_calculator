<?php
session_start();
require_once '../config/db_connect.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['recipenecklace_users_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

// ตรวจสอบว่ามีการส่ง necklace_id มาหรือไม่
if (!isset($_GET['necklace_id']) || empty($_GET['necklace_id'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่ได้ระบุรหัสสร้อย']);
    exit;
}

$necklace_id = $_GET['necklace_id'];

try {
    // เพิ่ม debug เพื่อตรวจสอบข้อมูล
    $debug_stmt = $pdo->prepare("
        SELECT * FROM necklace_sharing WHERE nd_id = :necklace_id
    ");
    $debug_stmt->execute(['necklace_id' => $necklace_id]);
    $debug_sharing = $debug_stmt->fetchAll(PDO::FETCH_ASSOC);

    // ดึงข้อมูลการแชร์ที่มีอยู่แล้ว พร้อมข้อมูลผู้ใช้และผู้ที่แชร์
    $stmt = $pdo->prepare("
        SELECT ns.sharing_id, ns.nd_id, ns.users_id, ns.sharing_by, ns.updated_at,
               u.first_name, u.last_name, u.users_depart,
               sharer.first_name AS sharer_first_name, sharer.last_name AS sharer_last_name
        FROM necklace_sharing ns
        JOIN users u ON ns.users_id = u.users_id
        LEFT JOIN users sharer ON ns.sharing_by = sharer.users_id
        WHERE ns.nd_id = :necklace_id
        ORDER BY ns.updated_at DESC
    ");
    $stmt->execute(['necklace_id' => $necklace_id]);

    $sharing = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // เพิ่ม debug information ในการส่งกลับ
    echo json_encode([
        'success' => true,
        'sharing' => $sharing,
        'debug' => [
            'raw_sharing_data' => $debug_sharing,
            'query_string' => "SELECT ns.sharing_id, ns.nd_id, ns.users_id, ns.sharing_by, ns.updated_at, u.first_name, u.last_name, u.users_depart, sharer.first_name AS sharer_first_name, sharer.last_name AS sharer_last_name FROM necklace_sharing ns JOIN users u ON ns.users_id = u.users_id LEFT JOIN users sharer ON ns.sharing_by = sharer.users_id WHERE ns.nd_id = $necklace_id ORDER BY ns.updated_at DESC"
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
