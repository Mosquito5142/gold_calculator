<?php
function get_necklace_all_details($pdo)
{
    $sql = "SELECT 
                nd.necklace_detail_id, 
                nd.name, 
                nd.type, 
                nd.ptt_thick, 
                nd.ptt_core, 
                nd.ptt_ratio, 
                nd.agpt_thick, 
                nd.agpt_core, 
                nd.agpt_ratio, 
                nd.true_length, 
                nd.true_weight,
                ANY_VALUE(np.proportions_size) AS proportions_size,
                ANY_VALUE(np.proportions_width) AS proportions_width,
                ANY_VALUE(np.proportions_thick) AS proportions_thick,
                ANY_VALUE(IFNULL(proportions_width / NULLIF(np.proportions_size, 0), 0)) AS ratio_width,
                ANY_VALUE(IFNULL(proportions_thick / NULLIF(np.proportions_size, 0), 0)) AS ratio_thick,
                nd.image,
                nd.comment,
                nd.updated_users_id, 
                u.first_name,
                u.last_name,
                nd.updated_at
            FROM necklace_detail nd
            LEFT JOIN necklace_proportions np ON nd.necklace_detail_id = np.necklace_detail_id
            LEFT JOIN users u ON nd.updated_users_id = u.users_id
            WHERE nd.pd_status='master'
            GROUP BY nd.necklace_detail_id
            ORDER BY nd.name";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function get_necklace_all_copy($pdo)
{
    $sql = "SELECT 
                nd.necklace_detail_id, 
                nd.name, 
                nd.type, 
                nd.ptt_thick, 
                nd.ptt_core, 
                nd.ptt_ratio, 
                nd.agpt_thick, 
                nd.agpt_core, 
                nd.agpt_ratio, 
                nd.true_length, 
                nd.true_weight,
                ANY_VALUE(np.proportions_size) AS proportions_size,
                ANY_VALUE(np.proportions_width) AS proportions_width,
                ANY_VALUE(np.proportions_thick) AS proportions_thick,
                ANY_VALUE(IFNULL(proportions_width / NULLIF(np.proportions_size, 0), 0)) AS ratio_width,
                ANY_VALUE(IFNULL(proportions_thick / NULLIF(np.proportions_size, 0), 0)) AS ratio_thick,
                nd.image,
                nd.comment,
                nd.updated_users_id, 
                u.first_name,
                nd.updated_at,
                ANY_VALUE(nc.weight) AS calc_weight,
                ANY_VALUE(nc.length) AS calc_length,
                ANY_VALUE(nc.gold_type) AS gold_type,
                ANY_VALUE(nc.ratio_id) AS ratio_id,
                ANY_VALUE(rd.ratio_data) AS ratio_data
            FROM necklace_detail nd
            LEFT JOIN necklace_proportions np ON nd.necklace_detail_id = np.necklace_detail_id
            LEFT JOIN users u ON nd.updated_users_id = u.users_id
            LEFT JOIN necklace_calculation nc ON nd.necklace_detail_id = nc.necklace_detail_id
            LEFT JOIN ratio_data rd ON nc.ratio_id = rd.ratio_id
            WHERE nd.pd_status='copy'
            GROUP BY nd.necklace_detail_id
            ORDER BY nd.updated_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function get_necklace_details_by_user($pdo, $user_id)
{
    $sql = "SELECT 
                nd.necklace_detail_id, 
                nd.name, 
                nd.type, 
                nd.ptt_thick, 
                nd.ptt_core, 
                nd.ptt_ratio, 
                nd.agpt_thick, 
                nd.agpt_core, 
                nd.agpt_ratio, 
                nd.true_length, 
                nd.true_weight,
                ANY_VALUE(np.proportions_size) AS proportions_size,
                ANY_VALUE(np.proportions_width) AS proportions_width,
                ANY_VALUE(np.proportions_thick) AS proportions_thick,
                ANY_VALUE(IFNULL(proportions_width / NULLIF(np.proportions_size, 0), 0)) AS ratio_width,
                ANY_VALUE(IFNULL(proportions_thick / NULLIF(np.proportions_size, 0), 0)) AS ratio_thick,
                nd.image,
                nd.comment, 
                nd.updated_users_id,
                u.first_name,
                u.last_name,
                nd.updated_at,
                CASE 
                    WHEN nd.updated_users_id = :user_id THEN 'own' 
                    ELSE 'shared' 
                END AS access_type,
                CASE 
                    WHEN ns.sharing_by IS NOT NULL THEN ns.sharing_by
                    ELSE NULL
                END AS shared_by
            FROM necklace_detail nd
            LEFT JOIN necklace_proportions np ON nd.necklace_detail_id = np.necklace_detail_id
            LEFT JOIN users u ON nd.updated_users_id = u.users_id
            LEFT JOIN necklace_sharing ns ON nd.necklace_detail_id = ns.nd_id AND ns.users_id = :user_id
            WHERE nd.updated_users_id = :user_id OR ns.users_id = :user_id
            GROUP BY nd.necklace_detail_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function get_necklace_all_copy_by_user($pdo, $user_id)
{
    $sql = "SELECT 
                nd.necklace_detail_id, 
                nd.name, 
                nd.type, 
                nd.ptt_thick, 
                nd.ptt_core, 
                nd.ptt_ratio, 
                nd.agpt_thick, 
                nd.agpt_core, 
                nd.agpt_ratio, 
                nd.true_length, 
                nd.true_weight,
                ANY_VALUE(np.proportions_size) AS proportions_size,
                ANY_VALUE(np.proportions_width) AS proportions_width,
                ANY_VALUE(np.proportions_thick) AS proportions_thick,
                ANY_VALUE(IFNULL(proportions_width / NULLIF(np.proportions_size, 0), 0)) AS ratio_width,
                ANY_VALUE(IFNULL(proportions_thick / NULLIF(np.proportions_size, 0), 0)) AS ratio_thick,
                nd.image,
                nd.comment,
                nd.updated_users_id, 
                u.first_name,
                nd.updated_at,
                ANY_VALUE(nc.weight) AS calc_weight,
                ANY_VALUE(nc.length) AS calc_length,
                ANY_VALUE(nc.gold_type) AS gold_type,
                ANY_VALUE(nc.ratio_id) AS ratio_id,
                ANY_VALUE(rd.ratio_data) AS ratio_data
            FROM necklace_detail nd
            LEFT JOIN necklace_proportions np ON nd.necklace_detail_id = np.necklace_detail_id
            LEFT JOIN users u ON nd.updated_users_id = u.users_id
            LEFT JOIN necklace_calculation nc ON nd.necklace_detail_id = nc.necklace_detail_id
            LEFT JOIN ratio_data rd ON nc.ratio_id = rd.ratio_id
            WHERE nd.pd_status='copy' AND nd.updated_users_id = :user_id
            GROUP BY nd.necklace_detail_id
            ORDER BY nd.updated_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_necklace_detail_by_id($pdo, $id)
{
    $sql = "SELECT 
                nd.necklace_detail_id, 
                nd.name, 
                nd.type, 
                nd.ptt_thick, 
                nd.ptt_core, 
                nd.ptt_ratio, 
                nd.agpt_thick, 
                nd.agpt_core, 
                nd.agpt_ratio, 
                nd.true_length, 
                nd.true_weight,
                np.shapeshape_necklace,
                ANY_VALUE(np.proportions_size) AS proportions_size,
                ANY_VALUE(np.proportions_width) AS proportions_width,
                ANY_VALUE(np.proportions_thick) AS proportions_thick,
                ANY_VALUE(IFNULL(proportions_width / NULLIF(np.proportions_size, 0), 0)) AS ratio_width,
                ANY_VALUE(IFNULL(proportions_thick / NULLIF(np.proportions_size, 0), 0)) AS ratio_thick,
                nd.image,
                nd.comment, 
                nd.updated_at
            FROM necklace_detail nd
            LEFT JOIN necklace_proportions np ON nd.necklace_detail_id = np.necklace_detail_id
            WHERE nd.necklace_detail_id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getnecklace_tbs_Byid($pdo, $necklace_detail_id)
{
    $sql = "SELECT * FROM necklace_tbs WHERE necklace_detail_id = :necklace_detail_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['necklace_detail_id' => $necklace_detail_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function add_necklace_detail($pdo, $name, $type, $ptt_thick, $ptt_core, $ptt_ratio, $agpt_thick, $agpt_core, $agpt_ratio, $true_length, $true_weight, $comment, $image, $updated_users_id)
{
    // ตรวจสอบชื่อซ้ำ
    if (check_duplicate_name($pdo, $name)) {
        throw new Exception("ชื่อลายสร้อยนี้มีอยู่แล้วในระบบ");
    }
    $sql = "INSERT INTO necklace_detail (name, type, ptt_thick, ptt_core, ptt_ratio, 
            agpt_thick, agpt_core, agpt_ratio, true_length, true_weight, comment, image, updated_users_id) 
            VALUES (:name, :type, :ptt_thick, :ptt_core, :ptt_ratio, :agpt_thick, 
            :agpt_core, :agpt_ratio, :true_length, :true_weight, :comment, :image, :updated_users_id)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'name' => $name,
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
        'image' => $image,
        'updated_users_id' => $updated_users_id
    ]);
}
function update_necklace_detail(
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
    $image = null
) {
    $sql = "UPDATE necklace_detail SET 
            name = :name, 
            type = :type, 
            ptt_thick = :ptt_thick, 
            ptt_core = :ptt_core, 
            ptt_ratio = :ptt_ratio,
            agpt_thick = :agpt_thick, 
            agpt_core = :agpt_core, 
            agpt_ratio = :agpt_ratio,
            true_length = :true_length,
            true_weight = :true_weight,
            comment = :comment";

    // เพิ่ม image เข้าไปในคำสั่ง SQL เมื่อมีการอัพโหลดรูปใหม่
    if ($image !== null) {
        $sql .= ", image = :image";
    }

    $sql .= " WHERE necklace_detail_id = :id";

    $params = [
        'id' => $id,
        'name' => $name,
        'type' => $type,
        'ptt_thick' => $ptt_thick,
        'ptt_core' => $ptt_core,
        'ptt_ratio' => $ptt_ratio,
        'agpt_thick' => $agpt_thick,
        'agpt_core' => $agpt_core,
        'agpt_ratio' => $agpt_ratio,
        'true_length' => $true_length,
        'true_weight' => $true_weight,
        'comment' => $comment
    ];

    if ($image !== null) {
        $params['image'] = $image;
    }

    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}
function add_necklace_tbs($pdo, $necklace_detail_id, $tbs_name, $tbs_before, $tbs_after)
{
    $sql = "INSERT INTO necklace_tbs (necklace_detail_id, tbs_name, tbs_before, tbs_after) 
            VALUES (:necklace_detail_id, :tbs_name, :tbs_before, :tbs_after)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'necklace_detail_id' => $necklace_detail_id,
        'tbs_name' => $tbs_name,
        'tbs_before' => $tbs_before,
        'tbs_after' => $tbs_after
    ]);
}
function add_necklace_proportions($pdo, $necklace_detail_id, $proportions_size, $proportions_width, $proportions_thick, $shapeshape_necklace)
{
    $sql = "INSERT INTO necklace_proportions (necklace_detail_id, proportions_size, proportions_width, proportions_thick";

    if ($shapeshape_necklace !== null) {
        $sql .= ", shapeshape_necklace";
    }

    $sql .= ") VALUES (:necklace_detail_id, :proportions_size, :proportions_width, :proportions_thick";

    if ($shapeshape_necklace !== null) {
        $sql .= ", :shapeshape_necklace";
    }

    $sql .= ")";

    $stmt = $pdo->prepare($sql);

    $params = [
        'necklace_detail_id' => $necklace_detail_id,
        'proportions_size' => $proportions_size,
        'proportions_width' => $proportions_width,
        'proportions_thick' => $proportions_thick
    ];

    if ($shapeshape_necklace !== null) {
        $params['shapeshape_necklace'] = $shapeshape_necklace;
    }

    $stmt->execute($params);
}
function update_necklace_proportions($pdo, $necklace_detail_id, $proportions_size, $proportions_width, $proportions_thick, $shapeshape_necklace = null)
{
    // First check if a record exists
    $check_sql = "SELECT COUNT(*) FROM necklace_proportions WHERE necklace_detail_id = :necklace_detail_id";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute(['necklace_detail_id' => $necklace_detail_id]);

    if ($check_stmt->fetchColumn() > 0) {
        // Update existing record
        $sql = "UPDATE necklace_proportions SET 
                proportions_size = :proportions_size, 
                proportions_width = :proportions_width, 
                proportions_thick = :proportions_thick";

        // Add shape field if provided
        if ($shapeshape_necklace !== null) {
            $sql .= ", shapeshape_necklace = :shapeshape_necklace";
        }

        $sql .= " WHERE necklace_detail_id = :necklace_detail_id";

        $stmt = $pdo->prepare($sql);

        $params = [
            'necklace_detail_id' => $necklace_detail_id,
            'proportions_size' => $proportions_size,
            'proportions_width' => $proportions_width,
            'proportions_thick' => $proportions_thick
        ];

        if ($shapeshape_necklace !== null) {
            $params['shapeshape_necklace'] = $shapeshape_necklace;
        }

        $stmt->execute($params);
    } else {
        // Insert new record if none exists
        add_necklace_proportions($pdo, $necklace_detail_id, $proportions_size, $proportions_width, $proportions_thick, $shapeshape_necklace);
    }
}
function check_duplicate_name($pdo, $name, $id = null)
{
    $sql = "SELECT COUNT(*) FROM necklace_detail WHERE name = :name";
    $params = ['name' => $name];

    if ($id) {
        $sql .= " AND necklace_detail_id != :id";
        $params['id'] = $id;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn() > 0;
}
function handle_image_upload($file)
{
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return null;
    }

    // กำหนด path ให้ถูกต้อง
    $upload_path = dirname(dirname(__FILE__)) . '/uploads/img/necklace_detail/';
    if (!file_exists($upload_path)) {
        mkdir($upload_path, 0777, true);
    }

    // สร้างชื่อไฟล์ที่ไม่ซ้ำกัน
    $original_name = pathinfo($file['name'], PATHINFO_FILENAME);
    $filename = sanitize_filename($original_name) . '_' . uniqid() . '.webp';
    $filepath = $upload_path . $filename;

    // โหลดรูปต้นฉบับ
    $source_image = null;
    $image_type = exif_imagetype($file['tmp_name']);

    switch ($image_type) {
        case IMAGETYPE_JPEG:
            $source_image = imagecreatefromjpeg($file['tmp_name']);
            break;
        case IMAGETYPE_PNG:
            $source_image = imagecreatefrompng($file['tmp_name']);
            break;
        case IMAGETYPE_WEBP:
            $source_image = imagecreatefromwebp($file['tmp_name']);
            break;
        default:
            throw new Exception("รองรับเฉพาะไฟล์ JPG, PNG และ WEBP เท่านั้น");
    }

    if (!$source_image) {
        throw new Exception("ไม่สามารถโหลดรูปภาพได้");
    }

    // ปรับขนาดรูป
    $width = imagesx($source_image);
    $height = imagesy($source_image);
    $new_width = 800;

    if ($width > $new_width) {
        $new_height = floor($height * ($new_width / $width));
        $tmp = imagecreatetruecolor($new_width, $new_height);

        // รักษาความโปร่งใสสำหรับ PNG
        imagealphablending($tmp, false);
        imagesavealpha($tmp, true);

        imagecopyresampled($tmp, $source_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        $source_image = $tmp;
    }

    // บันทึกเป็น WebP
    if (!imagewebp($source_image, $filepath, 80)) {
        throw new Exception("ไม่สามารถบันทึกรูปภาพได้");
    }

    imagedestroy($source_image);
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
function get_calculation_data($pdo, $necklace_id)
{
    $stmt = $pdo->prepare("SELECT * FROM necklace_calculation WHERE necklace_detail_id = :necklace_id ORDER BY created_at DESC LIMIT 1");
    $stmt->execute(['necklace_id' => $necklace_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
