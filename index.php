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
require 'functions/management_ratio.php';
require 'functions/management_necklace_detail.php';

// ==========================
// ตัวแปรพื้นฐาน
// ==========================
$PI_VALUE = 3.1415926535897932; // ค่าคงที่ของ Pi

$selected_necklace          = null; // รายละเอียดของลายสร้อยที่เลือกจากฐานข้อมูล
$wire_weight_no_copper      = 0; // น้ำหนักลวดที่ไม่มีส่วนผสมของทองแดง
$ratio_weight_no_copper     = 0; // อัตราส่วนน้ำหนักที่ไม่มีทองแดง
$total_weight               = 0; // น้ำหนักรวมของสร้อย
$solid_wire_weight          = 0; // น้ำหนักลวดตัน
$solid_hollow_wire_ratio    = 0; // อัตราส่วนของน้ำหนักลวดตัน/โปร่ง
$solid_wire_weight_1_inch   = 0; // นน.ลวดตัน1.0"
$hollow_wire_weight_1_inch  = 0; // นน.ลวดโปร่ง1.0"
$hollow_wire_length         = 1; // ความยาวของลวดโปร่งเริ่มต้น (ค่าคงที่เริ่มต้นคือ 1 นิ้ว)
$hollow_wire_weight         = 0; // น้ำหนักของลวดโปร่งที่ใช้จริง
$wire_length                = 0; // ความยาวลวดทั้งหมดที่ต้องใช้
$use_wire_length            = 0; // ความยาวลวดที่ใช้จริงหลังจากปรับตาม TBS
$ratio_solid_wire           = 0; // ค่าคำนวณน้ำหนักของลวดตันตามอัตราส่วน
$ratio_solid_hollow         = 0; // อัตราส่วนของน้ำหนักลวดตันเทียบกับลวดโปร่ง (จาก Ratio)
$total_solid                = 0; // น้ำหนักรวมของลวดตันที่ใช้จริง
$total_size                 = 0; // ขนาดรวมของลวดตันที่ใช้จริง
$width_size                 = 0; // ขนาดหน้ากว้างของลวดตันที่ใช้จริง
$thick_size                 = 0; // ขนาดความหนาของลวดตันที่ใช้จริง

// ==========================
// ตัวแปรสำหรับ TBS
// ==========================
$tbs_name   = []; // ชื่อ TBS
$tbs_before = []; // น้ำหนักก่อน TBS
$tbs_after  = []; // น้ำหนักหลัง TBS

// ==========================
// ดึงข้อมูลจากฐานข้อมูล
// ==========================
$user_dept = isset($_SESSION['recipenecklace_users_depart']) ? $_SESSION['recipenecklace_users_depart'] : '';
if ($_SESSION['recipenecklace_users_level'] === 'Admin' || $user_dept === 'SG' || $user_dept === 'YS') {
    $necklace_all_details = get_necklace_all_details($pdo);
} else {
    $necklace_all_details = get_necklace_details_by_user($pdo, $_SESSION['recipenecklace_users_id']);
}
$ratio_data           = get_ratio_data($pdo);
$gold_types           = gold_type($pdo);
$ratioBy_id           = get_ratio_By_id($pdo, $_POST['ratio']);

$tbs_data = !empty($_POST['necklace_name']) ? getnecklace_tbs_Byid($pdo, $_POST['necklace_name']) : [];

// ==========================
// ดึงข้อมูลจากฟอร์ม
// ==========================
$post_weight     = isset($_POST['weight']) ? floatval($_POST['weight']) : 0;
$post_length     = isset($_POST['necklace_length']) ? floatval($_POST['necklace_length']) : 0;
$post_gold_type  = $_POST['gold_type'] ?? '';
$post_ratio      = $_POST['ratio'] ?? '';

