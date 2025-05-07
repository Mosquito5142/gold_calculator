<?php
require '../config/db_connect.php';
require '../functions/check_login.php';
header('Content-Type: application/json');

try {
    // ตรวจสอบว่ามีการส่ง ID มาหรือไม่
    if (!isset($_POST['formula_id']) || !is_numeric($_POST['formula_id'])) {
        throw new Exception('รหัสสูตรไม่ถูกต้อง');
    }

    $formula_id = intval($_POST['formula_id']);
    $user_id = $_SESSION['recipenecklace_users_id'] ?? null;

    if (!$user_id) {
        throw new Exception('กรุณาเข้าสู่ระบบก่อนลบข้อมูล');
    }

    $stmt = $pdo->prepare("SELECT formula_id FROM gold_formulas WHERE formula_id = :formula_id AND created_by = :user_id AND formula_type = :type");
    $stmt->execute([
        'formula_id' => $formula_id,
        'user_id' => $user_id,
        'type' => 'per_work'
    ]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('ไม่พบสูตรหรือไม่มีสิทธิ์ลบสูตรนี้');
    }

    // เริ่ม transaction
    $pdo->beginTransaction();

    // ลบรายการในสูตร
    $stmt = $pdo->prepare("DELETE FROM gold_formula_items WHERE formula_id = :formula_id");
    $stmt->execute(['formula_id' => $formula_id]);

    // ลบสูตรหลัก
    $stmt = $pdo->prepare("DELETE FROM gold_formulas WHERE formula_id = :formula_id");
    $stmt->execute(['formula_id' => $formula_id]);

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'ลบสูตรเรียบร้อยแล้ว'
    ]);
} catch (Exception $e) {
    // Rollback ในกรณีที่เกิดข้อผิดพลาด
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    } elseif (isset($conn) && $conn->connect_errno === 0) {
        $conn->rollback();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
