<?php
session_start();
require '../config/db_connect.php'; // เชื่อมต่อกับฐานข้อมูล
require '../functions/management_ratio.php'; // นำเข้าฟังก์ชันจัดการอัตราส่วน

// ตรวจสอบว่ามีการส่งคำขอแบบ POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            // เพิ่มข้อมูลอัตราส่วน
            $ratio_thick = $_POST['ratio_thick'] ?? '';
            $ratio_data = $_POST['ratio_data'] ?? '';
            $ratio_size = $_POST['ratio_size'] ?? '';
            $ratio_gram = $_POST['ratio_gram'] ?? '';
            $ratio_inch = $_POST['ratio_inch'] ?? '';
            $updated_users_id = $_SESSION['recipenecklace_users_id'] ?? NULL;

            if (add_ratio($pdo, $ratio_thick, $ratio_data, $ratio_size, $ratio_gram, $ratio_inch, $updated_users_id)) {
                echo json_encode(['status' => 'success', 'message' => 'เพิ่มอัตราส่วนสำเร็จ']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถเพิ่มอัตราส่วนได้']);
            }
            break;

        case 'edit':
            // แก้ไขข้อมูลอัตราส่วน
            $ratio_id = $_POST['ratio_id'] ?? '';
            $ratio_thick = $_POST['ratio_thick'] ?? '';
            $ratio_data = $_POST['ratio_data'] ?? '';
            $ratio_size = $_POST['ratio_size'] ?? '';
            $ratio_gram = $_POST['ratio_gram'] ?? '';
            $ratio_inch = $_POST['ratio_inch'] ?? '';

            if (update_ratio($pdo, $ratio_id, $ratio_thick, $ratio_data, $ratio_size, $ratio_gram, $ratio_inch)) {
                echo json_encode(['status' => 'success', 'message' => 'แก้ไขอัตราส่วนสำเร็จ']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถแก้ไขอัตราส่วนได้']);
            }
            break;

        case 'delete':
            // ลบข้อมูลอัตราส่วน
            $ratio_id = $_POST['ratio_id'] ?? '';

            if (delete_ratio($pdo, $ratio_id)) {
                echo json_encode(['status' => 'success', 'message' => 'ลบอัตราส่วนสำเร็จ']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถลบอัตราส่วนได้']);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'คำขอไม่ถูกต้อง']);
            break;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'อนุญาตเฉพาะคำขอแบบ POST เท่านั้น']);
}