// ==========================
// เริ่มคำนวณ
// ==========================
if (!empty($_POST['necklace_name'])) {
    $selected_necklace = get_necklace_detail_by_id($pdo, $_POST['necklace_name']);

    if ($selected_necklace && !empty($post_gold_type)) {
        $wire_weight_no_copper = $selected_necklace['agpt_core'] / $selected_necklace['ptt_ratio'];
        $gold_density = $post_gold_type;

        $solid_wire_weight = $gold_density * $PI_VALUE * (pow(($selected_necklace['agpt_thick'] / 10), 2) / 4)
            * $hollow_wire_length * 3.7 * $selected_necklace['agpt_ratio'];

        $solid_hollow_wire_ratio   = $solid_wire_weight / $wire_weight_no_copper;
        $solid_wire_weight_1_inch  = $solid_wire_weight / $selected_necklace['agpt_ratio'];
        $hollow_wire_weight_1_inch = $wire_weight_no_copper / $selected_necklace['agpt_ratio'];

        $hollow_wire_weight = ($selected_necklace['type'] == 'ตัน')
            ? $solid_wire_weight_1_inch
            : $hollow_wire_weight_1_inch;

        $wire_length = ($hollow_wire_length / $selected_necklace['true_length'])
            * ($selected_necklace['true_weight'] / $hollow_wire_weight);

        // ดึง TBS
        foreach ($tbs_data as $index => $tbs) {
            $tbs_name[$index]   = $tbs['tbs_name'];
            $tbs_before[$index] = floatval($tbs['tbs_before']);
            $tbs_after[$index]  = floatval($tbs['tbs_after']);
        }

        $ratio_weight_no_copper = $ratioBy_id['ratio_gram'] / $ratioBy_id['ratio_data'];
        $ratio_solid_wire = $post_gold_type * $PI_VALUE * (pow(($ratioBy_id['ratio_size'] / 10), 2) / 4)
            * 3.7 * $ratioBy_id['ratio_inch'];
        $ratio_solid_hollow = $ratio_solid_wire / $ratio_weight_no_copper;

        // ปรับความยาวด้วย TBS
        $tbs_factor = 1;
        if (
            isset($tbs_before[0], $tbs_before[1], $tbs_after[0], $tbs_after[1]) &&
            $tbs_before[0] != 0 && $tbs_before[1] != 0
        ) {
            $factor1    = ($tbs_after[0] - $tbs_before[0]) / $tbs_before[0];
            $factor2    = ($tbs_after[1] - $tbs_before[1]) / $tbs_before[1];
            $tbs_factor = 1 + $factor1 + $factor2 + ($factor1 * $factor2);
        }

        $production_length = $post_length / $tbs_factor;
        $use_wire_length   = $production_length * $wire_length;

        $per_wire    = $post_weight / $use_wire_length;
        $total_solid = $per_wire * $ratio_solid_hollow;

        // ขนาดรู, หน้ากว้าง, ความหนา
        $total_size = sqrt($total_solid / ($post_gold_type * $PI_VALUE * 0.25 * pow(0.1, 2) * 3.7));
        $width_size = $selected_necklace['ratio_width'] * $total_size;
        $thick_size = $selected_necklace['ratio_thick'] * $total_size;
    }
}

