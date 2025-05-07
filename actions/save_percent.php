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

    // รับข้อมูลพื้นฐาน
    $pn_id = $_POST['pn_id'] ?? null;
    $pn_name = trim($_POST['pn_name']); // ใช้ trim() เพื่อตัดช่องว่างหน้า-หลัง
    $pn_grams = $_POST['pn_grams'];
    $pn_status = $_POST['pn_status'] ?? 'master';
    $users_id = $_SESSION['recipenecklace_users_id'];

    // ตรวจสอบว่าชื่อไม่เป็นค่าว่าง
    if (empty($pn_name)) {
        throw new Exception("กรุณาระบุชื่อสัดส่วน%สร้อย");
    }

    // เพิ่มการตรวจสอบชื่อซ้ำกับรายการอื่น
    if (empty($pn_id)) {
        // กรณีเพิ่มใหม่ - ต้องไม่ซ้ำกับรายการใดๆ
        $sql_check = "SELECT COUNT(*) FROM percent_necklace WHERE LOWER(pn_name) = LOWER(:pn_name)";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute(['pn_name' => $pn_name]);
        
        if ($stmt_check->fetchColumn() > 0) {
            throw new Exception("ชื่อซ้ำกับที่มีอยู่แล้ว: '{$pn_name}' กรุณาใช้ชื่ออื่น");
        }
    } else {
        // กรณีแก้ไข - ต้องไม่ซ้ำกับรายการอื่นที่ไม่ใช่รายการนี้
        $sql_check = "SELECT COUNT(*) FROM percent_necklace WHERE LOWER(pn_name) = LOWER(:pn_name) AND pn_id != :pn_id";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute(['pn_name' => $pn_name, 'pn_id' => $pn_id]);
        
        if ($stmt_check->fetchColumn() > 0) {
            throw new Exception("ชื่อซ้ำกับที่มีอยู่แล้ว: '{$pn_name}' กรุณาใช้ชื่ออื่น");
        }
    }

    // เพิ่ม debug info
    error_log("Processing data: ID=$pn_id, Name=$pn_name, Grams=$pn_grams, User=$users_id");

    $pdo->beginTransaction();

    // จัดการอัปโหลดรูปภาพ
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        try {
            $image = handle_image_upload($_FILES['image']);
            error_log("Image uploaded successfully: $image");
        } catch (Exception $e) {
            error_log("Image upload error: " . $e->getMessage());
            throw new Exception("ไม่สามารถอัปโหลดรูปภาพได้: " . $e->getMessage());
        }
    }

    // ถ้ามี ID แสดงว่าเป็นการอัพเดท
    if (!empty($pn_id)) {
        // ถ้ามีการอัปโหลดรูปใหม่และมีรูปเก่าอยู่แล้ว ให้ลบรูปเก่า
        if ($image !== null) {
            $old_image = get_percent_necklace_image($pdo, $pn_id);
            if (!empty($old_image)) {
                $old_image_path = dirname(dirname(__FILE__)) . '/uploads/img/percent_necklace/' . $old_image;
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
        }

        // อัพเดทข้อมูล
        try {
            if (!update_percent_necklace($pdo, $pn_id, $pn_name, $pn_grams, $image)) {
                throw new Exception("ไม่สามารถอัพเดทข้อมูลได้ - อาจมีปัญหากับการบันทึกลงฐานข้อมูล");
            }
        } catch (Exception $e) {
            throw new Exception("ข้อผิดพลาดในการอัพเดทข้อมูล: " . $e->getMessage());
        }

        // *** แก้ไขส่วนนี้ - ดึง pnd_id ทั้งหมดของ pn_id นี้ก่อนลบ ***
        $pnd_ids = get_all_pnd_ids_by_pn_id($pdo, $pn_id);

        // ลบข้อมูล necklace_detail_parts ที่เกี่ยวข้องกับ pnd_id ทั้งหมด
        if (!empty($pnd_ids)) {
            try {
                $placeholders = implode(',', array_fill(0, count($pnd_ids), '?'));
                $sql = "DELETE FROM necklace_detail_parts WHERE pnd_id IN ($placeholders)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($pnd_ids);
                error_log("Deleted related necklace_detail_parts records: " . implode(', ', $pnd_ids));
            } catch (PDOException $e) {
                throw new Exception("ข้อผิดพลาดในการลบข้อมูล necklace_detail_parts: " . $e->getMessage());
            }
        }

        // ลบข้อมูลรายละเอียดเก่าก่อนเพิ่มใหม่
        try {
            $sql = "DELETE FROM percent_necklace_detail WHERE pn_id = :pn_id";
            $stmt = $pdo->prepare($sql);
            if (!$stmt->execute(['pn_id' => $pn_id])) {
                throw new Exception("ไม่สามารถลบรายละเอียดเดิมได้: " . implode(" ", $stmt->errorInfo()));
            }
        } catch (PDOException $e) {
            throw new Exception("ข้อผิดพลาดในการลบรายละเอียดเดิม: " . $e->getMessage());
        }
    } else {
        // เพิ่มข้อมูลใหม่
        try {
            // เพิ่มข้อมูลใหม่ เราได้ตรวจสอบชื่อซ้ำไปแล้วด้านบน
            if (!add_percent_necklace($pdo, $pn_name, $pn_grams, $users_id, $image, $pn_status)) {
                throw new Exception("ไม่สามารถเพิ่มข้อมูลได้: " . implode(" ", $pdo->errorInfo()));
            }
            $pn_id = $pdo->lastInsertId();
            if (empty($pn_id)) {
                throw new Exception("ไม่สามารถเพิ่มข้อมูลได้ - ไม่ได้รับ ID ที่เพิ่มใหม่");
            }
        } catch (PDOException $e) {
            throw new Exception("ข้อผิดพลาดในการเพิ่มข้อมูล: " . $e->getMessage());
        }
    }

    // บันทึกข้อมูลรายการพิเศษ (เผื่อตัดลาย และ ตะขอ)
    if (isset($_POST['pnd_name_special']) && is_array($_POST['pnd_name_special'])) {
        foreach ($_POST['pnd_name_special'] as $i => $name) {
            // ตรวจสอบชื่อที่ว่างเปล่า
            if (trim($name) === '') {
                continue; // ข้ามรายการที่ไม่มีชื่อ
            }
            
            try {
                $weight = $_POST['pnd_weight_special'][$i];
                $longInch = isset($_POST['pnd_long_special'][$i]) && $_POST['pnd_long_special'][$i] !== '' ?
                    $_POST['pnd_long_special'][$i] : null;

                // ส่งค่าว่างสำหรับประเภท เนื่องจากเป็นรายการพิเศษ
                $type = '';

                $result = add_percent_necklace_detail(
                    $pdo,
                    $pn_id,
                    $type,
                    $name,
                    $weight,
                    $longInch
                );

                if (!$result) {
                    throw new Exception("ไม่สามารถเพิ่มข้อมูลรายการพิเศษได้ รายการ: " . $name);
                }
            } catch (Exception $e) {
                throw new Exception("ข้อผิดพลาดในการเพิ่มรายการพิเศษ " . $name . ": " . $e->getMessage());
            }
        }
    }

    // ตรวจสอบชื่อซ้ำในรายการย่อยเดียวกัน
    $detailNames = [];

    // บันทึกข้อมูลรายละเอียด
    if (isset($_POST['pnd_type']) && is_array($_POST['pnd_type'])) {
        foreach ($_POST['pnd_type'] as $i => $type) {
            $pnd_name = trim($_POST['pnd_name'][$i]);
            
            // ตรวจสอบชื่อที่ว่างเปล่า
            if ($pnd_name === '') {
                continue; // ข้ามรายการที่ไม่มีชื่อ
            }
            
            // ตรวจสอบชื่อซ้ำในชุดข้อมูลเดียวกัน
            $nameKey = strtolower($pnd_name);
            if (in_array($nameKey, $detailNames)) {
                throw new Exception("พบชื่อซ้ำในรายการย่อย: '{$pnd_name}' กรุณาตรวจสอบและแก้ไขข้อมูล");
            }
            
            // เพิ่มชื่อลงในอาร์เรย์ตรวจสอบ
            $detailNames[] = $nameKey;
            
            try {
                // แปลงค่าว่างเป็น null
                $pnd_long_inch = isset($_POST['pnd_long_inch'][$i]) && $_POST['pnd_long_inch'][$i] !== '' ?
                    $_POST['pnd_long_inch'][$i] : null;

                $result = add_percent_necklace_detail(
                    $pdo,
                    $pn_id,
                    $type,
                    $pnd_name,
                    $_POST['pnd_weight_grams'][$i],
                    $pnd_long_inch
                );

                if (!$result) {
                    throw new Exception("ไม่สามารถเพิ่มข้อมูลรายละเอียดได้รายการที่ " . ($i + 1));
                }

                // รับ pnd_id ของรายการที่เพิ่มล่าสุด
                $pnd_id = $pdo->lastInsertId();

                // กำหนดค่าตามประเภท
                $wire_hole = null;
                $wire_thick = null;
                $wire_core = null;
                $scale_wire_weight = null;
                $scale_wire_thick = null;
                $parts_weight = null;
                $parts_height = null;
                $parts_thick = null;

                if ($type === 'สร้อย' || $type === 'กำไล' || $type === 'มัลติ') {
                    $wire_hole = $_POST['wire_hole'][$i] ?? null;
                    $wire_thick = $_POST['wire_thick'][$i] ?? null;
                    $wire_core = $_POST['wire_core'][$i] ?? null;
                    $scale_wire_weight = $_POST['scale_wire_weight'][$i] ?? null;
                    $scale_wire_thick = $_POST['scale_wire_thick'][$i] ?? null;
                } else if ($type === 'อะไหล่') {
                    $parts_weight = $_POST['parts_weight'][$i] ?? null;
                    $parts_height = $_POST['parts_height'][$i] ?? null;
                    $parts_thick = $_POST['parts_thick'][$i] ?? null;
                }

                // ตรวจสอบว่ามีข้อมูลให้บันทึกหรือไม่
                if (($type === 'สร้อย' || $type === 'กำไล' || $type === 'มัลติ') && ($wire_hole !== null || $wire_thick !== null || $wire_core !== null || $scale_wire_weight !== null || $scale_wire_thick !== null)) {
                    // เพิ่มข้อมูลใหม่ (ไม่ต้องสนใจ ndp_id เก่า เพราะเราลบหมดแล้ว)
                    add_necklace_detail_parts(
                        $pdo,
                        $pnd_id,
                        $wire_hole,
                        $wire_thick,
                        $wire_core,
                        $scale_wire_weight,
                        $scale_wire_thick,
                        null, // ไม่ใช้ parts_weight
                        null, // ไม่ใช้ parts_height
                        null  // ไม่ใช้ parts_thick
                    );
                } elseif ($type === 'อะไหล่' && ($parts_weight !== null || $parts_height !== null || $parts_thick !== null)) {
                    // เพิ่มข้อมูลใหม่
                    add_necklace_detail_parts(
                        $pdo,
                        $pnd_id,
                        null, // ไม่ใช้ wire_hole
                        null, // ไม่ใช้ wire_thick
                        null, // ไม่ใช้ wire_core
                        null, // ไม่ใช้ scale_wire_weight
                        null, // ไม่ใช้ scale_wire_thick
                        $parts_weight,
                        $parts_height,
                        $parts_thick
                    );
                }
            } catch (Exception $e) {
                throw new Exception("ข้อผิดพลาดในการเพิ่มรายละเอียดรายการที่ " . ($i + 1) . ": " . $e->getMessage());
            }
        }
    } else if (!isset($_POST['pnd_name_special']) || count(array_filter($_POST['pnd_name_special'], 'trim')) == 0) {
        throw new Exception("ไม่พบข้อมูลรายละเอียด กรุณาเพิ่มรายการอย่างน้อย 1 รายการ");
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error in save_percent.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}