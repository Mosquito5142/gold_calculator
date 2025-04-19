<?php

function getAllUsers($pdo)
{
    $stmt = $pdo->prepare("SELECT users_id, first_name, last_name, username, users_level, users_depart, users_status FROM users");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// เพิ่มข้อมูลผู้ใช้งาน
function addUser($pdo, $first_name, $last_name, $username, $password, $users_level, $users_depart, $users_status)
{
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, username, password, users_level, users_depart, users_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$first_name, $last_name, $username, $hashed_password, $users_level, $users_depart, $users_status]);
}

// แก้ไขข้อมูลผู้ใช้งาน  
function editUser($pdo, $users_id, $first_name, $last_name, $username, $users_level, $users_depart, $users_status, $password = null)
{
    if ($password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, username = ?, users_level = ?, users_depart=?, users_status = ?, password = ? WHERE users_id = ?");
        $stmt->execute([$first_name, $last_name, $username, $users_level, $users_depart, $users_status, $hashed_password, $users_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, username = ?, users_level = ?, users_depart=?, users_status = ? WHERE users_id = ?");
        $stmt->execute([$first_name, $last_name, $username, $users_level, $users_depart, $users_status, $users_id]);
    }
}

// ลบข้อมูลผู้ใช้งาน
function deleteUser($pdo, $users_id)
{
    $stmt = $pdo->prepare("DELETE FROM users WHERE users_id = ?");
    $stmt->execute([$users_id]);
}

// ฟังก์ชันสำหรับการล็อกอิน
function login($pdo, $username)
{
    $stmt = $pdo->prepare("SELECT users_id, users_level, users_depart, users_status, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ฟังก์ชันสำหรับดึงข้อมูลผู้ใช้ตาม users_id
function getUserById($pdo, $users_id)
{
    $stmt = $pdo->prepare("SELECT users_id, first_name, last_name, username, password, users_level, users_depart, users_status FROM users WHERE users_id = ?");
    $stmt->execute([$users_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
