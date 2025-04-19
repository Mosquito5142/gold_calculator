<?php
$servername = "proxy-50.digital.organic:5436";
$username = "root";
$password = "3F0sRa4mp88o";
$dbname = "recipenecklace";

// เชื่อมต่อ MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// เปิดไฟล์ CSV
$file = fopen("สเปรดชีตไม่มีชื่อ - ชีต1.csv", "r");
fgetcsv($file); // ข้ามสองแถวแรก
fgetcsv($file);

while (($data = fgetcsv($file)) !== FALSE) {
    // แปลงค่าว่างเป็น NULL
    $type = !empty($data[0]) ? "'" . $conn->real_escape_string($data[0]) . "'" : "NULL";
    $name = !empty($data[1]) ? "'" . $conn->real_escape_string($data[1]) . "'" : "NULL";
    $ptt_thick = !empty($data[2]) ? $data[2] : "NULL";
    $ptt_core = !empty($data[3]) ? $data[3] : "NULL";
    $ptt_ratio = !empty($data[4]) ? $data[4] : "NULL";
    $agpt_thick = !empty($data[5]) ? $data[5] : "NULL";
    $agpt_core = !empty($data[6]) ? $data[6] : "NULL";
    $agpt_ratio = !empty($data[7]) ? $data[7] : "NULL";
    $true_length = !empty($data[8]) ? $data[8] : "NULL";
    $true_weight = !empty($data[9]) ? $data[9] : "NULL";
    $comment = !empty($data[21]) ? "'" . $conn->real_escape_string($data[21]) . "'" : "NULL";

    // แก้ไข SQL query ให้รองรับ NULL
    $sql = "INSERT INTO necklace_detail (
                name, type, ptt_thick, ptt_core, ptt_ratio, 
                agpt_thick, agpt_core, agpt_ratio, true_length, true_weight, comment
            ) VALUES (
                $name, $type, $ptt_thick, $ptt_core, $ptt_ratio,
                $agpt_thick, $agpt_core, $agpt_ratio, $true_length, $true_weight, $comment
            )";

    if (!$conn->query($sql)) {
        echo "Error: " . $sql . "<br>" . $conn->error;
        continue;
    }

    $necklace_detail_id = $conn->insert_id;

    // เพิ่มข้อมูล TBS
    for ($i = 10; $i <= 15; $i += 2) {
        $tbs_before = !empty($data[$i]) ? $data[$i] : "NULL";
        $tbs_after = !empty($data[$i + 1]) ? $data[$i + 1] : "NULL";

        // เพิ่มเฉพาะเมื่อมีค่าอย่างน้อย 1 ค่า
        if ($tbs_before != "NULL" || $tbs_after != "NULL") {
            $sql = "INSERT INTO necklace_tbs (necklace_detail_id, tbs_before, tbs_after) 
                    VALUES ($necklace_detail_id, $tbs_before, $tbs_after)";
            $conn->query($sql);
        }
    }

    // เพิ่มข้อมูล proportions
    $proportions_size = !empty($data[16]) ? $data[16] : "NULL";
    $proportions_width = !empty($data[17]) ? $data[17] : "NULL";
    $proportions_thick = !empty($data[18]) ? $data[18] : "NULL";

    // เพิ่มเฉพาะเมื่อมีค่าอย่างน้อย 1 ค่า
    if ($proportions_size != "NULL" || $proportions_width != "NULL" || $proportions_thick != "NULL") {
        $sql = "INSERT INTO necklace_proportions (
                    necklace_detail_id, proportions_size, proportions_width, proportions_thick
                ) VALUES (
                    $necklace_detail_id, $proportions_size, $proportions_width, $proportions_thick
                )";
        $conn->query($sql);
    }
}

fclose($file);
$conn->close();

echo "Import completed successfully!";
