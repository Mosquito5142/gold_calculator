<?php
require '../config/db_connect.php';
require '../functions/check_login.php';
header('Content-Type: application/json');

try {
    // รับข้อมูล JSON
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['formula_name']) || !isset($data['items']) || !is_array($data['items'])) {
        throw new Exception('ข้อมูลไม่ถูกต้อง');
    }

    $user_id = $_SESSION['recipenecklace_users_id'] ?? null;

    if (!$user_id) {
        throw new Exception('กรุณาเข้าสู่ระบบก่อนบันทึกข้อมูล');
    }

    if (isset($pdo)) {
        // ถ้าใช้ PDO
        $pdo->beginTransaction();

        // บันทึกหัวข้อสูตร
        $stmt = $pdo->prepare("INSERT INTO gold_formulas (formula_name, created_by, formula_type) VALUES (:name, :user_id, :type)");
        $stmt->execute([
            'name' => $data['formula_name'],
            'user_id' => $user_id,
            'type' => 'per_borax'
        ]);

        $formula_id = $pdo->lastInsertId();

        // บันทึกรายการ
        $stmt = $pdo->prepare("INSERT INTO gold_formula_items (formula_id, item_name, weight, percentage, item_order) VALUES (:formula_id, :name, :weight, :percent, :order)");

        $order = 1;
        foreach ($data['items'] as $item) {
            $weight = isset($item['weight']) && $item['weight'] !== '' ? $item['weight'] : null;
            $percentage = isset($item['percentage']) && $item['percentage'] !== '' ? $item['percentage'] : null;

            $stmt->execute([
                'formula_id' => $formula_id,
                'name' => $item['name'],
                'weight' => $weight,
                'percent' => $percentage,
                'order' => $order++
            ]);
        }

        $pdo->commit();
    } else {
        // ถ้าใช้ MySQLi
        $conn->begin_transaction();

        // บันทึกหัวข้อสูตร
        $stmt = $conn->prepare("INSERT INTO gold_formulas (formula_name, created_by, formula_type) VALUES (?, ?, ?)");
        $formula_type = 'per_borax';
        $stmt->bind_param("sis", $data['formula_name'], $user_id, $formula_type);
        $stmt->execute();
        $formula_id = $conn->insert_id;

        // บันทึกรายการ
        $stmt = $conn->prepare("INSERT INTO gold_formula_items (formula_id, item_name, weight, percentage, item_order) VALUES (?, ?, ?, ?, ?)");

        $order = 1;
        foreach ($data['items'] as $item) {
            $weight = isset($item['weight']) && $item['weight'] !== '' ? $item['weight'] : null;
            $percentage = isset($item['percentage']) && $item['percentage'] !== '' ? $item['percentage'] : null;
            $name = $item['name'];

            $stmt->bind_param(
                "isddi",
                $formula_id,
                $name,
                $weight,
                $percentage,
                $order
            );
            $stmt->execute();
            $order++;
        }

        $conn->commit();
    }

    echo json_encode([
        'success' => true,
        'formula_id' => $formula_id,
        'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว'
    ]);
} catch (Exception $e) {
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
