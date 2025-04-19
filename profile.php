<?php
session_start();
require 'config/db_connect.php';
require 'functions/management_user.php';

$users_id = $_SESSION['recipenecklace_users_id'];

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$user = getUserById($pdo, $users_id);
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
    <title>จัดการโปรไฟล์</title>

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css" />
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
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
                    <div class="col-lg-8 offset-lg-2">
                        <div class="card mb-0">
                            <div class="card-body">
                                <h4 class="card-title">โปรไฟล์ของคุณ</h4>
                                <div class="table-responsive dataview">
                                    <!-- แสดงค่าที่เก็บไว้ -->
                                    <?php if ($user): ?>
                                        <div class="text-center position-relative">
                                            <i class="me-2" data-feather="user" width="150" height="150"></i>
                                            <div class="overlay-text" style="display: none; color: green;">สามารถแก้ไขรูปโปรไฟล์</div>
                                            <h3><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>แผนก:</strong> <?php echo htmlspecialchars($user['users_depart']); ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>ชื่อผู้ใช้:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                                                </div>
                                            </div>
                                            <button class="btn btn-primary m-4" data-bs-toggle="modal" data-bs-target="#editProfileModal">แก้ไขโปรไฟล์</button>
                                        </div>
                                    <?php else: ?>
                                        <p>ไม่สามารถดึงข้อมูลผู้ใช้ได้</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal แก้ไขโปรไฟล์ -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">แก้ไขโปรไฟล์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" id="editUserId" name="users_id" value="<?php echo htmlspecialchars($user['users_id']); ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editFirstName">ชื่อ</label>
                                    <input type="text" class="form-control" id="editFirstName" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editLastName">นามสกุล</label>
                                    <input type="text" class="form-control" id="editLastName" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editLevel">ระดับสิทธิ</label>
                                    <input type="text" class="form-control" id="editLevel" name="users_level" value="<?php echo htmlspecialchars($user['users_level']); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_users_depart" class="form-label">แผนก</label>
                                <input type="text" class="form-control" id="edit_users_depart" name="users_depart" value="<?php echo htmlspecialchars($user['users_depart']); ?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <input type="hidden" id="editUsername" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                            <input type="hidden" id="editStatus" name="users_status" value="<?php echo htmlspecialchars($user['users_status']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="editPassword">รหัสผ่านใหม่ (ถ้าไม่เปลี่ยนให้เว้นว่าง)</label>
                            <input type="password" class="form-control" id="editPassword" name="password">
                        </div>
                        <div class="form-group">
                            <label for="confirmEditPassword">ยืนยันรหัสผ่านใหม่</label>
                            <input type="password" class="form-control" id="confirmEditPassword">
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                                <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">ปิด</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/feather.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
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
            // Handle edit profile form submission
            $('#editProfileForm').on('submit', function(e) {
                e.preventDefault();

                // ตรวจสอบว่ารหัสผ่านทั้งสองช่องตรงกันหรือไม่ (ถ้ามีการกรอก)
                var password = $('#editPassword').val();
                var confirmPassword = $('#confirmEditPassword').val();
                if (password && password !== confirmPassword) {
                    Swal.fire('Error', 'รหัสผ่านไม่ตรงกัน', 'error');
                    return;
                }

                $.ajax({
                    url: 'actions/user_actions.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        const res = JSON.parse(response);
                        if (res.status === 'success') {
                            Swal.fire({
                                title: 'Success',
                                text: 'บันทึกการเปลี่ยนแปลงเรียบร้อยแล้ว',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'เกิดข้อผิดพลาดในการส่งข้อมูล', 'error');
                    }
                });
            });
        });
    </script>
</body>

</html>