<?php
session_start();
require '../config/db_connect.php';
require '../functions/management_percent_necklace.php';
header('Content-Type: application/json');

try {
    if (!isset($_SESSION['recipenecklace_users_id'])) {
        throw new Exception("กรุณาเข้าสู่ระบบใหม่");
    }

    if (!isset($_GET['id'])) {
        throw new Exception("ไม่พบรหัสรายการ");
    }

    $pn_id = $_GET['id'];

    // ดึงข้อมูลพื้นฐาน รวมถึงรูปภาพด้วย
    $sql = "SELECT pn_id, pn_name, pn_grams, image FROM percent_necklace WHERE pn_id = :pn_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['pn_id' => $pn_id]);
    $percent = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$percent) {
        throw new Exception("ไม่พบข้อมูลรายการนี้");
    }

    // ดึงข้อมูลรายละเอียดทั้งหมด
    $details = get_percent_necklace_detail($pdo, $pn_id);

    // ดึงข้อมูล necklace_detail_parts สำหรับแต่ละรายการ
    foreach ($details as &$detail) {
        $parts = get_necklace_detail_parts($pdo, $detail['pnd_id']);
        if ($parts) {
            $detail['parts'] = $parts;
        }
    }
    unset($detail); // ป้องกันการอ้างอิงถึงตัวแปรที่ไม่ได้ใช้

    // แยกรายละเอียดพิเศษออกมา (เผื่อตัดลาย และตะขอ)
    $specialDetails = array_filter($details, function ($detail) {
        return $detail['pnd_name'] === 'เผื่อตัดลาย' || $detail['pnd_name'] === 'ตะขอ';
    });

    // กรองรายละเอียดทั่วไป (ไม่รวมรายการพิเศษ)
    $normalDetails = array_filter($details, function ($detail) {
        return $detail['pnd_name'] !== 'เผื่อตัดลาย' && $detail['pnd_name'] !== 'ตะขอ';
    });

    echo json_encode([
        'success' => true,
        'percent' => $percent,
        'details' => array_values($normalDetails),
        'specialDetails' => array_values($specialDetails)
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