// ==========================
// ฟังก์ชัน: ดึงข้อมูลประเภททอง
// ==========================
function gold_type($pdo)
{
    $stmt = $pdo->prepare("SELECT `gold_type_id`, `gold_percentage`, `gold_density` FROM `gold_type` WHERE 1");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สูตรฮั้วสร้อย</title>
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
        .wire-size-visualization {
            margin: 20px auto;
            text-align: center;
        }

        .wire-size-visualization svg {
            max-width: 100%;
            height: auto;
            background: #fcfcfc;
            border: 1px solid #e9ecef;
        }

        .svg-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
        }

        /* เพิ่มสีพื้นหลังอ่อนๆ สำหรับ cards */
        .card {
            border-color: #e9ecef;
        }

        .bg-primary-subtle {
            background-color: #cfe2ff !important;
        }

        .bg-info-subtle {
            background-color: #cff4fc !important;
        }

        .bg-warning-subtle {
            background-color: #fff3cd !important;
        }

        .bg-secondary-subtle {
            background-color: #e9ecef !important;
        }

        .bg-success-subtle {
            background-color: #d1e7dd !important;
        }


        @media print {
            .wire-size-visualization svg {
                transform: scale(1);
                transform-origin: center;
            }
        }

        @media (max-width: 768px) {
            .svg-container {
                padding: 5px;
            }
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
                <!-- <div class="page-header">
                    <div class="row">
                        <div class="col">
                            <h3 class="page-title">รายการงาน</h3>
                        </div>
                    </div>
                </div> -->
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="necklace_name">ลายสร้อย</label>
                                        <select name="necklace_name" id="necklace_name" class="form-select" required>
                                            <option value="">-- เลือกลายสร้อย --</option>
                                            <?php
                                            foreach ($necklace_all_details as $necklace) {
                                                $updated_date = date('d/m/Y', strtotime($necklace['updated_at']));
                                                $selected = (isset($_POST['necklace_name']) && $_POST['necklace_name'] == $necklace['necklace_detail_id']) ? 'selected' : '';
                                                echo '<option value="' . $necklace['necklace_detail_id'] . '" ' . $selected . '>' . $necklace['name'] . ' ' . $necklace['first_name'] . ' (' . $updated_date . ')' . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="weight">น้ำหนัก(เฉพาะสร้อย) (กรัม)</label>
                                        <input type="number"
                                            name="weight"
                                            id="weight"
                                            placeholder="ระบุน้ำหนัก"
                                            class="form-control"
                                            value="<?php echo isset($_POST['weight']) ? htmlspecialchars($_POST['weight']) : ''; ?>"
                                            required
                                            step="0.01"
                                            min="0" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="necklace_length">ความยาว(เฉพาะสร้อย) (นิ้ว)</label>
                                        <input type="number"
                                            name="necklace_length"
                                            id="necklace_length"
                                            placeholder="ระบุความยาว"
                                            class="form-control"
                                            value="<?php echo isset($_POST['necklace_length']) ? htmlspecialchars($_POST['necklace_length']) : ''; ?>"
                                            required
                                            step="0.01"
                                            min="0" />
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="gold_type">ประเภททอง</label>
                                        <select name="gold_type" id="gold_type" class="form-select" required>
                                            <option value="">-- เลือกประเภททอง --</option>
                                            <?php
                                            foreach ($gold_types as $gold_type) {
                                                $selected = (isset($_POST['gold_type']) && $_POST['gold_type'] == $gold_type['gold_density']) ? 'selected' : '';
                                                echo '<option value="' . $gold_type['gold_density'] . '" ' . $selected . '>' . $gold_type['gold_percentage'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ratio">ความหนา</label>
                                        <select name="ratio" id="ratio" class="form-select" required>
                                            <option value="">-- เลือกความหนา --</option>
                                            <?php
                                            foreach ($ratio_data as $ratio) {
                                                $updated_date = date('d/m/Y', strtotime($ratio['updated_at']));
                                                $selected = (isset($_POST['ratio']) && $_POST['ratio'] == $ratio['ratio_id']) ? 'selected' : '';
                                                echo '<option value="' . $ratio['ratio_id'] . '" ' . $selected . '>' . $ratio['ratio_thick'] . ' (อัตราส่วน :  ' . $ratio['ratio_data'] . ' )' . $ratio['first_name'] . ' (' . $updated_date . ')' . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-calculator"></i> คำนวณ
                                    </button>
                                </div>
                            </div>
                        </form>

                        <?php if ($selected_necklace): ?>
                            <div id="necklaceDetails" class="mt-4">
                                <?php if (!empty($selected_necklace['image'])): ?>
                                    <div class="text-center mb-3">
                                        <img src="uploads/img/necklace_detail/<?php echo htmlspecialchars($selected_necklace['image']); ?>" alt="Necklace Image" class="img-fluid" style="max-width: 300px; height: auto;">
                                    </div>
                                <?php else: ?>
                                    <div class="text-center mb-3">
                                        <img src="uploads/img/noimage.webp" alt="No Image" class="img-fluid" style="max-width: 300px; height: auto;">
                                    </div>
                                <?php endif; ?>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-secondary" id="toggleDetailsBtn">
                                            <i class="fas fa-chevron-down"></i> แสดงรายละเอียดการคำนวณ
                                        </button>
                                    </div>
                                </div>
                                <div id="calculationDetails" style="display: none;">
                                    <!-- Card 1: ข้อมูลลวดที่ใช้ทำสร้อยต้นแบบ -->
                                    <div class="card mb-3">
                                        <div class="card-header bg-primary-subtle text-dark">
                                            <h5 class="card-title mb-0">ข้อมูลลวดที่ใช้ทำสร้อยต้นแบบ</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <td width="200"><strong>หนา:</strong></td>
                                                        <td><?php echo htmlspecialchars($selected_necklace['ptt_thick']); ?> มม.</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>ไส้:</strong></td>
                                                        <td><?php echo htmlspecialchars($selected_necklace['ptt_core']); ?> มม.</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>อัตราส่วน:</strong></td>
                                                        <td><?php echo htmlspecialchars($selected_necklace['ptt_ratio']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>ประเภทลวด:</strong></td>
                                                        <td><?php echo htmlspecialchars($selected_necklace['type']); ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card 2: ลวดอกาโฟโต้ -->
                                    <div class="card mb-3">
                                        <div class="card-header bg-info-subtle text-dark">
                                            <h5 class="card-title mb-0">ลวดอกาโฟโต้ (ยังไม่สกัด)</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <td width="200"><strong>รูลวด:</strong></td>
                                                        <td><?php echo htmlspecialchars($selected_necklace['agpt_thick']); ?> มม.</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>นน.ลวด ก่อนสกัด:</strong></td>
                                                        <td><?php echo htmlspecialchars($selected_necklace['agpt_core']); ?> กรัม</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>ความยาวลวด:</strong></td>
                                                        <td><?php echo htmlspecialchars($selected_necklace['agpt_ratio']); ?> นิ้ว</td>
                                                    </tr>

                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card 3: ข้อมูลการคำนวณ -->
                                    <div class="card mb-3">
                                        <div class="card-header bg-warning-subtle text-dark">
                                            <h5 class="card-title mb-0">ผลการคำนวณ</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <td><strong>นน.ลวด (ไม่มีทองแดง):</strong></td>
                                                        <td><?php echo number_format($wire_weight_no_copper, 2); ?> กรัม</td>
                                                    </tr>
                                                    <tr>
                                                        <td width="200"><strong>ลวดตัน:</strong></td>
                                                        <td><?php echo number_format($solid_wire_weight, 3); ?> กรัม</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>อัตราส่วน นน.ลวดตัน/ลวดโปร่ง:</strong></td>
                                                        <td><?php echo number_format($solid_hollow_wire_ratio, 2); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>นน.ลวดตัน1.0":</strong></td>
                                                        <td><?php echo number_format($solid_wire_weight_1_inch, 3); ?> กรัม</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>นน.ลวดโปร่ง1.0":</strong></td>
                                                        <td><?php echo number_format($hollow_wire_weight_1_inch, 3); ?> กรัม</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Card 4: ข้อมูลสร้อย ต้นแบบ -->
                                    <div class="card mb-3">
                                        <div class="card-header bg-secondary-subtle text-dark">
                                            <h5 class="card-title mb-0">ข้อมูลสร้อย ต้นแบบ</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <td><strong>ลวดโปร่ง ยาว</strong></td>
                                                        <td><?php echo htmlspecialchars($hollow_wire_length); ?> นิ้ว.</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>ลวดโปร่ง น้ำหนัก:</strong></td>
                                                        <td> <?php echo number_format($hollow_wire_weight, 3) . '  กรัม';  ?> </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>สร้อยโปร่ง ยาว:</strong></td>
                                                        <td><?php echo htmlspecialchars($selected_necklace['true_length']); ?> นิ้ว.</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>สร้อยโปร่ง น้ำหนัก:</strong></td>
                                                        <td><?php echo htmlspecialchars($selected_necklace['true_weight']); ?> กรัม</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" class="table-secondary"><strong>นิ้วได้นิ้ว</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>ความยาวลวด: <?php echo number_format($wire_length, 2) . '  นิ้ว';  ?> </strong></td>
                                                        <td><strong>ได้สร้อยยาว : 1 นิ้ว </strong></td>

                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card 5: ผลลัพธ์สุดท้าย -->
                                <div class="card mb-3">
                                    <div class="card-header bg-success-subtle text-dark">
                                        <h5 class="card-title mb-0">ผลลัพธ์สุดท้าย</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td colspan="6" class="text-center bg-success-subtle"><strong>ผลลัพธ์</strong></td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h3 class="text-success">จะได้ รูลวด:</h3>
                                                    </td>
                                                    <td colspan="4" class="text-success">
                                                        <h3><?php echo number_format($total_size, 2); ?> มม.</h3>
                                                    </td>
                                                </tr>
                                                <tr>
                                                <tr>
                                                    <td colspan="6" class="text-center bg-success-subtle"><strong>ขนาดสร้อย (คาดการณ์) </strong></td>
                                                </tr>
                                                <td colspan="6" class="text-center">
                                                    <div class="wire-size-visualization">
                                                        <!-- แสดงขนาดด้านบน SVG -->
                                                        <div class="mb-2">
                                                            <span class="me-4">
                                                                <strong>หน้ากว้าง:</strong>
                                                                <?php echo ($width_size == 0) ? '<span style="color:red">ไม่มีข้อมูล</span>' : number_format($width_size, 2) . ' มม.'; ?>
                                                            </span>
                                                            <span>
                                                                <strong>หนา:</strong>
                                                                <?php echo ($thick_size == 0) ? '<span style="color:red">ไม่มีข้อมูล</span>' : number_format($thick_size, 2) . ' มม.'; ?>
                                                            </span>
                                                        </div>
                                                        <!-- SVG แสดงรูปทรง -->
                                                        <div
                                                            class="svg-container"
                                                            style="width: 100%; max-width: 300px; margin: 0 auto;"
                                                            id="svg-mm-container"
                                                            data-width-mm="<?php echo htmlspecialchars($width_size); ?>"
                                                            data-thick-mm="<?php echo htmlspecialchars($thick_size); ?>"
                                                            data-shape="<?php echo htmlspecialchars($selected_necklace['shapeshape_necklace']); ?>">
                                                            <!-- SVG จะถูกสร้างด้วย JS -->
                                                            <svg id="wire-svg" viewBox="0 0 300 200" preserveAspectRatio="xMidYMid meet" style="width: 100%; height: auto; background: white; border: 1px solid #ddd;">
                                                                <!-- will be replaced by JS -->
                                                            </svg>
                                                        </div>
                                                        <div class="text-muted small mt-2">* แสดงในขนาดจริง</div>
                                                    </div>
                                                </td>
                                            </table>
                                        </div>
                                        <?php if (isset($selected_necklace['ratio_width']) && $selected_necklace['ratio_width'] > 0): ?>
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <button type="button" class="btn btn-secondary" id="toggleJiapongBtn">
                                                        <i class="fas fa-chevron-down"></i> แสดงตารางหลอดเจี่ยโป่ง
                                                    </button>
                                                </div>
                                            </div>
                                            <div id="jiapongDetails" style="display: none;">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <td colspan="6" class="text-center bg-success-subtle"><strong>หลอดเจี่ยโป่ง</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2"></td>
                                                            <td colspan="4" class="text-center bg-success-subtle" style="font-weight: bold;">ขนาดสร้อย (คาดการณ์)</td>
                                                        </tr>
                                                        <tr class="text-center">
                                                            <td style="font-weight: bold;">หนา</td>
                                                            <td style="font-weight: bold;">รูลวด</td>
                                                            <td class="text-center bg-primary-subtle" style="font-weight: bold;">หน้ากว้าง(มม.)</td>
                                                            <td class="text-center bg-info-subtle" style="font-weight: bold;">หนา(มม.)</td>
                                                        </tr>
                                                        <?php for ($i = 0.25; $i <= 1.35; $i += 0.05): ?>
                                                            <?php
                                                            $width_size_jiapong = (($post_weight / ($post_gold_type * $PI_VALUE * ($i / 10) * ($use_wire_length * 3.7))) + ($i / 10)) * 10;
                                                            $width_jiapong = $selected_necklace['ratio_width'] * $width_size_jiapong;
                                                            $thick_jiapong = $selected_necklace['ratio_thick'] * $width_size_jiapong;
                                                            ?>
                                                            <tr class="text-center">
                                                                <td><?php echo number_format($i, 2); ?></td>
                                                                <td><?php echo number_format($width_size_jiapong, 2); ?></td>
                                                                <td><?php echo number_format($width_jiapong, 2); ?></td>
                                                                <td><?php echo number_format($thick_jiapong, 2); ?></td>
                                                            </tr>
                                                        <?php endfor; ?>
                                                    </table>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        </table>
                    </div>
                </div>
                <?php if (isset($selected_necklace) && !empty($selected_necklace)) {
                    if ($selected_necklace['shapeshape_necklace'] === 'สี่เหลี่ยม') {
                        $thick_incaseofsize = $thick_size;
                    } else if ($selected_necklace['shapeshape_necklace'] === 'วงกลม') {
                        $thick_incaseofsize = $width_size;
                    }
                }
                ?>
                <!-- เริ่มส่วนคำนวณเผื่อไซต์สร้อย -->
                <?php if (isset($thick_incaseofsize) && $thick_incaseofsize > 0): ?>
                    <div class="card mb-3 mt-3">
                        <div class="card-header bg-warning-subtle text-dark">
                            <h5 class="card-title mb-0">คำนวณเผื่อไซต์สร้อย</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            // ประกาศตัวแปรสำหรับใช้ในการคำนวณเผื่อไซต์
                            $wrist_size = '';
                            $hook_size = '';
                            $necklace_size = $thick_incaseofsize;
                            $thick_necklace_result = '';
                            $necklace_length_result = '';
                            $calculation_performed = false;
                            $necklace_results = [];
                            $final_necklace_length_before_hook = 0;
                            $final_necklace_length_with_hook = 0;

                            // ตรวจสอบการส่งฟอร์มเฉพาะส่วนเผื่อไซต์
                            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['calculate_size'])) {
                                // รับค่าจากฟอร์ม
                                $wrist_size = isset($_POST['wrist_size']) ? (float)$_POST['wrist_size'] : 0;
                                $hook_size = isset($_POST['hook_size']) ? (float)$_POST['hook_size'] : 0;

                                // คำนวณค่า
                                if ($necklace_size > 0) {
                                    $thick_necklace_result = ($necklace_size / 10) / 3.7;
                                }

                                if ($wrist_size > 0 && $hook_size > 0) {
                                    $necklace_length_result = $wrist_size - $hook_size;
                                }

                                // คำนวณตามตาราง
                                if ($necklace_length_result > 0 && $thick_necklace_result > 0) {
                                    $PI = M_PI;  // ค่า PI จาก PHP

                                    // เก็บข้อมูลการคำนวณทั้งหมดในอาร์เรย์
                                    for ($i = 1.0; $i <= 2.0; $i += 0.1) {
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

                            <form method="POST" action="#size_calculation">
                                <a name="size_calculation"></a>
                                <input type="hidden" name="necklace_name" value="<?php echo isset($_POST['necklace_name']) ? htmlspecialchars($_POST['necklace_name']) : ''; ?>">
                                <input type="hidden" name="weight" value="<?php echo isset($_POST['weight']) ? htmlspecialchars($_POST['weight']) : ''; ?>">
                                <input type="hidden" name="necklace_length" value="<?php echo isset($_POST['necklace_length']) ? htmlspecialchars($_POST['necklace_length']) : ''; ?>">
                                <input type="hidden" name="gold_type" value="<?php echo isset($_POST['gold_type']) ? htmlspecialchars($_POST['gold_type']) : ''; ?>">
                                <input type="hidden" name="ratio" value="<?php echo isset($_POST['ratio']) ? htmlspecialchars($_POST['ratio']) : ''; ?>">
                                <input type="hidden" name="calculate_size" value="1">

                                <div class="alert alert-info">
                                    <strong>ความหนาสร้อย (รูลวด):</strong> <?php echo number_format($necklace_size, 2); ?> มม.
                                    <small>(ค่านี้นำมาจากการคำนวณข้างต้น)</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="wrist_size">ข้อมือคนใส่ (นิ้ว)<span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control" id="wrist_size" name="wrist_size"
                                                placeholder="กรอกข้อมือคนใส่ (นิ้ว)" value="<?php echo htmlspecialchars($wrist_size); ?>" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="hook_size">ความยาวตะขอ (นิ้ว)<span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control" id="hook_size" name="hook_size"
                                                placeholder="กรอกความยาวตะขอ (นิ้ว)" value="<?php echo htmlspecialchars($hook_size); ?>" required />
                                        </div>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <button type="submit" class="btn btn-primary">คำนวณเผื่อไซต์</button>
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

                                <div class="row mb-3 mt-3">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-secondary" id="toggleSizeDetailsBtn">
                                            <i class="fas fa-chevron-down"></i> แสดงรายละเอียดการคำนวณ
                                        </button>
                                    </div>
                                </div>

                                <div id="sizeCalculationDetails" style="display: none;">
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
                <?php endif; ?>
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
                $(document).ready(function() {
                    $('#necklace_name, #gold_type, #ratio').select2();

                    // ฟังก์ชัน toggle แสดง/ซ่อนรายละเอียด
                    function bindToggleButton(buttonSelector, detailsSelector, showText, hideText) {
                        $(buttonSelector).click(function() {
                            const detailsDiv = $(detailsSelector);
                            const icon = $(this).find('i');

                            if (detailsDiv.is(':visible')) {
                                detailsDiv.slideUp();
                                icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                                $(this).html(`<i class="fas fa-chevron-down"></i> ${showText}`);
                            } else {
                                detailsDiv.slideDown();
                                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                                $(this).html(`<i class="fas fa-chevron-up"></i> ${hideText}`);
                            }
                        });
                    }

                    // เรียกใช้ฟังก์ชันกับแต่ละปุ่ม
                    bindToggleButton('#toggleDetailsBtn', '#calculationDetails', 'แสดงรายละเอียดการคำนวณ', 'ซ่อนรายละเอียดการคำนวณ');
                    bindToggleButton('#toggleSizeDetailsBtn', '#sizeCalculationDetails', 'แสดงรายละเอียดการคำนวณ', 'ซ่อนรายละเอียดการคำนวณ');
                    bindToggleButton('#toggleJiapongBtn', '#jiapongDetails', 'แสดงตารางหลอดเจี่ยโป่ง', 'ซ่อนตารางหลอดเจี่ยโป่ง');

                    // โหลดรายละเอียดสร้อย
                    $('#necklace_name').on('change', function() {
                        const id = $(this).val();
                        if (!id) {
                            $('#necklaceDetails').hide();
                            return;
                        }

                        $.ajax({
                            url: 'api/get_necklace_detail.php',
                            type: 'GET',
                            data: {
                                id: id
                            },
                            success: function(response) {
                                if (response.success) {
                                    const data = response.data;
                                    $('#ptt_thick').text(data.ptt_thick);
                                    $('#ptt_core').text(data.ptt_core);
                                    $('#ptt_ratio').text(data.ptt_ratio);
                                    $('#type').text(data.type);
                                    $('#agpt_thick').text(data.agpt_thick);
                                    $('#agpt_core').text(data.agpt_core);
                                    $('#agpt_ratio').text(data.agpt_ratio);
                                    $('#necklaceDetails').show();
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: 'ไม่สามารถดึงข้อมูลได้'
                                });
                            }
                        });
                    });
                });

                function getPxPerMM() {
                    const div = document.createElement('div');
                    div.style.width = '1mm';
                    div.style.position = 'absolute';
                    div.style.visibility = 'hidden';
                    document.body.appendChild(div);
                    const pxPerMM = div.offsetWidth;
                    document.body.removeChild(div);
                    return pxPerMM;
                }

                // ฟังก์ชันวาด SVG ขนาดจริง
                function drawWireSVG() {
                    const container = document.getElementById('svg-mm-container');
                    if (!container) return;

                    const widthMM = parseFloat(container.dataset.widthMm) || 0;
                    const thickMM = parseFloat(container.dataset.thickMm) || 0;
                    const shape = container.dataset.shape || 'สี่เหลี่ยม';

                    const pxPerMM = getPxPerMM();

                    let widthPx = widthMM * pxPerMM;
                    let thickPx = thickMM * pxPerMM;

                    // กำหนดขนาด SVG
                    const centerX = 150;
                    const centerY = 100;
                    const maxSize = Math.min(centerX * 1.6, centerY * 1.6);

                    // ปรับ scale ให้พอดี
                    let scale = 1;
                    if (widthPx > maxSize || thickPx > maxSize) {
                        scale = maxSize / Math.max(widthPx, thickPx);
                        widthPx *= scale;
                        thickPx *= scale;
                    }

                    let svgContent = '';
                    if (shape === 'สี่เหลี่ยม') {
                        svgContent = `<rect x="${centerX - widthPx / 2}" y="${centerY - thickPx / 2}" width="${widthPx}" height="${thickPx}" stroke="#666" stroke-width="1" fill="#FFD700" />`;
                    } else if (shape === 'วงกลม') {
                        svgContent = `<circle cx="${centerX}" cy="${centerY}" r="${widthPx / 2}" stroke="#666" stroke-width="1" fill="#FFD700" />`;
                    }

                    document.getElementById('wire-svg').innerHTML = svgContent;
                }

                // เรียกวาด SVG เมื่อโหลดหน้า
                window.addEventListener('DOMContentLoaded', drawWireSVG);
                
            </script>

</body>

</html>