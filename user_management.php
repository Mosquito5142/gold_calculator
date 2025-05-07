<?php
require 'functions/check_login.php';
require 'functions/check_admin.php'; // ตรวจสอบว่าเป็น Admin หรือไม่
// แสดงerror
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'config/db_connect.php'; // เชื่อมต่อกับฐานข้อมูล
require 'functions/management_user.php'; // นำเข้าคลาส User

if ($_SESSION['recipenecklace_users_level'] == 'Admin') {
    $users = getAllUsers($pdo);
} else {
    $users = getAllmechanic($pdo);
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
    <meta name="description" content="POS - Bootstrap Admin Template" />
    <meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, html5, responsive" />
    <meta name="author" content="Dreamguys - Bootstrap Admin Template" />
    <meta name="robots" content="noindex, nofollow" />
    <title>ผู้ใช้งาน</title>

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css" />
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />
</head>

<body>
    <!-- Loading -->
    <div id="global-loader">
        <div class="whirly-loader"></div>
    </div>
    <!-- End Loading -->

    <div class="main-wrapper">
        <?php include 'include/header.php'; ?>
        <?php include 'include/sidebar.php'; ?>

        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="card-title">จัดการผู้ใช้งาน</h4>
                                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                        <i class="fas fa-user-plus"></i> เพิ่มผู้ใช้งาน
                                    </a>
                                </div>
                                <div class="table-responsive text-center">
                                    <table id="userTable" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>ชื่อ</th>
                                                <th>นามสกุล</th>
                                                <th>ชื่อผู้ใช้</th>
                                                <th>ระดับ</th>
                                                <th>แผนก</th>
                                                <th>สถานะ</th>
                                                <th>จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($users as $user): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($user['users_id']); ?></td>
                                                    <td class="text-start"><?php echo htmlspecialchars($user['first_name']); ?></td>
                                                    <td class="text-start"><?php echo htmlspecialchars($user['last_name']); ?></td>
                                                    <td class="text-start"><?php echo htmlspecialchars($user['username']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['users_level']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['users_depart']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['users_status'] == 'Enable' ? 'เปิดใช้งาน' : 'ปิดใช้งาน'); ?></td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm text-white btn-edit" data-user='<?php echo json_encode($user); ?>'>
                                                            <i class="fas fa-edit"></i> แก้ไข
                                                        </button>
                                                        <button class="btn btn-danger btn-sm text-white btn-delete" data-id="<?php echo $user['users_id']; ?>">
                                                            <i class="fas fa-trash"></i> ลบ
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'modal/user_modal.php'; ?>
        </div>
    </div>

    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/feather.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
    <script src="assets/plugins/sweetalert/sweetalerts.min.js"></script>
    <script src="assets/plugins/select2/js/select2.min.js"></script>
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        $(document).ready(function() {
            $('#userTable').DataTable();

            // Add User Form Submission
            $('#addUserForm').on('submit', function(e) {
                e.preventDefault();

                var password = $('#password').val();
                var confirmPassword = $('#confirm_password').val();

                if (password !== confirmPassword) {
                    Swal.fire({
                        icon: 'error',
                        title: 'ข้อผิดพลาด',
                        text: 'รหัสผ่านไม่ตรงกัน',
                    });
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: 'actions/user_actions.php',
                    data: $(this).serialize() + '&action=add',
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: 'เพิ่มผู้ใช้งานเรียบร้อยแล้ว',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'ข้อผิดพลาด',
                                text: result.message,
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'ข้อผิดพลาด',
                            text: 'ไม่สามารถเพิ่มผู้ใช้งานได้',
                        });
                    }
                });
            });

            // Event delegation for Edit User Button Click
            $('#userTable').on('click', '.btn-edit', function() {
                var user = $(this).data('user');
                $('#edit_users_id').val(user.users_id);
                $('#edit_first_name').val(user.first_name);
                $('#edit_last_name').val(user.last_name);
                $('#edit_username').val(user.username);
                $('#edit_users_level').val(user.users_level);
                $('#edit_users_depart').val(user.users_depart);
                $('#edit_users_status').val(user.users_status);
                $('#editUserModal').modal('show');
            });

            // Edit User Form Submission
            $('#editUserForm').on('submit', function(e) {
                e.preventDefault();

                var password = $('#edit_password').val();
                var confirmPassword = $('#edit_confirm_password').val();

                if (password && password !== confirmPassword) {
                    Swal.fire({
                        icon: 'error',
                        title: 'ข้อผิดพลาด',
                        text: 'รหัสผ่านไม่ตรงกัน',
                    });
                    return;
                }

                var formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: 'actions/user_actions.php',
                    data: formData + '&action=edit',
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: 'แก้ไขผู้ใช้งานเรียบร้อยแล้ว',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'ข้อผิดพลาด',
                                text: result.message,
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'ข้อผิดพลาด',
                            text: 'ไม่สามารถแก้ไขผู้ใช้งานได้',
                        });
                    }
                });
            });

            // Event delegation for Delete User Button Click
            $('#userTable').on('click', '.btn-delete', function() {
                var userId = $(this).data('id');
                $('#delete_users_id').val(userId);
                $('#deleteUserModal').modal('show');
            });

            // Delete User Form Submission
            $('#deleteUserForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: 'actions/user_actions.php',
                    data: $(this).serialize() + '&action=delete',
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: 'ลบผู้ใช้งานเรียบร้อยแล้ว',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'ข้อผิดพลาด',
                                text: result.message,
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'ข้อผิดพลาด',
                            text: 'ไม่สามารถลบผู้ใช้งานได้',
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>