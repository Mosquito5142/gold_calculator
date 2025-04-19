<?php
session_start();
require '../config/db_connect.php';
require '../functions/management_necklace_detail.php';
header('Content-Type: application/json');

try {
    $pdo->beginTransaction();

    $id = $_POST['necklace_detail_id'] ?? null;
    $name = $_POST['name'];

    // ตรวจสอบชื่อซ้ำ
    if (check_duplicate_name($pdo, $name, $id)) {
        throw new Exception("ชื่อลายสร้อยนี้มีอยู่แล้วในระบบ");
    }

    // จัดการรูปภาพ
    $image_name = null;
    if (isset($_FILES['necklace_image']) && $_FILES['necklace_image']['error'] === UPLOAD_ERR_OK) {
        $image_name = handle_image_upload($_FILES['necklace_image']);

        // ถ้าเป็นการแก้ไข ให้ลบรูปเก่า
        if ($id) {
            $old_image = get_necklace_detail_by_id($pdo, $id)['image'];
            if ($old_image) {
                $old_file = dirname(dirname(__FILE__)) . '/uploads/img/necklace_detail/' . $old_image;
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
        }
    }
    // ข้อมูลสร้อย
    $type = $_POST['type'];
    $ptt_thick = $_POST['type'] === 'โปร่ง' ? ($_POST['ptt_thick'] ?? NULL) : NULL;
    $ptt_core = $_POST['type'] === 'โปร่ง' ? ($_POST['ptt_core'] ?? NULL) : NULL;
    $ptt_ratio = $_POST['type'] === 'โปร่ง' ? ($_POST['ptt_ratio'] ?? NULL) : NULL;
    $agpt_thick = $_POST['agpt_thick'] ?? 0;
    $agpt_core = $_POST['agpt_core'] ?? 0;
    $agpt_ratio = $_POST['agpt_ratio'] ?? 0;
    $true_length = $_POST['true_length'] ?? 0;
    $true_weight = isset($_POST['ptt_ratio']) && $_POST['ptt_ratio'] != 0 ?
        ($_POST['weight_ture'] / $_POST['ptt_ratio']) : ($_POST['weight_ture'] ?? 0);
    $comment = $_POST['comment'] ?? NULL;
    $users_id = $_SESSION['recipenecklace_users_id'];

    if (empty($id)) {
        // เพิ่มข้อมูลใหม่
        add_necklace_detail(
            $pdo,
            $name,
            $type,
            $ptt_thick,
            $ptt_core,
            $ptt_ratio,
            $agpt_thick,
            $agpt_core,
            $agpt_ratio,
            $true_length,
            $true_weight,
            $comment,
            $image_name,
            $users_id
        );
        $id = $pdo->lastInsertId();
    } else {
        // อัพเดทข้อมูล
        update_necklace_detail(
            $pdo,
            $id,
            $name,
            $type,
            $ptt_thick,
            $ptt_core,
            $ptt_ratio,
            $agpt_thick,
            $agpt_core,
            $agpt_ratio,
            $true_length,
            $true_weight,
            $comment,
            $image_name,
        );
    }

    // บันทึกข้อมูลสัดส่วน
    if (!empty($_POST['proportions_size']) || !empty($_POST['proportions_width']) || !empty($_POST['proportions_thick'])) {
        $proportions_size = $_POST['proportions_size'] ?? 0;
        $proportions_width = $_POST['proportions_width'] ?? 0;
        $proportions_thick = $_POST['proportions_thick'] ?? 0;
        $shapeshape_necklace = !empty($_POST['shapeshape_necklace']) ? $_POST['shapeshape_necklace'] : null;

        // เรียกใช้ update_necklace_proportions แทน add_necklace_proportions
        update_necklace_proportions($pdo, $id, $proportions_size, $proportions_width, $proportions_thick, $shapeshape_necklace);
    }

    // บันทึกข้อมูล TBS
    $tbs_data = [
        ['name' => 'A', 'before' => 1, 'after' => 1],
        ['name' => 'B', 'before' => 1, 'after' => 1]
    ];

    foreach ($tbs_data as $tbs) {
        add_necklace_tbs($pdo, $id, $tbs['name'], $tbs['before'], $tbs['after']);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
