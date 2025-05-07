<?php
require '../config/db_connect.php';
require '../functions/check_login.php';
header('Content-Type: application/json');

try {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception('รหัสสูตรไม่ถูกต้อง');
    }

    $formula_id = intval($_GET['id']);
    $user_id = $_SESSION['recipenecklace_users_id'] ?? null;

    if (!$user_id) {
        throw new Exception('กรุณาเข้าสู่ระบบก่อนโหลดข้อมูล');
    }

    // ถ้าใช้ PDO
    $stmt = $pdo->prepare("
            SELECT formula_id, formula_name, created_at, created_by, formula_type
            FROM gold_formulas
            WHERE formula_id = :formula_id AND formula_type = :type
        ");

    $stmt->execute([
        'formula_id' => $formula_id,
        'type' => 'per_work'
    ]);

    $formula = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$formula) {
        throw new Exception('ไม่พบข้อมูลสูตรหรือไม่ใช่สูตรสำหรับหน้านี้');
    }

    // ดึงรายการในสูตร
    $stmt = $pdo->prepare("
            SELECT item_name, weight, percentage
            FROM gold_formula_items
            WHERE formula_id = :formula_id
            ORDER BY item_order ASC
        ");

    $stmt->execute(['formula_id' => $formula_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // เพิ่มรายการเข้าไปในสูตร
    $formula['items'] = $items;

    echo json_encode([
        'success' => true,
        'formula' => $formula
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
