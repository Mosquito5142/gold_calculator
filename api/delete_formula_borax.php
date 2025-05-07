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

    if (isset($pdo)) {
        // ถ้าใช้ PDO
        // ตรวจสอบว่าผู้ใช้มีสิทธิ์ลบสูตรนี้หรือไม่และเป็นสูตรประเภท per_borax
        $stmt = $pdo->prepare("SELECT formula_id FROM gold_formulas WHERE formula_id = :formula_id AND created_by = :user_id AND formula_type = :type");
        $stmt->execute([
            'formula_id' => $formula_id,
            'user_id' => $user_id,
            'type' => 'per_borax'
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
    } else {
        // ถ้าใช้ MySQLi
        // ตรวจสอบว่าผู้ใช้มีสิทธิ์ลบสูตรนี้หรือไม่
        $formula_type = 'per_borax';
        $stmt = $conn->prepare("SELECT formula_id FROM gold_formulas WHERE formula_id = ? AND created_by = ? AND formula_type = ?");
        $stmt->bind_param("iis", $formula_id, $user_id, $formula_type);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception('ไม่พบสูตรหรือไม่มีสิทธิ์ลบสูตรนี้');
        }

        // เริ่ม transaction
        $conn->begin_transaction();

        // ลบรายการในสูตร
        $stmt = $conn->prepare("DELETE FROM gold_formula_items WHERE formula_id = ?");
        $stmt->bind_param("i", $formula_id);
        $stmt->execute();

        // ลบสูตรหลัก
        $stmt = $conn->prepare("DELETE FROM gold_formulas WHERE formula_id = ?");
        $stmt->bind_param("i", $formula_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();
    }

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
