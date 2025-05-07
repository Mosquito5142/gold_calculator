<?php
session_start();
require '../config/db_connect.php';
require '../functions/management_necklace_detail.php';
header('Content-Type: application/json');

try {
    // ตรวจสอบว่ามีข้อมูลที่จำเป็นครบถ้วนหรือไม่
    if (empty($_POST['original_id']) || empty($_POST['name'])) {
        throw new Exception("กรุณาระบุข้อมูลที่จำเป็นให้ครบถ้วน");
    }

    $pdo->beginTransaction();

    // ข้อมูลต้นฉบับ
    $original_id = $_POST['original_id'];
    $new_name = $_POST['name'];
    $weight = isset($_POST['weight']) ? floatval($_POST['weight']) : 0;
    $length = isset($_POST['length']) ? floatval($_POST['length']) : 0;
    $gold_type = isset($_POST['gold_type']) ? $_POST['gold_type'] : '';
    $ratio_id = isset($_POST['ratio_id']) ? $_POST['ratio_id'] : '';
    $users_id = $_SESSION['recipenecklace_users_id'];

    // ตรวจสอบชื่อซ้ำ
    if (check_duplicate_name($pdo, $new_name, null)) {
        throw new Exception("ชื่อลายสร้อยนี้มีอยู่แล้วในระบบ");
    }

    // ดึงข้อมูลต้นฉบับ
    $original = get_necklace_detail_by_id($pdo, $original_id);
    if (!$original) {
        throw new Exception("ไม่พบข้อมูลต้นฉบับ");
    }

    // เตรียมข้อมูลสำหรับบันทึก
    $type = $original['type'];
    $ptt_thick = $original['ptt_thick'];
    $ptt_core = $original['ptt_core'];
    $ptt_ratio = $original['ptt_ratio'];
    $agpt_thick = $original['agpt_thick'];
    $agpt_core = $original['agpt_core'];
    $agpt_ratio = $original['agpt_ratio'];
    $true_length = $original['true_length'];
    $true_weight = $original['true_weight'];
    $comment = isset($_POST['comment']) ? $_POST['comment'] : ''; // รับค่าหมายเหตุที่ส่งมาจากฟอร์ม

    // ดึงข้อมูลความหนาจาก ratio ถ้ามีการส่ง ratio_id มา
    $ratio_thick = null;
    $ratio_thick_copy = null; // สำหรับเก็บลงใน necklace_ratio_copy
    $ratio_data = null;
    $ratio_size = null;
    $ratio_gram = null;
    $ratio_inch = null;
    $new_ratio_id = null; // ตัวแปรสำหรับเก็บ ID ใหม่ของสำเนา ratio

    if (!empty($ratio_id)) {
        $stmt = $pdo->prepare("SELECT * FROM ratio_data WHERE ratio_id = :ratio_id");
        $stmt->execute(['ratio_id' => $ratio_id]);
        $ratio_data_record = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($ratio_data_record) {
            // ตรวจสอบโครงสร้างตาราง ratio_data เพื่อดูว่า ratio_thick เป็น decimal หรือไม่
            $stmt = $pdo->prepare("DESCRIBE ratio_data ratio_thick");
            $stmt->execute();
            $column_desc = $stmt->fetch(PDO::FETCH_ASSOC);
            $is_decimal = false;
            if ($column_desc && strpos($column_desc['Type'], 'decimal') !== false) {
                $is_decimal = true;
            }

            $original_thick = $ratio_data_record['ratio_thick'];

            // ค่า ratio_thick สำหรับ necklace_ratio_copy (เก็บเป็น varchar ได้)
            if (preg_match('/([0-9]*\.?[0-9]+)/', $original_thick, $matches)) {
                $number_only = $matches[1];
                $ratio_thick_copy = "(สำเนา) " . $number_only;
            } else {
                $ratio_thick_copy = "(สำเนา) " . $original_thick;
            }

            // ค่า ratio_thick สำหรับ ratio_data (ต้องเป็นตัวเลขล้วนถ้าเป็น decimal)
            if ($is_decimal) {
                // ถ้าเป็น decimal ให้เก็บเฉพาะตัวเลข
                if (preg_match('/([0-9]*\.?[0-9]+)/', $original_thick, $matches)) {
                    $ratio_thick = $matches[1]; // เก็บเฉพาะตัวเลข
                } else {
                    $ratio_thick = "0"; // กำหนดค่าเริ่มต้นถ้าไม่พบตัวเลข
                }
            } else {
                // ถ้าไม่ใช่ decimal ให้เก็บค่าที่มี (สำเนา) ได้
                $ratio_thick = $ratio_thick_copy;
            }

            // เก็บค่าอื่นๆ เป็นตัวเลข
            $ratio_data = floatval($ratio_data_record['ratio_data'] ?? 0);
            $ratio_size = floatval($ratio_data_record['ratio_size'] ?? 0);
            $ratio_gram = floatval($ratio_data_record['ratio_gram'] ?? 0);
            $ratio_inch = floatval($ratio_data_record['ratio_inch'] ?? 0);

            // ทำสำเนาข้อมูล ratio และบันทึกเป็นรายการใหม่ในตาราง ratio_data
            try {
                $stmt = $pdo->prepare("INSERT INTO ratio_data 
                                      (ratio_thick, ratio_data, ratio_size, ratio_gram, ratio_inch, ratio_status , updated_users_id, updated_at) 
                                      VALUES 
                                      (:ratio_thick, :ratio_data, :ratio_size, :ratio_gram, :ratio_inch, :ratio_status , :updated_users_id, NOW())");
                $stmt->execute([
                    'ratio_thick' => $ratio_thick,
                    'ratio_data' => $ratio_data,
                    'ratio_size' => $ratio_size,
                    'ratio_gram' => $ratio_gram,
                    'ratio_inch' => $ratio_inch,
                    'ratio_status' => 'copy',
                    'updated_users_id' => $users_id
                ]);

                // เก็บ ID ใหม่ของ ratio ที่เพิ่งสร้าง
                $new_ratio_id = $pdo->lastInsertId();
            } catch (PDOException $e) {
                error_log("Error saving ratio data: " . $e->getMessage());
                throw new Exception("เกิดข้อผิดพลาดในการบันทึกข้อมูลอัตราส่วน: " . $e->getMessage());
            }
        }
    }

    // บันทึกข้อมูล
    $stmt = $pdo->prepare("INSERT INTO necklace_detail 
                          (name, type, ptt_thick, ptt_core, ptt_ratio, 
                           agpt_thick, agpt_core, agpt_ratio, 
                           true_length, true_weight, comment, 
                           pd_status, updated_users_id, updated_at) 
                          VALUES 
                          (:name, :type, :ptt_thick, :ptt_core, :ptt_ratio, 
                           :agpt_thick, :agpt_core, :agpt_ratio, 
                           :true_length, :true_weight, :comment, 
                           'copy', :users_id, NOW())");

    $stmt->execute([
        'name' => $new_name,
        'type' => $type,
        'ptt_thick' => $ptt_thick,
        'ptt_core' => $ptt_core,
        'ptt_ratio' => $ptt_ratio,
        'agpt_thick' => $agpt_thick,
        'agpt_core' => $agpt_core,
        'agpt_ratio' => $agpt_ratio,
        'true_length' => $true_length,
        'true_weight' => $true_weight,
        'comment' => $comment,
        'users_id' => $users_id,
    ]);

    $new_id = $pdo->lastInsertId();

    // คัดลอกข้อมูลสัดส่วน
    $stmt = $pdo->prepare("SELECT * FROM necklace_proportions WHERE necklace_detail_id = :id");
    $stmt->execute(['id' => $original_id]);
    $proportions = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($proportions) {
        add_necklace_proportions(
            $pdo,
            $new_id,
            $proportions['proportions_size'],
            $proportions['proportions_width'],
            $proportions['proportions_thick'],
            $proportions['shapeshape_necklace']
        );
    }

    // สร้างข้อมูล TBS ใหม่แทนการคัดลอกทั้งหมด
    $tbs_data = [
        ['name' => 'A', 'before' => 1, 'after' => 1],
        ['name' => 'B', 'before' => 1, 'after' => 1]
    ];

    foreach ($tbs_data as $tbs) {
        add_necklace_tbs($pdo, $new_id, $tbs['name'], $tbs['before'], $tbs['after']);
    }

    // บันทึกข้อมูลการคำนวณ โดยใช้ new_ratio_id (ถ้ามี)
    $stmt = $pdo->prepare("INSERT INTO necklace_calculation 
                          (necklace_detail_id, weight, length, gold_type, ratio_id, created_at) 
                          VALUES 
                          (:necklace_id, :weight, :length, :gold_type, :ratio_id, NOW())");

    $stmt->execute([
        'necklace_id' => $new_id,
        'weight' => $weight,
        'length' => $length,
        'gold_type' => $gold_type,
        'ratio_id' => $new_ratio_id ? $new_ratio_id : $ratio_id // ใช้ ID ใหม่ถ้ามี หรือเดิมถ้าไม่มี
    ]);

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'บันทึกเป็นรายการใหม่เรียบร้อยแล้ว', 'new_id' => $new_id]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
