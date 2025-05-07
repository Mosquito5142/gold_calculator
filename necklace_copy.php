<?php
session_start();
require 'functions/check_login.php';
require 'config/db_connect.php';
require 'functions/management_necklace_detail.php';

// ดึงข้อมูลทั้งหมดมาแสดง (เปลี่ยนจาก percent_necklace เป็น necklace_detail)
$user_dept = isset($_SESSION['recipenecklace_users_depart']) ? $_SESSION['recipenecklace_users_depart'] : '';
$current_user_id = isset($_SESSION['recipenecklace_users_id']) ? $_SESSION['recipenecklace_users_id'] : 0;
$is_admin = $_SESSION['recipenecklace_users_level'] === 'Admin';

if ($_SESSION['recipenecklace_users_level'] === 'Admin' || $user_dept === 'SG' || $user_dept === 'YS' || $user_dept === 'หัวหน้าช่าง') {
    $necklace_copies = get_necklace_all_copy($pdo);
} else {
    $necklace_copies = get_necklace_all_copy_by_user($pdo, $current_user_id);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการสูตรฮั้วสร้อยที่บันทึกไว้</title>
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
        .badge-copy {
            background-color: #17a2b8;
            color: white;
        }

        @media (max-width: 767.98px) {
            #necklace_copy_table thead {
                display: none;
            }

            #necklace_copy_table tbody tr {
                display: block;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 10px;
                background-color: #fff;
            }

            #necklace_copy_table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 10px;
                border: none;
                border-bottom: 1px solid #eee;
            }

            #necklace_copy_table tbody td:last-child {
                border-bottom: none;
            }

            #necklace_copy_table tbody td::before {
                content: attr(data-label);
                font-weight: bold;
                flex: 1;
                color: #333;
                margin-right: 10px;
            }

            #necklace_copy_table tbody td img {
                max-height: 80px;
                height: auto;
            }

            #necklace_copy_table .badge-copy {
                display: inline-block;
                margin-top: 5px;
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
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4>รายการสูตรฮั้วสร้อยที่บันทึกไว้</h4>
                            <a href="index.php" class="btn btn-primary">
                                <i class="fas fa-calculator"></i> กลับไปหน้าคำนวณ
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="necklace_copy_table" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="60">รูป</th>
                                        <th>ชื่อลายสร้อย</th>
                                        <th>ประเภท</th>
                                        <th>น้ำหนัก (กรัม)</th>
                                        <th>ความยาว (นิ้ว)</th>
                                        <th>อัตราส่วน</th>
                                        <th>หมายเหตุ</th>
                                        <th>วันที่บันทึก</th>
                                        <th>ผู้บันทึก</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($necklace_copies as $necklace): ?>
                                        <tr>
                                            <td class="text-center" data-label="รูป">
                                                <?php if (!empty($necklace['image'])): ?>
                                                    <img src="uploads/img/necklace_detail/<?php echo htmlspecialchars($necklace['image']); ?>"
                                                        class="img-thumbnail" style="max-height: 50px; width: auto;"
                                                        onclick="showFullImage('uploads/img/necklace_detail/<?php echo htmlspecialchars($necklace['image']); ?>', '<?php echo htmlspecialchars($necklace['name']); ?>')">
                                                <?php else: ?>
                                                    <i class="fas fa-image text-muted"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="ชื่อลายสร้อย">
                                                <?php echo htmlspecialchars($necklace['name']); ?>
                                                <span class="badge badge-copy">สำเนา</span>
                                            </td>
                                            <td data-label="ประเภท"><?php echo htmlspecialchars($necklace['type']); ?></td>
                                            <td data-label="น้ำหนัก (กรัม)"><?php echo !empty($necklace['calc_weight']) ? htmlspecialchars($necklace['calc_weight']) : htmlspecialchars($necklace['true_weight']); ?></td>
                                            <td data-label="ความยาว (นิ้ว)"><?php echo !empty($necklace['calc_length']) ? htmlspecialchars($necklace['calc_length']) : htmlspecialchars($necklace['true_length']); ?></td>
                                            <td data-label="อัตราส่วน"><?php echo htmlspecialchars($necklace['ratio_data'] ?? ''); ?></td>
                                            <td data-label="หมายเหตุ"><?php echo htmlspecialchars($necklace['comment'] ?? ''); ?></td>
                                            <td data-label="วันที่บันทึก"><?php echo date('d/m/Y H:i', strtotime($necklace['updated_at'])); ?></td>
                                            <td data-label="ผู้บันทึก"><?php echo htmlspecialchars($necklace['first_name'] ?? 'ไม่ระบุ'); ?></td>
                                            <td data-label="จัดการ">
                                                <button class="btn btn-success btn-sm"
                                                    onclick="selectNecklace(<?php echo $necklace['necklace_detail_id']; ?>, 
                                            '<?php echo !empty($necklace['calc_weight']) ? $necklace['calc_weight'] : $necklace['true_weight']; ?>', 
                                            '<?php echo !empty($necklace['calc_length']) ? $necklace['calc_length'] : $necklace['true_length']; ?>', 
                                            '<?php echo !empty($necklace['gold_type']) ? $necklace['gold_type'] : ''; ?>', 
                                            '<?php echo !empty($necklace['ratio_id']) ? $necklace['ratio_id'] : ''; ?>')">
                                                    <i class="fas fa-check"></i> คำนวณ
                                                </button>

                                                <?php if ($current_user_id == $necklace['updated_users_id'] || $is_admin || $user_dept === 'หัวหน้าช่าง'): ?>
                                                    <button type="button" class="btn btn-warning btn-sm" onclick="editNecklace(<?php echo $necklace['necklace_detail_id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm" onclick="deleteNecklaceCopy(<?php echo $necklace['necklace_detail_id']; ?>)">
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
    <?php include 'modal/necklace_detail_modal.php'; ?>

    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/feather.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
    <script src="assets/plugins/sweetalert/sweetalerts.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="js/necklace-crud.js"></script>
    <script src="js/necklace-form.js"></script>
    <script>
        $(document).ready(function() {
            // เปิดใช้งาน DataTable
            $('#necklace_copy_table').DataTable({
                "language": {
                    "lengthMenu": "แสดง _MENU_ รายการต่อหน้า",
                    "zeroRecords": "ไม่พบข้อมูล",
                    "info": "แสดงหน้า _PAGE_ จาก _PAGES_",
                    "infoEmpty": "ไม่มีข้อมูล",
                    "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)",
                    "search": "ค้นหา:",
                    "paginate": {
                        "first": "หน้าแรก",
                        "last": "หน้าสุดท้าย",
                        "next": "ถัดไป",
                        "previous": "ก่อนหน้า"
                    }
                },
                "order": [
                    [7, "desc"]
                ] // เรียงตามวันที่บันทึกล่าสุด
            });
            // ฟังก์ชันแสดงรูปภาพเต็ม
            window.showFullImage = function(imageSrc, title) {
                $('#fullImage').attr('src', imageSrc);
                $('#imageModalTitle').text(title);
                $('#imageModal').modal('show');
            };
        });

        // ฟังก์ชันเลือกสร้อย
        function selectNecklace(necklace_id, weight, length, gold_type, ratio_id) {
            // ส่งผู้ใช้ไปยังหน้า index.php พร้อมส่งค่าพารามิเตอร์
            window.location.href = `necklace_calculator.php?necklace_id=${necklace_id}&weight=${weight}&length=${length}&gold_type=${gold_type}&ratio_id=${ratio_id}`;
        }

        // ฟังก์ชันลบสร้อย
        function deleteNecklaceCopy(necklace_id) {
            Swal.fire({
                title: 'ยืนยันการลบ',
                text: "คุณแน่ใจหรือไม่ที่จะลบรายการนี้? การลบไม่สามารถย้อนกลับได้",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ส่ง Ajax request เพื่อลบข้อมูล
                    $.ajax({
                        url: 'actions/delete_necklace_copy.php',
                        type: 'POST',
                        data: {
                            necklace_id: necklace_id
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'สำเร็จ!',
                                    text: 'ลบข้อมูลเรียบร้อยแล้ว',
                                    icon: 'success'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'เกิดข้อผิดพลาด!',
                                    text: response.message,
                                    icon: 'error'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'เกิดข้อผิดพลาด!',
                                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }
    </script>
</body>

</html>