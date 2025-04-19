<?php
function get_percent_necklace($pdo)
{
    $sql = "SELECT pn.pn_id, pn.pn_name, pn.pn_grams, pn.image, u.first_name, pn.users_id, updated_at 
        FROM percent_necklace pn 
        LEFT JOIN users u ON pn.users_id = u.users_id
        WHERE pn.pn_status = 'master'
        ORDER BY pn.pn_name DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function get_copy_percent_necklace($pdo)
{
    $sql = "SELECT pn.pn_id, pn.pn_name, pn.pn_grams, pn.image, u.first_name, pn.users_id, updated_at 
        FROM percent_necklace pn 
        LEFT JOIN users u ON pn.users_id = u.users_id
        WHERE pn.pn_status = 'copy'
        ORDER BY pn.pn_name DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function get_percent_necklace_detail($pdo, $pn_id)
{
    $sql = "SELECT pnd.pnd_id, pnd.pn_id, pnd.pnd_type, pnd.pnd_name, pnd.pnd_weight_grams, pnd.pnd_long_inch, 
                   ndp.ndp_id, ndp.wire_hole, ndp.wire_thick, ndp.wire_core, ndp.scale_wire_weight, 
                   ndp.scale_wire_thick, ndp.parts_weight, ndp.parts_height, ndp.parts_thick
            FROM percent_necklace_detail pnd
            LEFT JOIN necklace_detail_parts ndp ON pnd.pnd_id = ndp.pnd_id
            WHERE pnd.pn_id = :pn_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['pn_id' => $pn_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function add_percent_necklace($pdo, $pn_name, $pn_grams, $users_id, $image = null, $pn_status = 'master')
{
    $sql = "INSERT INTO percent_necklace (pn_name, pn_grams, users_id, image, pn_status) VALUES (:pn_name, :pn_grams, :users_id, :image, :pn_status)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'pn_name' => $pn_name,
        'pn_grams' => $pn_grams,
        'users_id' => $users_id,
        'image' => $image,
        'pn_status' => $pn_status
    ]);
}
function update_percent_necklace($pdo, $pn_id, $pn_name, $pn_grams, $image = null)
{
    try {
        // ถ้าไม่มีการเปลี่ยนรูปภาพ จะไม่อัพเดทฟิลด์ image
        if ($image === null) {
            $sql = "UPDATE percent_necklace 
                    SET pn_name = :pn_name, 
                        pn_grams = :pn_grams
                    WHERE pn_id = :pn_id";
            $params = [
                'pn_id' => $pn_id,
                'pn_name' => $pn_name,
                'pn_grams' => $pn_grams
            ];
        } else {
            $sql = "UPDATE percent_necklace 
                    SET pn_name = :pn_name, 
                        pn_grams = :pn_grams, 
                        image = :image
                    WHERE pn_id = :pn_id";
            $params = [
                'pn_id' => $pn_id,
                'pn_name' => $pn_name,
                'pn_grams' => $pn_grams,
                'image' => $image
            ];
        }

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($params);

        if (!$result) {
            throw new Exception("Database error: " . implode(" ", $stmt->errorInfo()));
        }

        return $result;
    } catch (PDOException $e) {
        throw new Exception("Database error: " . $e->getMessage());
    }
}
function delete_percent_necklace($pdo, $pn_id)
{
    $sql = "DELETE FROM percent_necklace WHERE pn_id = :pn_id";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute(['pn_id' => $pn_id]);

    if (!$result) {
        $error = $stmt->errorInfo();
        error_log("Error in delete_percent_necklace: " . implode(" ", $error));
    }

    return $result;
}
function add_percent_necklace_detail($pdo, $pn_id, $pnd_type, $pnd_name, $pnd_weight_grams, $pnd_long_inch)
{
    // แปลงค่าว่างเป็น null
    if ($pnd_long_inch === '') {
        $pnd_long_inch = null;
    }

    $sql = "INSERT INTO percent_necklace_detail (pn_id, pnd_type, pnd_name, pnd_weight_grams, pnd_long_inch) VALUES (:pn_id, :pnd_type, :pnd_name, :pnd_weight_grams, :pnd_long_inch)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        'pn_id' => $pn_id,
        'pnd_type' => $pnd_type,
        'pnd_name' => $pnd_name,
        'pnd_weight_grams' => $pnd_weight_grams,
        'pnd_long_inch' => $pnd_long_inch
    ]);

    if (!$result) {
        $error = $stmt->errorInfo();
        error_log("Error in add_percent_necklace_detail: " . implode(" ", $error));
    }

    return $result;
}
function update_percent_necklace_detail($pdo, $pnd_id, $pn_id, $pnd_type, $pnd_name, $pnd_weight_grams, $pnd_long_inch)
{
    $sql = "UPDATE percent_necklace_detail SET pn_id = :pn_id, pnd_type = :pnd_type, pnd_name = :pnd_name, pnd_weight_grams = :pnd_weight_grams, pnd_long_inch = :pnd_long_inch WHERE pnd_id = :pnd_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['pnd_id' => $pnd_id, 'pn_id' => $pn_id, 'pnd_type' => $pnd_type, 'pnd_name' => $pnd_name, 'pnd_weight_grams' => $pnd_weight_grams, 'pnd_long_inch' => $pnd_long_inch]);
}
function delete_percent_necklace_detail($pdo, $pnd_id)
{
    $sql = "DELETE FROM percent_necklace_detail WHERE pnd_id = :pnd_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['pnd_id' => $pnd_id]);
}
function check_duplicate_name($pdo, $name)
{
    $sql = "SELECT COUNT(*) FROM necklace_detail WHERE name = :name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['name' => $name]);
    return $stmt->fetchColumn() > 0;
}
function get_necklace_detail($pdo, $necklace_detail_id)
{
    $sql = "SELECT * FROM necklace_detail WHERE necklace_detail_id = :necklace_detail_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['necklace_detail_id' => $necklace_detail_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function handle_image_upload($file)
{
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return null;
    }

    // กำหนด path ให้ถูกต้อง
    $upload_path = dirname(dirname(__FILE__)) . '/uploads/img/percent_necklace/';
    if (!file_exists($upload_path)) {
        mkdir($upload_path, 0777, true);
    }

    // กำหนดนามสกุลไฟล์
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($file_extension, $allowed_extensions)) {
        throw new Exception("รองรับเฉพาะไฟล์ JPG, PNG และ WEBP เท่านั้น");
    }

    // สร้างชื่อไฟล์ที่ไม่ซ้ำกัน คงนามสกุลเดิมไว้
    $original_name = pathinfo($file['name'], PATHINFO_FILENAME);
    $filename = sanitize_filename($original_name) . '_' . uniqid() . '.' . $file_extension;
    $filepath = $upload_path . $filename;

    // ย้ายไฟล์โดยตรงโดยไม่ต้องแปลงหรือปรับขนาด
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception("ไม่สามารถบันทึกรูปภาพได้ กรุณาตรวจสอบสิทธิ์การเข้าถึงโฟลเดอร์");
    }

    return $filename;
}
// เพิ่มฟังก์ชันสำหรับทำความสะอาดชื่อไฟล์
function sanitize_filename($filename)
{
    // ลบอักขระพิเศษและแทนที่ช่องว่างด้วย underscore
    $filename = preg_replace('/[^a-zA-Z0-9ก-๙\-_.]/', '_', $filename);
    $filename = preg_replace('/-+/', '-', $filename);
    return strtolower(trim($filename, '-'));
}
// เพิ่มฟังก์ชันเพื่อดึงข้อมูลรูปภาพ
function get_percent_necklace_image($pdo, $pn_id)
{
    $sql = "SELECT image FROM percent_necklace WHERE pn_id = :pn_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['pn_id' => $pn_id]);
    return $stmt->fetchColumn();
}
// เพิ่มฟังก์ชันเพื่อดึงข้อมูล necklace_detail_parts
function get_necklace_detail_parts($pdo, $pnd_id)
{
    $sql = "SELECT * FROM necklace_detail_parts WHERE pnd_id = :pnd_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['pnd_id' => $pnd_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// เพิ่มฟังก์ชันบันทึกข้อมูล necklace_detail_parts
function add_necklace_detail_parts($pdo, $pnd_id, $wire_hole, $wire_thick, $wire_core, $scale_wire_weight, $scale_wire_thick, $parts_weight, $parts_height, $parts_thick)
{
    $sql = "INSERT INTO necklace_detail_parts (pnd_id, wire_hole, wire_thick, wire_core, scale_wire_weight, scale_wire_thick, parts_weight, parts_height, parts_thick) 
            VALUES (:pnd_id, :wire_hole, :wire_thick, :wire_core, :scale_wire_weight, :scale_wire_thick, :parts_weight, :parts_height, :parts_thick)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'pnd_id' => $pnd_id,
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

// เพิ่มฟังก์ชันอัพเดทข้อมูล necklace_detail_parts
function update_necklace_detail_parts($pdo, $ndp_id, $pnd_id, $wire_hole, $wire_thick, $wire_core, $scale_wire_weight, $scale_wire_thick, $parts_weight, $parts_height, $parts_thick)
{
    $sql = "UPDATE necklace_detail_parts SET 
            wire_hole = :wire_hole,
            wire_thick = :wire_thick,
            wire_core = :wire_core,
            scale_wire_weight = :scale_wire_weight,
            scale_wire_thick = :scale_wire_thick,
            parts_weight = :parts_weight,
            parts_height = :parts_height,
            parts_thick = :parts_thick
            WHERE ndp_id = :ndp_id AND pnd_id = :pnd_id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'ndp_id' => $ndp_id,
        'pnd_id' => $pnd_id,
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
// เพิ่มฟังก์ชันเพื่อดึง pnd_id ทั้งหมดของ pn_id
function get_all_pnd_ids_by_pn_id($pdo, $pn_id)
{
    $sql = "SELECT pnd_id FROM percent_necklace_detail WHERE pn_id = :pn_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['pn_id' => $pn_id]);
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    return $result;
}
