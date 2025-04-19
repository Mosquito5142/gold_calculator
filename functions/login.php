<?php
session_start();
require '../config/db_connect.php';
require 'management_user.php';

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = login($pdo, $username);

    if ($user && password_verify($password, $user['password'])) {
        if ($user['users_status'] === 'Disable') {
            $response['message'] = 'บัญชีผู้ใช้นี้ถูกปิดใช้งาน';
        } else {
            $_SESSION['recipenecklace_users_id'] = $user['users_id'];
            $_SESSION['recipenecklace_users_level'] = $user['users_level'];
            $_SESSION['recipenecklace_users_depart'] = $user['users_depart'];
            $_SESSION['recipenecklace_username'] = $username; // เก็บชื่อผู้ใช้ในเซสชัน
            $response['status'] = 'success';
        }
    } else {
        $response['message'] = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
    }
}

echo json_encode($response);
?>
