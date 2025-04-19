<?php
session_start();
require '../functions/check_login.php';
require '../config/db_connect.php';
require '../functions/management_percent_necklace.php';

$response = ['success' => false, 'message' => ''];

try {
    // Check for required fields
    if (empty($_POST['pn_name']) || empty($_POST['original_pn_id'])) {
        throw new Exception('กรุณากรอกข้อมูลให้ครบถ้วน');
    }

    // Start transaction
    $pdo->beginTransaction();

    // Get original data
    $original_id = $_POST['original_pn_id'];
    $stmt = $pdo->prepare("SELECT * FROM percent_necklace WHERE pn_id = :id");
    $stmt->execute(['id' => $original_id]);
    $original = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$original) {
        throw new Exception('ไม่พบข้อมูลต้นฉบับ');
    }

    // ใช้ค่าน้ำหนักที่ผู้ใช้กรอกแทนค่าเดิม
    $grams = !empty($_POST['pn_grams']) ? $_POST['pn_grams'] : $original['pn_grams'];

    // Create new record with new name and set status to 'copy'
    $sql = "INSERT INTO percent_necklace (pn_name, pn_grams, users_id, updated_at, pn_status) 
            VALUES (:name, :grams, :user_id, NOW(), 'copy')";

    $stmt = $pdo->prepare($sql);

    // หาค่า session ที่ถูกต้องสำหรับ user id
    $userId = null;
    if (isset($_SESSION['recipenecklace_users_id'])) {
        $userId = $_SESSION['recipenecklace_users_id'];
    } elseif (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    } elseif (isset($_SESSION['users_id'])) {
        $userId = $_SESSION['users_id'];
    } else {
        // ถ้าไม่มี session ให้ใช้ค่าจากข้อมูลต้นฉบับ
        $userId = $original['users_id'];
    }

    $stmt->execute([
        'name' => $_POST['pn_name'] . ' (สำเนาของ ' . $original['pn_name'] . ')',
        'grams' => $grams,
        'user_id' => $userId
    ]);

    $new_id = $pdo->lastInsertId();

    // เก็บค่าความกว้างสร้อยที่ผู้ใช้กรอก (ถ้ามี)
    $custom_scale_wire_weight = !empty($_POST['custom_scale_wire_weight']) ? floatval($_POST['custom_scale_wire_weight']) : 0;

    // เก็บค่า reference scale_wire_weight เดิม
    $reference_scale = 0;
    $details = get_percent_necklace_detail($pdo, $original_id);
    $total_weight_original = 0;

    // คำนวณน้ำหนักรวมของข้อมูลเดิมและหาค่าความกว้างอ้างอิง
    foreach ($details as $detail) {
        $total_weight_original += floatval($detail['pnd_weight_grams']);
        if ($detail['pnd_type'] === 'สร้อย' && $reference_scale == 0) {
            $reference_scale = floatval($detail['scale_wire_weight']);
        }
    }

    // ถ้ามีน้ำหนักเดิมแล้วและมีการกรอกน้ำหนักใหม่ จะคำนวณตามสัดส่วน
    $weight_ratio = ($total_weight_original > 0 && $grams > 0) ? $grams / $total_weight_original : 1;

    // สร้าง array เก็บข้อมูล pnd_id -> อัตราส่วนของอะไหล่เทียบกับความกว้างสร้อยเดิม
    $part_ratios = [];
    
    // คำนวณอัตราส่วนของอะไหล่เทียบกับความกว้างสร้อยเดิม
    if ($reference_scale > 0) {
        foreach ($details as $detail) {
            if ($detail['pnd_type'] === 'อะไหล่') {
                $part_ratios[$detail['pnd_id']] = [
                    'width_ratio' => floatval($detail['parts_weight']) / $reference_scale,
                    'height_ratio' => floatval($detail['parts_height']) / $reference_scale,
                    'thick_ratio' => floatval($detail['parts_thick']) / $reference_scale
                ];
            }
        }
    }

    foreach ($details as $detail) {
        // คำนวณน้ำหนักใหม่ตามสัดส่วนของน้ำหนักรวมที่ผู้ใช้กรอก
        $new_weight = floatval($detail['pnd_weight_grams']) * $weight_ratio;

        // Insert detail record with new weight
        $stmt = $pdo->prepare("INSERT INTO percent_necklace_detail 
                              (pn_id, pnd_type, pnd_name, pnd_weight_grams, pnd_long_inch) 
                              VALUES (:pn_id, :type, :name, :weight, :long)");

        $stmt->execute([
            'pn_id' => $new_id,
            'type' => $detail['pnd_type'],
            'name' => $detail['pnd_name'],
            'weight' => $new_weight,
            'long' => $detail['pnd_long_inch']
        ]);

        $new_detail_id = $pdo->lastInsertId();

        // Copy parts data with new calculations if exists
        if (isset($detail['ndp_id']) && !empty($detail['ndp_id'])) {
            $stmt = $pdo->prepare("SELECT * FROM necklace_detail_parts WHERE ndp_id = :id");
            $stmt->execute(['id' => $detail['ndp_id']]);
            $parts = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($parts) {
                // กำหนดค่าตั้งต้นสำหรับรายการส่วนประกอบ
                $wire_hole = $parts['wire_hole'];
                $wire_thick = $parts['wire_thick'];
                $wire_core = $parts['wire_core'];
                $scale_wire_weight = $parts['scale_wire_weight'];
                $scale_wire_thick = $parts['scale_wire_thick'];
                $parts_weight = $parts['parts_weight'];
                $parts_height = $parts['parts_height'];
                $parts_thick = $parts['parts_thick'];

                // ถ้าเป็นสร้อยและมีการกรอกความกว้างใหม่
                if ($detail['pnd_type'] === 'สร้อย' && $custom_scale_wire_weight > 0) {
                    $scale_wire_weight = $custom_scale_wire_weight;
                }
                // ถ้าเป็นอะไหล่และมีการกรอกความกว้างใหม่ - คำนวณค่าจากอัตราส่วน
                else if ($detail['pnd_type'] === 'อะไหล่' && $custom_scale_wire_weight > 0 && isset($part_ratios[$detail['pnd_id']])) {
                    $ratios = $part_ratios[$detail['pnd_id']];
                    $parts_weight = $ratios['width_ratio'] * $custom_scale_wire_weight;
                    $parts_height = $ratios['height_ratio'] * $custom_scale_wire_weight;
                    $parts_thick = $ratios['thick_ratio'] * $custom_scale_wire_weight;
                }

                $stmt = $pdo->prepare("INSERT INTO necklace_detail_parts 
                    (pnd_id, wire_hole, wire_thick, wire_core, scale_wire_weight, scale_wire_thick, 
                    parts_weight, parts_height, parts_thick) 
                    VALUES (:pnd_id, :wire_hole, :wire_thick, :wire_core, :scale_wire_weight, 
                    :scale_wire_thick, :parts_weight, :parts_height, :parts_thick)");

                $stmt->execute([
                    'pnd_id' => $new_detail_id,
                    'wire_hole' => $wire_hole,
                    'wire_thick' => $wire_thick,
                    'wire_core' => $wire_core,
                    'scale_wire_weight' => $scale_wire_weight,
                    'scale_wire_thick' => $scale_wire_thick,
                    'parts_weight' => $parts_weight,
                    'parts_height' => $parts_height,
                    'parts_thick' => $parts_thick
                ]);
            }
        }
    }

    // Commit transaction
    $pdo->commit();

    $response = [
        'success' => true,
        'message' => 'บันทึกสำเนาเรียบร้อยแล้ว',
        'new_id' => $new_id
    ];
} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $response = ['success' => false, 'message' => $e->getMessage()];
} catch (PDOException $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $response = ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;