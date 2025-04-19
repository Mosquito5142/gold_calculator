<?php
require 'functions/check_login.php';
// แสดงerror
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'config/db_connect.php'; // เชื่อมต่อกับฐานข้อมูล
require 'functions/management_ratio.php'; // นำเข้าคลาส User

$ratio_data = get_ratio_data($pdo);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, ratio_data-scalable=0" />
    <meta name="description" content="POS - Bootstrap Admin Template" />
    <meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, html5, responsive" />
    <meta name="author" content="Dreamguys - Bootstrap Admin Template" />
    <meta name="robots" content="noindex, nofollow" />
    <title>จัดการอัตราส่วน</title>

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
                                    <h4 class="card-title">จัดการอัตราส่วน</h4>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRatioModal">
                                        เพิ่มอัตราส่วน
                                    </button>
                                </div>
                                <div class="table-responsive text-center">
                                    <table id="ratio_dataTable" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>หนา</th>
                                                <th>อัตราส่วน</th>
                                                <th>รูลวด</th>
                                                <th>นน.ลวดก่อนสกัด(กรัม)</th>
                                                <th>ค.ยาวลวด(นิ้ว)</th>
                                                <th>อัพเดทล่าสุด</th>
                                                <th>โดย</th>
                                                <th>จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($ratio_data as $data): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($data['ratio_id']); ?></td>
                                                    <td><?php echo htmlspecialchars($data['ratio_thick']); ?></td>
                                                    <td><?php echo htmlspecialchars($data['ratio_data']); ?></td>
                                                    <td><?php echo htmlspecialchars($data['ratio_size']); ?></td>
                                                    <td><?php echo htmlspecialchars($data['ratio_gram']); ?></td>
                                                    <td><?php echo htmlspecialchars($data['ratio_inch']); ?></td>
                                                    <td><?php echo htmlspecialchars($data['updated_at']); ?></td>
                                                    <td><?php echo htmlspecialchars($data['first_name']); ?></td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm text-white btn-edit" data-bs-toggle="modal" data-bs-target="#editRatioModal" data-ratio='<?php echo json_encode($data); ?>'>
                                                            <i class="fas fa-edit"></i> แก้ไข
                                                        </button>
                                                        <button class="btn btn-danger btn-sm text-white btn-delete" data-bs-toggle="modal" data-bs-target="#deleteRatioModal" data-id="<?php echo $data['ratio_id']; ?>">
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
        </div>
    </div>
    <?php include 'modal/ratio_data_modal.php'; ?>

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
            $('#ratio_dataTable').DataTable();

            $('#addRatioForm').on('submit', function(e) {
                e.preventDefault(); // ป้องกันการรีเฟรชหน้า

                $.ajax({
                    type: 'POST',
                    url: 'actions/ratio_data_actions.php', // URL สำหรับส่งคำขอ
                    data: $(this).serialize() + '&action=add', // ส่งข้อมูลฟอร์มพร้อม action=add
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: result.message,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload(); // รีเฟรชหน้าเพื่ออัปเดตข้อมูล
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
                            text: 'ไม่สามารถเพิ่มอัตราส่วนได้',
                        });
                    },
                });
            });

            // Edit Ratio Form Submission
            $('#editRatioForm').on('submit', function(e) {
                e.preventDefault(); // ป้องกันการรีเฟรชหน้า

                $.ajax({
                    type: 'POST',
                    url: 'actions/ratio_data_actions.php', // URL สำหรับส่งคำขอ
                    data: $(this).serialize() + '&action=edit', // ส่งข้อมูลฟอร์มพร้อม action=edit
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: result.message,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload(); // รีเฟรชหน้าเพื่ออัปเดตข้อมูล
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
                            text: 'ไม่สามารถแก้ไขอัตราส่วนได้',
                        });
                    },
                });
            });

            // Delete Ratio Form Submission
            $('#deleteRatioForm').on('submit', function(e) {
                e.preventDefault(); // ป้องกันการรีเฟรชหน้า

                $.ajax({
                    type: 'POST',
                    url: 'actions/ratio_data_actions.php', // URL สำหรับส่งคำขอ
                    data: $(this).serialize() + '&action=delete', // ส่งข้อมูลฟอร์มพร้อม action=delete
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: result.message,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload(); // รีเฟรชหน้าเพื่ออัปเดตข้อมูล
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
                            text: 'ไม่สามารถลบอัตราส่วนได้',
                        });
                    },
                });
            });

            // Open Edit Modal and Populate Data
            $('#ratio_dataTable').on('click', '.btn-edit', function() {
                var ratioData = $(this).data('ratio'); // ดึงข้อมูลจาก data-ratio
                $('#edit_ratio_id').val(ratioData.ratio_id);
                $('#edit_ratio_thick').val(ratioData.ratio_thick);
                $('#edit_ratio_data').val(ratioData.ratio_data);
                $('#edit_ratio_size').val(ratioData.ratio_size);
                $('#edit_ratio_gram').val(ratioData.ratio_gram);
                $('#edit_ratio_inch').val(ratioData.ratio_inch);
                $('#editRatioModal').modal('show'); // เปิด Modal
            });

            // Open Delete Modal and Set ID
            $('#ratio_dataTable').on('click', '.btn-delete', function() {
                var ratioId = $(this).data('id'); // ดึง ID จาก data-id
                $('#delete_ratio_id').val(ratioId);
                $('#deleteRatioModal').modal('show'); // เปิด Modal
            });
        });
    </script>
</body>

</html>