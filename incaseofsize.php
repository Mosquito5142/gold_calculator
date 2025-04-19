<?php
// ==========================
// แสดงข้อผิดพลาด PHP
// ==========================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ==========================
// รวมไฟล์ที่จำเป็น
// ==========================
require 'functions/check_login.php';
require 'config/db_connect.php';

// ==========================
// ประกาศตัวแปรสำหรับใช้ในการคำนวณ
// ==========================
$wrist_size = '';
$hook_size = '';
$necklace_size = '';
$thick_necklace_result = '';
$necklace_length_result = '';
$calculation_performed = false;
$necklace_results = [];
$final_necklace_length_before_hook = 0;
$final_necklace_length_with_hook = 0;

// ==========================
// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มหรือไม่
// ==========================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจากฟอร์ม
    $wrist_size = isset($_POST['wrist_size']) ? (float)$_POST['wrist_size'] : 0;
    $hook_size = isset($_POST['hook_size']) ? (float)$_POST['hook_size'] : 0;
    $necklace_size = isset($_POST['necklace_size']) ? (float)$_POST['necklace_size'] : 0;

    // คำนวณค่า
    if ($necklace_size > 0) {
        // แก้สูตรจาก / 10 เป็น * 10 ตามความต้องการ
        $thick_necklace_result = ($necklace_size / 10) / 3.7;
    }

    if ($wrist_size > 0 && $hook_size > 0) {
        $necklace_length_result = $wrist_size - $hook_size;
    }

    // คำนวณตามตาราง
    if ($necklace_length_result > 0 && $thick_necklace_result > 0) {
        $PI = M_PI;  // ค่า PI จาก PHP

        // เก็บข้อมูลการคำนวณทั้งหมดในอาร์เรย์
        for ($i = 1.0; $i < 2.1; $i += 0.1) {
            $ratio = round($i, 1); // ปัดเศษให้เป็น 1 ตำแหน่ง

            // คำนวณค่าตามสูตรที่ให้มา
            $b = sqrt((pow(($necklace_length_result / (2 * $PI)), 2) * 2) / (pow($ratio, 2) + 1));
            $size_allowance = pow(($ratio * $b) + ($thick_necklace_result / 2), 2);
            $question_mark = pow($b + ($thick_necklace_result / 2), 2);
            $necklace_before_hook = sqrt(($size_allowance + $question_mark) / 2) * (2 * $PI);
            $necklace_with_hook = $necklace_before_hook + $hook_size;

            // เก็บผลลัพธ์ไว้ในอาร์เรย์
            $necklace_results[] = [
                'ratio' => $ratio,
                'b' => $b,
                'size_allowance' => $size_allowance,
                'question_mark' => $question_mark,
                'necklace_before_hook' => $necklace_before_hook,
                'necklace_with_hook' => $necklace_with_hook
            ];
        }

        // คำนวณค่าเฉลี่ยของค่าแรกและค่าสุดท้าย
        if (count($necklace_results) > 0) {
            $first = $necklace_results[0];
            $last = $necklace_results[count($necklace_results) - 1];

            $final_necklace_length_before_hook = ($first['necklace_before_hook'] + $last['necklace_before_hook']) / 2;
            $final_necklace_length_with_hook = ($first['necklace_with_hook'] + $last['necklace_with_hook']) / 2;
        }
    }

    $calculation_performed = true;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คำนวณเผื่อไซต์สร้อย</title>
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
        .result-box {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #17a2b8;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .calculation-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .calculation-table th,
        .calculation-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: center;
        }

        .calculation-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .calculation-table tr:hover {
            background-color: #f1f1f1;
        }

        .final-result {
            margin-top: 20px;
            padding: 20px;
            border-radius: 5px;
            background-color: #e9ecef;
            border-left: 5px solid #28a745;
        }

        .formula {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
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
                <div class="page-header">
                    <div class="row">
                        <div class="col">
                            <h3 class="page-title">คำนวณเผื่อไซต์สร้อย</h3>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="wrist_size">ข้อมือคนใส่ (นิ้ว)<span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" id="wrist_size" name="wrist_size"
                                            placeholder="กรอกข้อมือคนใส่ (นิ้ว)" value="<?php echo htmlspecialchars($wrist_size); ?>" required />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="hook_size">ความยาวตะขอ (นิ้ว)<span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" id="hook_size" name="hook_size"
                                            placeholder="กรอกความยาวตะขอ (นิ้ว)" value="<?php echo htmlspecialchars($hook_size); ?>" required />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="necklace_size">ความหนาสร้อย (มม.)<span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" id="necklace_size" name="necklace_size"
                                            placeholder="กรอกความหนาสร้อย (มม.)" value="<?php echo htmlspecialchars($necklace_size); ?>" required />
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary">คำนวณ</button>
                                </div>
                            </div>
                        </form>

                        <?php if ($calculation_performed): ?>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-header bg-primary text-white" style="border-radius: 5px;">
                                            <h5 class="mb-0">ค่าพื้นฐาน</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="result-box">
                                                        <h5>สร้อยหนา</h5>
                                                        <h4 class="text-primary"><?php echo number_format($thick_necklace_result, 2); ?> นิ้ว</h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="result-box">
                                                        <h5>ความยาวสร้อย (มือคน-ตะขอ)</h5>
                                                        <h4 class="text-primary"><?php echo number_format($necklace_length_result, 2); ?> นิ้ว</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <button type="button" class="btn btn-secondary" id="toggleincaseofsizenDetailssBtn">
                                        <i class="fas fa-chevron-down"></i> แสดงรายละเอียดการคำนวณ
                                    </button>
                                </div>
                            </div>
                            <div id="incaseofsizenDetails" style="display: none;">
                                <div class="mt-4">
                                    <h5 class="text-primary">ตารางคำนวณเผื่อไซต์</h5>
                                    <div class="table-responsive">
                                        <table class="table table-striped calculation-table">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>ratio(a/b)</th>
                                                    <th>b</th>
                                                    <th>เผื่อไซส์(นิ้ว)</th>
                                                    <th>?</th>
                                                    <th>ทำสร้อย (ก่อนใส่ตะขอ) หน่วย: นิ้ว</th>
                                                    <th>ค.ยาวรวมตะขอ(นิ้ว)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($necklace_results as $result): ?>
                                                    <tr>
                                                        <td><?php echo number_format($result['ratio'], 2); ?></td>
                                                        <td><?php echo number_format($result['b'], 2); ?></td>
                                                        <td><?php echo number_format($result['size_allowance'], 2); ?></td>
                                                        <td><?php echo number_format($result['question_mark'], 2); ?></td>
                                                        <td><?php echo number_format($result['necklace_before_hook'], 2); ?></td>
                                                        <td><?php echo number_format($result['necklace_with_hook'], 2); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="final-result">
                                        <h4 class="text-success">ผลลัพธ์สุดท้าย</h4>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="result-box">
                                                    <h5>ความยาวสร้อย (ก่อนใส่ตะขอ)</h5>
                                                    <h4 class="text-primary"><?php echo number_format($final_necklace_length_before_hook, 2); ?> นิ้ว</h4>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="result-box">
                                                    <h5>ความยาวรวมตะขอ</h5>
                                                    <h4 class="text-primary"><?php echo number_format($final_necklace_length_with_hook, 2); ?> นิ้ว</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/feather.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
    <script src="assets/plugins/sweetalert/sweetalerts.min.js"></script>
    <script src="assets/plugins/select2/js/select2.min.js"></script>
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="assets/js/touchmove_table.js"></script>
    <script>
        // เพิ่ม function สำหรับปุ่มแสดง/ซ่อนรายละเอียด
        $('#toggleincaseofsizenDetailssBtn').click(function() {
            const detailsDiv = $('#incaseofsizenDetails');
            const icon = $(this).find('i');

            if (detailsDiv.is(':visible')) {
                detailsDiv.slideUp();
                icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                $(this).html('<i class="fas fa-chevron-down"></i> แสดงรายละเอียดการคำนวณ');
            } else {
                detailsDiv.slideDown();
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                $(this).html('<i class="fas fa-chevron-up"></i> ซ่อนรายละเอียดการคำนวณ');
            }
        });
    </script>
</body>

</html>