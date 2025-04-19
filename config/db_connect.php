<?php
$servername = "proxy-50.digital.organic:5436";
$username = "root";
$password = "3F0sRa4mp88o";
$dbname = "recipenecklace";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // ตรวจสอบว่าการเชื่อมต่อสำเร็จ
    // echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
