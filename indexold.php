<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'functions/check_login.php';
require 'config/db_connect.php';
require 'functions/management_ratio.php';
require 'functions/management_necklace_detail.php';

// ===== ตัวแปรสำหรับการคำนวณ =====
$PI_VALUE = 3.1415926535897932; // ค่า PI
$selected_necklace = null;    // เก็บข้อมูลลายสร้อยที่เลือก
$wire_weight_no_copper = 0;   // น้ำหนักลวดที่ไม่มีทองแดง
$ratio_weight_no_copper = 0; // น้ำหนักอัตราลวดที่ไม่มีทองแดง
$total_weight = 0;           // น้ำหนักรวม
$solid_wire_weight = 0;      // น้ำหนักลวดตัน
$solid_hollow_wire_ratio = 0; // อัตราส่วนน้ำหนักลวดตันต่อลวดโปร่ง
$solid_wire_weight_1_inch = 0; // น้ำหนักลวดตัน 1.0 นิ้ว
$hollow_wire_weight_1_inch = 0; // น้ำหนักลวดโปร่ง 1.0 นิ้ว
$hollow_wire_length = 1; // ความยาวลวดโปร่ง 1.0 นิ้ว
$hollow_wire_weight = 0; // น้ำหนักลวดโปร่ง 1.0 นิ้ว
$wire_length = 0; // ความยาวลวด
$use_wire_length = 0; // ใช้ลวดยาว
$ratio_solid_wire = 0; // น้ำหนักลวดตัน
$ratio_solid_hollow = 0; // อัตราส่วน นน.ลวดตัน/ลวดโปร่ง
$total_solid = 0; // คิดเป็น ลวดตัน นน.
$total_size = 0; // จะได้ รูลวด
// คาดการขนาดสร้อย
$width_size = 0; // หน้ากว้าง
$thick_size = 0; // หนา
// ===== ตัวแปรสำหรับ TBS =====
$tbs_name = [];    // ชื่อ TBS
$tbs_before = [];  // ค่า before
$tbs_after = [];   // ค่า after
// ===== ตัวแปรจากฐานข้อมูล =====
$necklace_all_details = get_necklace_all_details($pdo);  // ข้อมูลลายสร้อยทั้งหมด
$ratio_data = get_ratio_data($pdo);                      // ข้อมูลอัตราส่วนทั้งหมด
$gold_types = gold_type($pdo);                           // ข้อมูลประเภททองทั้งหมด
$ratioBy_id = get_ratio_By_id($pdo, $_POST['ratio']); // ข้อมูลอัตราส่วนที่เลือก
$tbs_data = [];
if (isset($_POST['necklace_name']) && !empty($_POST['necklace_name'])) {
    $tbs_data = getnecklace_tbs_Byid($pdo, $_POST['necklace_name']);
}
// ===== ตัวแปรจากฟอร์ม =====
$post_weight = isset($_POST['weight']) ? floatval($_POST['weight']) : 0;           // น้ำหนักที่ผู้ใช้กรอก
$post_length = isset($_POST['necklace_length']) ? floatval($_POST['necklace_length']) : 0;  // ความยาวที่ผู้ใช้กรอก
$post_gold_type = isset($_POST['gold_type']) ? $_POST['gold_type'] : '';           // ประเภททองที่เลือก
$post_ratio = isset($_POST['ratio']) ? $_POST['ratio'] : '';                       // อัตราส่วนที่เลือก
// ดึงข้อมูลลายสร้อยที่เลือก
if (isset($_POST['necklace_name']) && !empty($_POST['necklace_name'])) {
    $selected_necklace = get_necklace_detail_by_id($pdo, $_POST['necklace_name']);

    // คำนวณเมื่อมีการเลือกลายสร้อยและประเภททอง
    if ($selected_necklace && !empty($_POST['gold_type'])) {
        $wire_weight_no_copper = $selected_necklace['agpt_core'] / $selected_necklace['ptt_ratio']; // คำนวณน้ำหนักลวดที่ไม่มีทองแดง
        $gold_density = $_POST['gold_type']; // ประเภททองที่เลือก
        $solid_wire_weight = $gold_density * $PI_VALUE * (pow(($selected_necklace['agpt_thick'] / 10), 2) / 4) * $hollow_wire_length * 3.7 * $selected_necklace['agpt_ratio']; // น้ำหนักลวดตัน
        $solid_hollow_wire_ratio = $solid_wire_weight / $wire_weight_no_copper; // อัตราส่วน นน.ลวดตัน/ลวดโปร่ง
        $solid_wire_weight_1_inch = $solid_wire_weight / $selected_necklace['agpt_ratio']; // อัตราส่วน นน.ลวดตัน/ลวดโปร่ง
        $hollow_wire_weight_1_inch = $wire_weight_no_copper / $selected_necklace['agpt_ratio']; // น้ำหนักลวดโปร่ง 1.0 นิ้ว
        // ตรวจสอบประเภทลวดและกำหนดน้ำหนักลวดโปร่ง 1.0 นิ้ว
        if ($selected_necklace['type'] == 'ตัน') {
            $hollow_wire_weight = $solid_wire_weight_1_inch; // น้ำหนักลวดตัน 1.0 นิ้ว
        } else {
            $hollow_wire_weight = $hollow_wire_weight_1_inch; // น้ำหนักลวดโปร่ง 1.0 นิ้ว
        }
        $wire_length = ($hollow_wire_length / $selected_necklace['true_length']) * ($selected_necklace['true_weight'] / $hollow_wire_weight);
        // ดึงและเก็บข้อมูล TBS
        if (!empty($tbs_data)) {
            foreach ($tbs_data as $index => $tbs) {
                $tbs_name[$index] = $tbs['tbs_name'];
                $tbs_before[$index] = floatval($tbs['tbs_before']);
                $tbs_after[$index] = floatval($tbs['tbs_after']);
            }
        }
        $ratio_weight_no_copper = $ratioBy_id['ratio_gram'] / $ratioBy_id['ratio_data'];
        $ratio_solid_wire = $post_gold_type * $PI_VALUE * (pow(($ratioBy_id['ratio_size'] / 10), 2) / 4) * 3.7 * $ratioBy_id['ratio_inch']; // น้ำหนักลวดตัน
        $ratio_solid_hollow = $ratio_solid_wire / $ratio_weight_no_copper;
    }
}

