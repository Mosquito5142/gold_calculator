<?php
require 'functions/check_login.php';
require 'config/db_connect.php';
require 'functions/management_percent_necklace.php';

$percent_necklaces = get_percent_necklace($pdo);
$current_user_id = isset($_SESSION['recipenecklace_users_id']) ? $_SESSION['recipenecklace_users_id'] : 0;
$is_admin = isset($_SESSION['recipenecklace_users_level']) && $_SESSION['recipenecklace_users_level'] === 'Admin';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการ สัดส่วน%สร้อย</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css" />
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
    <style>
        ::placeholder {
            color: #ff9999 !important;
            opacity: 1;
        }

        input[style*="--placeholder-color: red"]::placeholder {
            color: #ff9999 !important;
            opacity: 1;
        }

        :-ms-input-placeholder {
            color: #ff9999 !important;
        }

        ::-ms-input-placeholder {
            color: #ff9999 !important;
        }
        hr {
            border: 0;
            height: 1px;
            background-color:rgb(0, 0, 0);
        }
    </style>
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
                <!-- <div class="page-header">
                    <div class="row">
                        <div class="col">
                            <h3 class="page-title">รายการงาน</h3>
                        </div>
                    </div>
                </div> -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4>จัดการ % สร้อย</h4>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#percentModal">
                                <i class="fas fa-plus"></i> เพิ่มข้อมูล
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="percent_necklace" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="60">รูป</th>
                                        <th>ชื่อ</th>
                                        <th>น้ำหนัก (กรัม)</th>
                                        <th>ผู้บันทึก</th>
                                        <th>วันที่บันทึก</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($percent_necklaces as $pn): ?>
                                        <tr>
                                            <td class="text-center">
                                                <?php if (!empty($pn['image'])): ?>
                                                    <img src="uploads/img/percent_necklace/<?php echo htmlspecialchars($pn['image']); ?>"
                                                        class="img-thumbnail" style="max-height: 50px; width: auto;"
                                                        onclick="showFullImage('uploads/img/percent_necklace/<?php echo htmlspecialchars($pn['image']); ?>', '<?php echo htmlspecialchars($pn['pn_name']); ?>')">
                                                <?php else: ?>
                                                    <i class="fas fa-image text-muted"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($pn['pn_name']); ?></td>
                                            <td><?php echo htmlspecialchars($pn['pn_grams']); ?></td>
                                            <td><?php echo htmlspecialchars($pn['first_name']); ?></td>
                                            <td><?php echo htmlspecialchars($pn['updated_at']); ?></td>
                                            <td>
                                                <!-- ปุ่มรูปตาเอาไว้ดูข้อมูล (ทุกคนสามารถดูได้) -->
                                                <button class="btn btn-info btn-sm" onclick="viewPercent(<?php echo $pn['pn_id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <?php if ($pn['users_id'] == $current_user_id || $is_admin): ?>
                                                    <!-- ปุ่มแก้ไขและลบ - แสดงเฉพาะเจ้าของข้อมูล -->
                                                    <button class="btn btn-warning btn-sm" onclick="editPercent(<?php echo $pn['pn_id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm" onclick="deletePercent(<?php echo $pn['pn_id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <!-- ปุ่มแก้ไขและลบที่ถูกปิด - สำหรับผู้ที่ไม่ใช่เจ้าของ -->
                                                    <button class="btn btn-secondary btn-sm" disabled title="คุณไม่มีสิทธิ์แก้ไขรายการนี้">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-secondary btn-sm" disabled title="คุณไม่มีสิทธิ์ลบรายการนี้">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
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
    <?php include 'modal/percent_necklace_management_modal.php'; ?>

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
    <script src="js/percent_necklace_management.js"></script>

</body>

</html>