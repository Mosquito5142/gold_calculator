<?php
session_start();
require '../config/db_connect.php';
require '../functions/management_percent_necklace.php';
header('Content-Type: application/json');

try {
    // ตรวจสอบว่ามี session users_id หรือไม่
    if (!isset($_SESSION['recipenecklace_users_id'])) {
        throw new Exception("กรุณาเข้าสู่ระบบใหม่ - ไม่พบข้อมูลผู้ใช้");
    }

    // ตรวจสอบว่ามีการส่ง ID มาหรือไม่
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("ไม่พบรหัสข้อมูลที่ต้องการลบ");
    }

    $pdo->beginTransaction();

    $pn_id = $_GET['id'];

    // ดึงข้อมูลรูปภาพก่อนที่จะลบข้อมูล
    $image = get_percent_necklace_image($pdo, $pn_id);

    // ลบไฟล์รูปภาพจาก server (ถ้ามี)
    if (!empty($image)) {
        $image_path = dirname(dirname(__FILE__)) . '/uploads/img/percent_necklace/' . $image;
        if (file_exists($image_path)) {
            if (!unlink($image_path)) {
                error_log("Warning: Could not delete image file: $image_path");
                // ไม่ throw exception เพื่อให้สามารถลบข้อมูลในฐานข้อมูลต่อไปได้
            }
        }
    }

    // 1. ดึงรายการ pnd_id ทั้งหมดที่เกี่ยวข้องกับ pn_id นี้
    $pnd_ids = [];
    try {
        $sql = "SELECT pnd_id FROM percent_necklace_detail WHERE pn_id = :pn_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['pn_id' => $pn_id]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pnd_ids[] = $row['pnd_id'];
        }
    } catch (PDOException $e) {
        throw new Exception("ข้อผิดพลาดในการดึงข้อมูลรายละเอียด: " . $e->getMessage());
    }

    // 2. ลบข้อมูลในตาราง necklace_detail_parts ก่อน (ถ้ามี pnd_id ที่เกี่ยวข้อง)
    if (!empty($pnd_ids)) {
        try {
            $placeholders = implode(',', array_fill(0, count($pnd_ids), '?'));
            $sql = "DELETE FROM necklace_detail_parts WHERE pnd_id IN ($placeholders)";
            $stmt = $pdo->prepare($sql);
            if (!$stmt->execute($pnd_ids)) {
                throw new Exception("ไม่สามารถลบข้อมูลในตาราง necklace_detail_parts ได้: " . implode(" ", $stmt->errorInfo()));
            }
        } catch (PDOException $e) {
            throw new Exception("ข้อผิดพลาดในการลบข้อมูลในตาราง necklace_detail_parts: " . $e->getMessage());
        }
    }

    // 3. ลบข้อมูลในตาราง percent_necklace_detail
    try {
        $sql = "DELETE FROM percent_necklace_detail WHERE pn_id = :pn_id";
        $stmt = $pdo->prepare($sql);
        if (!$stmt->execute(['pn_id' => $pn_id])) {
            throw new Exception("ไม่สามารถลบรายละเอียดได้: " . implode(" ", $stmt->errorInfo()));
        }
    } catch (PDOException $e) {
        throw new Exception("ข้อผิดพลาดในการลบรายละเอียด: " . $e->getMessage());
    }

    // 4. ลบข้อมูลหลักในตาราง percent_necklace
    try {
        if (!delete_percent_necklace($pdo, $pn_id)) {
            throw new Exception("ไม่สามารถลบข้อมูลหลักได้");
        }
    } catch (Exception $e) {
        throw new Exception("ข้อผิดพลาดในการลบข้อมูลหลัก: " . $e->getMessage());
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error in delete_percent.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