// ฟังก์ชันดึงข้อมูลประเภททอง
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
    <title>หน้าแรก</title>
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
                                                $selected = (isset($_POST['necklace_name']) && $_POST['necklace_name'] == $necklace['necklace_detail_id']) ? 'selected' : '';
                                                echo '<option value="' . $necklace['necklace_detail_id'] . '" ' . $selected . '>' . $necklace['name'] . '</option>';
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
                                        <label for="ratio">อัตราส่วน</label>
                                        <select name="ratio" id="ratio" class="form-select" required>
                                            <option value="">-- เลือกอัตราส่วน --</option>
                                            <?php
                                            foreach ($ratio_data as $ratio) {
                                                $selected = (isset($_POST['ratio']) && $_POST['ratio'] == $ratio['ratio_id']) ? 'selected' : '';
                                                echo '<option value="' . $ratio['ratio_id'] . '" ' . $selected . '>' . $ratio['ratio_thick'] . '</option>';
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



                        <?php if ($selected_necklace):
                            $wire_weight_no_copper = ($selected_necklace['agpt_core'] / $selected_necklace['ptt_ratio']);
                        ?>
                            <div id="necklaceDetails" class="mt-4">
                                <div class="card bg-light mb-3">
                                    <h5 class="card-title">ข้อมูลลวด ที่ใช้ทำสร้อยต้นแบบ</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td colspan="2" class="table-secondary"><strong>ข้อมูลลวด ที่ใช้ทำสร้อยต้นแบบ</strong></td>
                                                </tr>
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
                                                <tr>
                                                    <td colspan="2" class="table-secondary"><strong>ลวดอกาโฟโต้ (ยังไม่สกัด)</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>รูลวด:</strong></td>
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
                                                <tr class="table-warning">
                                                    <td><strong>นน.ลวด (ไม่มีทองแดง):</strong></td>
                                                    <td><?php echo number_format($wire_weight_no_copper, 2); ?> กรัม</td>
                                                </tr>
                                                <tr class="table-warning">
                                                    <td><strong>ลวดตัน:</strong></td>
                                                    <td><?php echo number_format($solid_wire_weight, 3) . ' กรัม'; ?></td>
                                                </tr>
                                                <!-- อัตราส่วน นน.ลวดตัน/ลวดโปร่ง -->
                                                <tr class="table-warning">
                                                    <td><strong>อัตราส่วน นน.ลวดตัน/ลวดโปร่ง:</strong></td>
                                                    <td> <?php echo number_format($solid_hollow_wire_ratio, 2); ?> </td>
                                                </tr>
                                                <!-- นน.ลวดตัน1.0" -->
                                                <tr class="table-warning">
                                                    <td><strong>นน.ลวดตัน1.0" :</strong></td>
                                                    <td> <?php echo number_format($solid_wire_weight_1_inch, 3) . ' กรัม';  ?> </td>
                                                </tr>
                                                <!-- นน.ลวดโปร่ง1.0" -->
                                                <tr class="table-warning">
                                                    <td><strong>นน.ลวดโปร่ง1.0" :</strong></td>
                                                    <td> <?php echo number_format($hollow_wire_weight_1_inch, 3) . ' กรัม';  ?> </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="table-secondary"><strong>ข้อมูลสร้อย ต้นแบบ</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>ลวดโปร่ง ยาว</strong></td>
                                                    <td><?php echo htmlspecialchars($hollow_wire_length); ?> นิ้ว.</td>
                                                    <td><strong>ลวดโปร่ง น้ำหนัก:</strong></td>
                                                    <td> <?php echo number_format($hollow_wire_weight, 3) . '  กรัม';  ?> </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>สร้อยโปร่ง ยาว:</strong></td>
                                                    <td><?php echo htmlspecialchars($selected_necklace['true_length']); ?> นิ้ว.</td>
                                                    <td><strong>สร้อยโปร่ง น้ำหนัก:</strong></td>
                                                    <td><?php echo htmlspecialchars($selected_necklace['true_weight']); ?> กรัม</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="table-secondary"><strong>นิ้วได้นิ้ว</strong></td>
                                                </tr>
                                                <tr class="table-info">
                                                    <td><strong>ความยาวลวด:</strong></td>
                                                    <td> <?php echo number_format($wire_length, 2) . '  นิ้ว';  ?> </td>
                                                    <td><strong>ได้สร้อยยาว :</strong></td>
                                                    <td>1 นิ้ว </td>
                                                </tr>
                                    </div>
                                </div>
                                <div class="card bg-light mb-3">
                                    <?php for ($i = 0; $i < count($tbs_name); $i++): ?>
                                        <tr>
                                            <td><strong>ความยาวสร้อยก่อน<?php echo htmlspecialchars($tbs_name[$i]); ?>:</strong></td>
                                            <td> <?php echo number_format($tbs_before[$i], 2) . '  นิ้ว';  ?> </td>
                                            <td><strong>ความยาวสร้อยหลัง<?php echo htmlspecialchars($tbs_name[$i]); ?> :</strong></td>
                                            <td> <?php echo number_format($tbs_after[$i], 2) . '  นิ้ว';  ?> </td>
                                            <td><strong><?php echo htmlspecialchars($tbs_name[$i]); ?> ยืดออก:</strong></td>
                                            <td> <?php echo number_format(($tbs_after[$i] - $tbs_before[$i]) / $tbs_before[$i], 2) . '  นิ้ว/สร้อย1"';  ?> </td>
                                        </tr>
                                    <?php endfor; ?>
                                    <tr>
                                        <td colspan="2" class="table-secondary"><strong>ผลิตสร้อยยาว</strong></td>
                                    </tr>
                                    <tr class="table-info">
                                        <?php
                                        $tbs_factor = 1 +
                                            (($tbs_after[0] - $tbs_before[0]) / $tbs_before[0]) +
                                            (($tbs_after[1] - $tbs_before[1]) / $tbs_before[1]) +
                                            ((($tbs_after[0] - $tbs_before[0]) / $tbs_before[0]) *
                                                (($tbs_after[1] - $tbs_before[1]) / $tbs_before[1]));
                                        $production_length = $post_length / $tbs_factor;
                                        $use_wire_length = $production_length * $wire_length; // ใช้ลวดยาว
                                        $per_wire = $post_weight / $use_wire_length; // ใช้ลวดยาว
                                        $total_solid = $per_wire * $ratio_solid_hollow; // คิดเป็น ลวดตัน นน.
                                        $total_size = sqrt($total_solid / ($post_gold_type * $PI_VALUE * (1 / 4) * pow((1 / 10), 2) * 3.7)); // จะได้ รูลวด

                                        ?>
                                        <td><strong>จะได้ว่า ผลิตสร้อยยาว:</strong></td>
                                        <td><?php echo number_format($production_length, 2); ?> นิ้ว</td>
                                    </tr>
                                    <tr class="table-info">
                                        <td><strong>ใช้ ลวดยาว :</strong></td>
                                        <td><?php echo number_format($use_wire_length, 2); ?> นิ้ว.</td>
                                    </tr>
                                    <tr class="table-info">
                                        <td><strong>อัตราส่วน นน.ลวด(โปร่ง)/นิ้ว :</strong></td>
                                        <td><?php echo number_format($per_wire, 2); ?> นิ้ว.</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="table-secondary"><strong>หารูลวดจากอัตราส่วนอื่น</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>อัตราส่วน: </strong></td>
                                        <td><?php echo number_format($ratioBy_id['ratio_data'], 2) . ' มม. -> ทอง/ทอง+ไส้'; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="table-secondary"><strong>ลวดโปร่ง (มีไส้)</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>รูลวด : </strong></td>
                                        <td><?php echo number_format($ratioBy_id['ratio_size'], 2) . 'มม.'; ?></td>
                                        <td><strong>นน.ลวด ก่อนสกัด : </strong></td>
                                        <td><?php echo number_format($ratioBy_id['ratio_gram'], 2) . 'กรัม.'; ?></td>
                                        <td><strong>ความยาวลวด: </strong></td>
                                        <td><?php echo number_format($ratioBy_id['ratio_inch'], 2) . 'นิ้ว.'; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>คำนวณ นน.ลวด (ไม่มีทองแดง) : </strong></td>
                                        <td><?php echo number_format($ratio_weight_no_copper, 2) . 'มม.'; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>ลวดตัน : </strong></td>
                                        <td><?php echo number_format($ratio_solid_wire, 3) . 'กรัม'; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>อัตราส่วน นน.ลวดตัน/ลวดโปร่ง : </strong></td>
                                        <td><?php echo number_format($ratio_solid_hollow, 2) . 'กรัม'; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>คิดเป็น ลวดตัน นน. : </strong></td>
                                        <td><?php echo number_format($total_solid, 4) . 'กรัม'; ?></td>
                                    </tr>
                                    <tr class="table-danger">
                                        <td><strong>จะได้ รูลวด : </strong></td>
                                        <td><?php echo number_format($total_size, 2) . 'มม.'; ?></td>
                                    </tr>
                                    <?php
                                    if (isset($selected_necklace['ratio_width']) && isset($selected_necklace['ratio_thick'])) {
                                        $width_size = $selected_necklace['ratio_width'] * $total_size; // หน้ากว้าง
                                        $thick_size = $selected_necklace['ratio_thick'] * $total_size; // หนา
                                    ?>
                                        <tr class="table-danger">
                                            <td><strong>หน้ากว้าง(มม.) : </strong></td>
                                            <td><?php echo number_format($width_size, 2) . 'มม.'; ?></td>
                                            <td><strong>หนา(มม.) : </strong></td>
                                            <td><?php echo number_format($thick_size, 2) . 'มม.'; ?></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </div>
                                </tbody>
                                </table>
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
        // Add this to your existing script section
        $(document).ready(function() {
            $('#necklace_name').select2();
            $('#gold_type').select2();
            $('#ratio').select2();

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
    </script>
</body>

</html>