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
$file = fopen("addratio_data - ชีต1.csv", "r");
fgetcsv($file); // ข้ามแถวแรกที่เป็นหัวตาราง

while (($data = fgetcsv($file)) !== FALSE) {
    // ทำความสะอาดและตรวจสอบข้อมูล
    $ratio_thick = !empty($data[0]) ? "'" . $conn->real_escape_string(trim($data[0])) . "'" : "NULL";
    $ratio_data = !empty($data[1]) ? $data[1] : "NULL";
    $ratio_size = !empty($data[2]) ? $data[2] : "NULL";
    $ratio_gram = !empty($data[3]) ? $data[3] : "NULL";
    $ratio_inch = !empty($data[4]) ? $data[4] : "NULL";

    // ตรวจสอบว่าค่าตัวเลขเป็นตัวเลขจริง
    if ($ratio_data != "NULL" && !is_numeric($ratio_data)) continue;
    if ($ratio_size != "NULL" && !is_numeric($ratio_size)) continue;
    if ($ratio_gram != "NULL" && !is_numeric($ratio_gram)) continue;
    if ($ratio_inch != "NULL" && !is_numeric($ratio_inch)) continue;

    $sql = "INSERT INTO ratio_data (
                ratio_thick, ratio_data, ratio_size, ratio_gram, ratio_inch
            ) VALUES (
                $ratio_thick, $ratio_data, $ratio_size, $ratio_gram, $ratio_inch
            )";

    if (!$conn->query($sql)) {
        echo "Error: " . $sql . "<br>" . $conn->error . "<br>";
        continue;
    }
}

fclose($file);
$conn->close();

echo "Ratio data import completed successfully!";