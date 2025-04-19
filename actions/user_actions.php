<?php
require '../config/db_connect.php';
require '../functions/management_user.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    try {
        switch ($action) {
            case 'add':
                $first_name = $_POST['first_name'];
                $last_name = $_POST['last_name'];
                $username = $_POST['username'];
                $password = $_POST['password'];
                $users_level = $_POST['users_level'];
                $users_depart = $_POST['users_depart'];
                $users_status = $_POST['users_status'];

                // Check if the username already exists
                $stmt = $pdo->prepare("SELECT users_id FROM users WHERE username = :username");
                $stmt->execute(['username' => $username]);
                if ($stmt->rowCount() > 0) {
                    echo json_encode(['status' => 'error', 'message' => 'Username already exists']);
                    break;
                }

                addUser($pdo, $first_name, $last_name, $username, $password, $users_level, $users_depart, $users_status);
                echo json_encode(['status' => 'success']);
                break;

            case 'edit':
                $users_id = $_POST['users_id'];
                $first_name = $_POST['first_name'];
                $last_name = $_POST['last_name'];
                $username = $_POST['username'];
                $users_level = $_POST['users_level'];
                $users_depart = $_POST['users_depart'];
                $users_status = $_POST['users_status'];
                $password = isset($_POST['password']) ? $_POST['password'] : null;

                editUser($pdo, $users_id, $first_name, $last_name, $username, $users_level, $users_depart, $users_status, $password);
                echo json_encode(['status' => 'success']);
                break;

            case 'delete':
                $users_id = $_POST['users_id'];

                deleteUser($pdo, $users_id);
                echo json_encode(['status' => 'success']);
                break;

            default:
                echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
                break;
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
