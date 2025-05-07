<?php
require '../config/db_connect.php';
require '../functions/check_login.php';
header('Content-Type: application/json');

try {
    $user_id = $_SESSION['recipenecklace_users_id'] ?? null;

    if (!$user_id) {
        throw new Exception('กรุณาเข้าสู่ระบบก่อนโหลดข้อมูล');
    }

    // ถ้าใช้ PDO
    try {
        // ลองดึงข้อมูลพร้อมชื่อ-นามสกุลผู้ใช้
        $stmt = $pdo->prepare("
                SELECT 
                    f.formula_id, 
                    f.formula_name, 
                    f.created_at, 
                    f.created_by, 
                    u.username,
                    u.first_name,
                    u.last_name
                FROM gold_formulas f
                LEFT JOIN users u ON f.created_by = u.users_id
                WHERE f.formula_type = :type
                ORDER BY f.created_at DESC
            ");

        $stmt->execute(['type' => 'per_work']);
        $formulas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // หากไม่สามารถ JOIN กับตาราง users ได้ ให้ดึงเฉพาะข้อมูลสูตร
        $stmt = $pdo->prepare("
                SELECT 
                    formula_id, 
                    formula_name, 
                    created_at, 
                    created_by, 
                    created_by as username,
                    '' as first_name,
                    '' as last_name
                FROM gold_formulas
                WHERE formula_type = :type
                ORDER BY created_at DESC
            ");

        $stmt->execute(['type' => 'per_work']);
        $formulas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        'success' => true,
        'formulas' => $formulas
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
