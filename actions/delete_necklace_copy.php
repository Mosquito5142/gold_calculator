<?php
session_start();
require '../config/db_connect.php';
require '../functions/management_necklace_detail.php';
header('Content-Type: application/json');

// ตรวจสอบว่ามีการส่ง ID มาหรือไม่
if (empty($_POST['necklace_id'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูล ID ที่ต้องการลบ']);
    exit;
}

try {
    $necklace_id = $_POST['necklace_id'];
    $user_id = $_SESSION['recipenecklace_users_id'];
    $is_admin = isset($_SESSION['recipenecklace_users_level']) && $_SESSION['recipenecklace_users_level'] === 'Admin';
    
    // ตรวจสอบว่าเป็นเจ้าของหรือ admin หรือไม่
    $stmt = $pdo->prepare("SELECT updated_users_id, image FROM necklace_detail WHERE necklace_detail_id = :id");
    $stmt->execute(['id' => $necklace_id]);
    $necklace = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$necklace) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลสร้อย']);
        exit;
    }
    
    // ตรวจสอบสิทธิ์การลบ
    if ($necklace['updated_users_id'] != $user_id && !$is_admin) {
        echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ลบข้อมูลนี้']);
        exit;
    }
    
    // เก็บชื่อไฟล์รูปภาพไว้ลบภายหลัง
    $image_filename = $necklace['image'];
    
    $pdo->beginTransaction();
    
    // ค้นหาข้อมูล ratio_id ที่ใช้โดยสร้อยนี้
    $stmt = $pdo->prepare("SELECT ratio_id FROM necklace_calculation WHERE necklace_detail_id = :id");
    $stmt->execute(['id' => $necklace_id]);
    $ratio_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // ลบข้อมูลการคำนวณ
    $stmt = $pdo->prepare("DELETE FROM necklace_calculation WHERE necklace_detail_id = :id");
    $stmt->execute(['id' => $necklace_id]);
    
    // ลบข้อมูล ratio_data ที่เกี่ยวข้อง (เฉพาะที่ไม่ได้ใช้โดยสร้อยอื่น)
    if (!empty($ratio_ids)) {
        foreach ($ratio_ids as $ratio_id) {
            // ตรวจสอบว่า ratio_id นี้ถูกใช้โดยสร้อยคอใดอีกบ้าง
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM necklace_calculation WHERE ratio_id = :ratio_id AND necklace_detail_id != :necklace_id");
            $stmt->execute([
                'ratio_id' => $ratio_id,
                'necklace_id' => $necklace_id
            ]);
            $used_count = $stmt->fetchColumn();
            
            // ถ้าไม่มีการใช้งานโดยสร้อยอื่น ให้ลบทิ้ง
            if ($used_count == 0) {
                $stmt = $pdo->prepare("DELETE FROM ratio_data WHERE ratio_id = :ratio_id");
                $stmt->execute(['ratio_id' => $ratio_id]);
            }
        }
    }
    
    // ลบข้อมูล TBS
    $stmt = $pdo->prepare("DELETE FROM necklace_tbs WHERE necklace_detail_id = :id");
    $stmt->execute(['id' => $necklace_id]);
    
    // ลบข้อมูลสัดส่วน
    $stmt = $pdo->prepare("DELETE FROM necklace_proportions WHERE necklace_detail_id = :id");
    $stmt->execute(['id' => $necklace_id]);
    
    // ลบข้อมูล necklace_ratio_copy (ถ้ามีตารางนี้)
    try {
        $stmt = $pdo->prepare("DELETE FROM necklace_ratio_copy WHERE necklace_detail_id = :id");
        $stmt->execute(['id' => $necklace_id]);
    } catch (PDOException $e) {
        // ไม่ต้องทำอะไร อาจจะยังไม่มีตารางนี้
    }
    
    // ลบข้อมูลหลัก
    $stmt = $pdo->prepare("DELETE FROM necklace_detail WHERE necklace_detail_id = :id");
    $stmt->execute(['id' => $necklace_id]);
    
    $pdo->commit();
    
    // ลบไฟล์รูปภาพจากเซิร์ฟเวอร์ (ถ้ามี)
    if (!empty($image_filename)) {
        $image_path = dirname(dirname(__FILE__)) . '/uploads/img/necklace_detail/' . $image_filename;
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    echo json_encode(['success' => true, 'message' => 'ลบข้อมูลและรูปภาพเรียบร้อยแล้ว']);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}