<?php
require 'functions/check_login.php';
require 'config/db_connect.php';
require 'functions/management_percent_necklace.php';

$percent_necklaces = get_percent_necklace($pdo);
$current_user_id = isset($_SESSION['recipenecklace_users_id']) ? $_SESSION['recipenecklace_users_id'] : 0;
$is_admin = isset($_SESSION['recipenecklace_users_level']) && $_SESSION['recipenecklace_users_level'] === 'Admin';
$user_dept = isset($_SESSION['recipenecklace_users_depart']) ? $_SESSION['recipenecklace_users_depart'] : '';
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

        /* การไฮไลท์ช่องกว้างที่เป็นค่าอ้างอิง */
        .reference-width {
            background-color: #b3e0ff !important;
            /* สีฟ้าอ่อน */
            border: 2px solid #66b3ff !important;
            box-shadow: 0 0 5px rgba(102, 179, 255, 0.5);
        }

        .reference-width::after {
            content: " (ค่าอ้างอิง)";
            font-size: 0.8em;
            color: #007bff;
            font-style: italic;
        }

        /* สำหรับการรวมมัลติ */
        .multi-reference {
            background-color: #b3e0ff !important;
            /* สีฟ้าอ่อน */
            border: 2px solid #66b3ff !important;
            position: relative;
        }

        .multi-reference-label {
            position: absolute;
            bottom: -18px;
            right: 0;
            font-size: 0.8em;
            color: #007bff;
            background: #f8f9fa;
            padding: 1px 3px;
            border-radius: 3px;
            border: 1px solid #dee2e6;
        }

        hr {
            border: 0;
            height: 1px;
            background-color: rgb(0, 0, 0);
        }

        @media (max-width: 767.98px) {
            table#percent_necklace thead {
                display: none;
            }

            table#percent_necklace tbody tr {
                display: block;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 10px;
                background-color: #fff;
            }

            table#percent_necklace tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 10px;
                border: none;
                border-bottom: 1px solid #eee;
            }

            table#percent_necklace tbody td:last-child {
                border-bottom: none;
            }

            table#percent_necklace tbody td::before {
                content: attr(data-label);
                font-weight: bold;
                flex: 1;
                color: #333;
                margin-right: 10px;
            }

            table#percent_necklace tbody td img {
                max-height: 100px;
                height: auto;
            }
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
                                        <th>วันที่บันทึก</th>
                                        <th>ผู้บันทึก</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($percent_necklaces as $pn): ?>
                                        <tr>
                                            <td class="text-center" data-label="รูป">
                                                <?php if (!empty($pn['image'])): ?>
                                                    <img src="uploads/img/percent_necklace/<?php echo htmlspecialchars($pn['image']); ?>"
                                                        class="img-thumbnail" style="max-height: 50px; width: auto;"
                                                        onclick="showFullImage('uploads/img/percent_necklace/<?php echo htmlspecialchars($pn['image']); ?>', '<?php echo htmlspecialchars($pn['pn_name']); ?>')">
                                                <?php else: ?>
                                                    <i class="fas fa-image text-muted"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="ชื่อ"><?php echo htmlspecialchars($pn['pn_name']); ?></td>
                                            <td data-label="น้ำหนัก (กรัม)"><?php echo htmlspecialchars($pn['pn_grams']); ?></td>
                                            <td data-label="วันที่บันทึก"><?php echo htmlspecialchars($pn['updated_at']); ?></td>
                                            <td data-label="ผู้บันทึก"><?php echo htmlspecialchars($pn['first_name']); ?></td>
                                            <td data-label="จัดการ">
                                                <!-- ปุ่มรูปตาเอาไว้ดูข้อมูล (ทุกคนสามารถดูได้) -->
                                                <button class="btn btn-info btn-sm" onclick="viewPercent(<?php echo $pn['pn_id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <?php if ($pn['users_id'] == $current_user_id || $is_admin || $user_dept === 'หัวหน้าช่าง'): ?>
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
    <?php include 'modal/imageModal.php'; ?>
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