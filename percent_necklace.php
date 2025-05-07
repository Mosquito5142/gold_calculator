<?php
require 'functions/check_login.php';
require 'config/db_connect.php';
require 'functions/management_percent_necklace.php';

$percent_necklaces = get_percent_necklace($pdo);

// ตรวจสอบว่ามีการส่ง pn_id มาหรือไม่
$baht = $_GET['baht'] ?? '';
$grams = $_GET['grams'] ?? '';
$width = $_GET['width'] ?? '';
$selected_pn_id = $_GET['pn_id'] ?? null;
$necklace_details = null;
$necklace_info = null;
$total_weight = 0;
$total_length = 0;

// ถ้ามีการเลือกสร้อย ให้ดึงข้อมูล
if ($selected_pn_id) {
    // ดึงข้อมูลหลักของสร้อย
    $stmt = $pdo->prepare("SELECT * FROM percent_necklace WHERE pn_id = :pn_id");
    $stmt->execute(['pn_id' => $selected_pn_id]);
    $necklace_info = $stmt->fetch(PDO::FETCH_ASSOC);

    // ดึงข้อมูลรายละเอียดของสร้อย
    $necklace_details = get_percent_necklace_detail($pdo, $selected_pn_id);

    // คำนวณข้อมูลรวม
    if (!empty($necklace_details)) {
        foreach ($necklace_details as $detail) {
            $total_weight += floatval($detail['pnd_weight_grams']);
            $total_length += !empty($detail['pnd_long_inch']) ? floatval($detail['pnd_long_inch']) : 0;
        }
    }
}

// ฟังก์ชันคำนวณเปอร์เซ็นต์
function calculate_percent($total, $part)
{
    return ($part / $total) * 100;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สัดส่วน%สร้อย</title>
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
        .card {
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #eaeaea;
            padding: 15px 20px;
        }

        .card-title {
            margin-bottom: 0;
            color: #333;
            font-weight: 600;
        }

        .card-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-control {
            height: 42px;
            border-radius: 5px;
        }

        .form-control-static {
            padding: 7px 12px;
            background-color: #f5f5f5;
            border-radius: 5px;
            min-height: 38px;
            display: flex;
            align-items: center;
        }

        .detail-container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-top: 25px;
            border: 1px solid #eaeaea;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #f1f1f1;
        }

        .select2-container .select2-selection--single {
            height: 42px;
            display: flex;
            align-items: center;
        }

        .highlight-field {
            background-color: #fff9c4 !important;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 5px;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .section-divider {
            border-top: 1px solid #e9e9e9;
            margin: 25px 0;
        }

        .total-row {
            background-color: #e8f5e9;
            font-weight: bold;
        }

        .image-container {
            background-color: #f8f9fa;
            border: 1px solid #eaeaea;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .image-container:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .no-image-container {
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border: 2px dashed #dee2e6;
        }

        #imageModal .modal-dialog {
            max-width: 800px;
        }

        #fullImage {
            max-height: 80vh;
            object-fit: contain;
        }

        .visualization-row td {
            background-color: #f8f9fa;
        }

        .visualization-container {
            background-color: #f8f9fa;
            border-radius: 0 0 8px 8px;
        }

        .part-row.active-row {
            background-color: #e3f2fd !important;
        }

        .visualization-title {
            font-size: 0.9rem;
            text-align: center;
            color: #555;
            margin-bottom: 10px;
        }

        .svg-container {
            width: 300px !important;
            height: 200px !important;
            max-width: none !important;
            max-height: none !important;
            min-width: 300px !important;
            min-height: 200px !important;
            padding: 10px !important;
            margin: 0 auto;
            display: block;
        }

        .svg-container svg {
            width: 300px !important;
            height: 200px !important;
            min-width: 300px !important;
            min-height: 200px !important;
            max-width: none !important;
            max-height: none !important;
            display: block;
            /* ไม่ปรับขนาดตามหน้าจอ */
        }

        @media (max-width: 767.98px) {

            .svg-container,
            .svg-container svg {
                width: 300px !important;
                height: 200px !important;
                min-width: 300px !important;
                min-height: 200px !important;
                max-width: none !important;
                max-height: none !important;
            }
        }

        /* เพิ่มเข้าไปในส่วน style ของไฟล์ percent_necklace.php */
        tr.multi-type td {
            background-color: #e8eaf6 !important;
            /* สีฟ้าอ่อน */
        }

        .reference-width-marker {
            position: relative;
        }

        .reference-width-marker::after {
            content: "⭐";
            position: absolute;
            right: -5px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.8rem;
            color: #ff9800;
        }

        /* สไตล์สำหรับหมายเหตุ ratio */
        .ratio-note {
            background-color: #f5f5f5;
            padding: 0.5rem;
            border-radius: 0.25rem;
            margin-top: 0.5rem;
            font-size: 0.85rem;
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
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col">
                            <h3 class="page-title">ข้อมูลเปอร์เซ็นต์สร้อย</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">ข้อมูลเปอร์เซ็นต์สร้อย</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">เลือกข้อมูลสร้อย</h4>
                                <a href="percent_necklace_management.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> เพิ่มข้อมูล
                                </a>
                            </div>
                            <div class="card-body">
                                <form method="get" action="" class="mb-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="select_necklace_name" class="form-label">เลือกสร้อย:</label>
                                                <select name="pn_id" id="select_necklace_name" class="form-select select2" onchange="this.form.submit()">
                                                    <option value="">-- เลือกสร้อย --</option>
                                                    <?php foreach ($percent_necklaces as $necklace) : ?>
                                                        <?php
                                                        $updated_date = date('d/m/Y', strtotime($necklace['updated_at']));
                                                        // แสดงชื่อสั้น ๆ บนมือถือ
                                                        $short_name = $necklace['pn_name'];
                                                        // ชื่อเต็มสำหรับ data-full-name
                                                        $full_name = $necklace['pn_name'] . ' ' . ($necklace['first_name'] ?? 'ไม่ระบุ') . ' (' . $updated_date . ')';
                                                        ?>
                                                        <option value="<?php echo $necklace['pn_id']; ?>"
                                                            <?php echo ($selected_pn_id == $necklace['pn_id']) ? 'selected' : ''; ?>
                                                            data-full-name="<?php echo htmlspecialchars($full_name); ?>">
                                                            <?php echo $short_name; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <?php if ($necklace_info && $selected_pn_id) : ?>
                                    <div class="detail-container">
                                        <div class="row mb-4">
                                            <div class="col-md-12 text-center mb-3">
                                                <?php if (!empty($necklace_info['image'])): ?>
                                                    <div class="image-container">
                                                        <img src="uploads/img/percent_necklace/<?php echo htmlspecialchars($necklace_info['image']); ?>"
                                                            class="img-fluid rounded" style="max-height: 250px; cursor: pointer;"
                                                            onclick="showFullImage('uploads/img/percent_necklace/<?php echo htmlspecialchars($necklace_info['image']); ?>', '<?php echo htmlspecialchars($necklace_info['pn_name']); ?>')">
                                                        <div class="mt-2">
                                                            <small class="text-muted">คลิกที่รูปภาพเพื่อขยาย</small>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="no-image-container p-4 bg-light rounded text-center">
                                                        <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                                        <p class="text-muted">ไม่มีรูปภาพ</p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">ชื่อสร้อย:</label>
                                                    <div class="form-control-static"><?php echo htmlspecialchars($necklace_info['pn_name']); ?></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">น้ำหนักรวม (กรัม):</label>
                                                    <div class="form-control-static"><?php echo htmlspecialchars($necklace_info['pn_grams']); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <button type="button" class="btn btn-secondary" id="toggleDetailsBtn">
                                                    <i class="fas fa-chevron-down"></i> แสดงรายละเอียดการคำนวณ
                                                </button>
                                            </div>
                                        </div>

                                        <div id="calculationDetails" style="display: none;">
                                            <div class="section-divider"></div>
                                            <h5 class="mb-3">รายละเอียดชิ้นส่วน</h5>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>ประเภท</th>
                                                            <th>ชื่อ</th>
                                                            <th>น้ำหนัก (กรัม)</th>
                                                            <th>%</th>
                                                            <th>ความยาว (นิ้ว)</th>
                                                            <!-- ถ้าเป็นสร้อย -->
                                                            <th>ลวด.รู</th>
                                                            <th>ลวด.หนา</th>
                                                            <th>ลวด.ไส้</th>
                                                            <th>โต.กว้าง(มม.)</th>
                                                            <th>โต.หนา(มม.)</th>
                                                            <!-- ถ้าเป็นอะไหล่ -->
                                                            <th>โต.กว้าง(มม.)</th>
                                                            <th>โต.สูง(มม.)</th>
                                                            <th>โต.หนา(มม.)</th>
                                                            <th>ratio(กว้าง)</th>
                                                            <th>ratio(สูง)</th>
                                                            <th>ratio(หนา)</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="total-row">
                                                            <td>-</td>
                                                            <td>ทั้งหมด</td>
                                                            <td><?php echo number_format($total_weight, 2); ?></td>
                                                            <td>100%</td>
                                                            <td><?php echo $total_length > 0 ? number_format($total_length, 2) : '-'; ?></td>
                                                        </tr>
                                                        <?php
                                                        // เก็บค่า scale_wire_weight ของ pnd_type ที่เป็น "สร้อย" หรือ "มัลติ"
                                                        $scale_wire_weight = 0;
                                                        $total_multi_width = 0;
                                                        $multi_count = 0;

                                                        // ตรวจสอบประเภทมัลติก่อน
                                                        foreach ($necklace_details as $detail) {
                                                            if ($detail['pnd_type'] === 'มัลติ') {
                                                                $total_multi_width += floatval($detail['scale_wire_weight']);
                                                                $multi_count++;
                                                            }
                                                        }

                                                        // ใช้ความกว้างรวมของมัลติถ้ามี
                                                        if ($multi_count > 0) {
                                                            $scale_wire_weight = $total_multi_width;
                                                        } else {
                                                            // ถ้าไม่มีมัลติ ให้ใช้ค่าจากสร้อย
                                                            foreach ($necklace_details as $detail) {
                                                                if ($detail['pnd_type'] === 'สร้อย') {
                                                                    $scale_wire_weight = floatval($detail['scale_wire_weight']);
                                                                    break;
                                                                }
                                                            }
                                                        }

                                                        if (!empty($necklace_details)) :
                                                            foreach ($necklace_details as $detail) :
                                                                $weight = floatval($detail['pnd_weight_grams']);
                                                                $percent = calculate_percent($total_weight, $weight);

                                                                // คำนวณ ratio เฉพาะ pnd_type ที่เป็น "อะไหล่"
                                                                $ratio_width = $ratio_height = $ratio_thick = '-';
                                                                if ($detail['pnd_type'] === 'อะไหล่' && $scale_wire_weight > 0) {
                                                                    $ratio_width = floatval($detail['parts_weight']) / $scale_wire_weight;
                                                                    $ratio_height = floatval($detail['parts_height']) / $scale_wire_weight;
                                                                    $ratio_thick = floatval($detail['parts_thick']) / $scale_wire_weight;
                                                                }
                                                        ?>
                                                                <tr>
                                                                    <td><?php echo htmlspecialchars($detail['pnd_type'] ?? '-'); ?></td>
                                                                    <td><?php echo htmlspecialchars($detail['pnd_name']); ?></td>
                                                                    <td><?php echo number_format($weight, 2); ?></td>
                                                                    <td><?php echo number_format($percent, 1); ?>%</td>
                                                                    <td><?php echo !empty($detail['pnd_long_inch']) ? number_format(floatval($detail['pnd_long_inch']), 2) : '-'; ?></td>
                                                                    <td><?php echo number_format($detail['wire_hole'], 2); ?></td>
                                                                    <td><?php echo number_format($detail['wire_thick'], 2); ?></td>
                                                                    <td><?php echo number_format($detail['wire_core'], 2); ?></td>
                                                                    <td><?php echo number_format($detail['scale_wire_weight'], 2); ?></td>
                                                                    <td><?php echo number_format($detail['scale_wire_thick'], 2); ?></td>
                                                                    <td><?php echo number_format($detail['parts_weight'], 2); ?></td>
                                                                    <td><?php echo number_format($detail['parts_height'], 2); ?></td>
                                                                    <td><?php echo number_format($detail['parts_thick'], 2); ?></td>
                                                                    <td><?php echo is_numeric($ratio_width) ? number_format($ratio_width, 2) : '-'; ?></td>
                                                                    <td><?php echo is_numeric($ratio_height) ? number_format($ratio_height, 2) : '-'; ?></td>
                                                                    <td><?php echo is_numeric($ratio_thick) ? number_format($ratio_thick, 2) : '-'; ?></td>
                                                                </tr>
                                                            <?php
                                                            endforeach;
                                                        else :
                                                            ?>
                                                            <tr>
                                                                <td colspan="5" class="text-center">ไม่พบข้อมูลรายละเอียด</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="section-divider"></div>
                                        <div class="row">
                                            <div class="col-12">
                                                <h4 class="mb-3">คำนวณมูลค่า</h4>
                                            </div>
                                            <div class="col-12">
                                                <h6 class="mb-3">เลือกกรอก</h6>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">บาท</label>
                                                    <input type="number" class="form-control highlight-field" id="pn_baht" step="0.01">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">น้ำหนัก (กรัม)</label>
                                                    <input type="number" class="form-control highlight-field" name="pn_grams" id="pn_grams" step="0.01" required>
                                                </div>
                                            </div>

                                            <!-- เพิ่มส่วนคำนวณ Scale Wire Weight -->
                                            <div class="col-12 mt-4">
                                                <h5>คำนวณขนาดอะไหล่</h5>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">ความกว้างสร้อย (มม.)</label>
                                                    <input type="number" class="form-control highlight-field" id="custom_scale_wire_weight" step="0.01">
                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-2">
                                                <div id="result" class="mt-3 p-3" style="display: none; background-color: #e8f5e9; border-radius: 5px;">
                                                    <!-- ผลลัพธ์จะแสดงที่นี่ -->
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <h5 class="mb-3">ผลการคำนวณมูลค่า</h5>
                                                        </div>
                                                        <div class="col-md-6 text-end">
                                                            <button type="button" class="btn btn-success" id="saveAsCopyBtn">
                                                                <i class="fas fa-copy"></i> บันทึกเป็นรายการใหม่ (สำเนา)
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr class="text-center">
                                                                    <th>ประเภท</th>
                                                                    <th>ชื่อ</th>
                                                                    <th>น้ำหนัก (กรัม)</th>
                                                                    <th>%</th>
                                                                    <th>มูลค่า (บาท)</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="resultTableBody">
                                                                <!-- ผลลัพธ์การคำนวณจะแสดงที่นี่ -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                <!-- เพิ่มในส่วน ratioResult -->
                                                <div class="mt-4" id="ratioResult" style="display:none;">
                                                    <div class="mb-4">
                                                        <h4 class="text-primary fw-bold py-2 d-flex align-items-center justify-content-between" style="background:#e3f2fd;border-radius:8px;">
                                                            <span class="ms-2"><i class="fas fa-ruler-combined me-2"></i>ผลการคำนวณ Scale Wire Weight</span>
                                                            <button type="button" class="btn btn-outline-primary btn-sm me-2" id="dimensionHelpBtn">
                                                                <i class="fas fa-question-circle me-1"></i> วิธีวัดขนาด
                                                            </button>
                                                        </h4>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr class="text-center">
                                                                    <th>ประเภท</th>
                                                                    <th>ชื่อ</th>
                                                                    <th>กว้าง (มม.)</th>
                                                                    <th>สูง (มม.)</th>
                                                                    <th>หนา (มม.)</th>
                                                                    <th>อัตราส่วนกว้าง</th>
                                                                    <th>อัตราส่วนสูง</th>
                                                                    <th>อัตราส่วนหนา</th>
                                                                    <th>ภาพจำลอง</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="ratioTableBody"></tbody>
                                                        </table>
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
        </div>
    </div>
    <!-- Modal แสดงรูปเต็ม -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="fullImage" class="img-fluid" src="">
                </div>
            </div>
        </div>
    </div>
    <!-- Modal แสดงรูปอธิบายขนาด กว้าง สูง หนา -->
    <div class="modal fade" id="dimensionModal" tabindex="-1" aria-labelledby="dimensionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dimensionModalLabel">วิธีวัดขนาด กว้าง สูง และหนา</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-150">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">กว้าง × สูง</h5>
                                </div>
                                <div class="card-body text-center d-flex align-items-center justify-content-center" style="height: 400px; overflow: hidden;">
                                    <img src="uploads/img/กว้างสูง.jpg" class="img-fluid rounded" alt="กว้าง × สูง" style="max-height: 400px; width: auto; object-fit: contain;">
                                </div>
                                <div class="card-footer bg-light">
                                    <p class="mb-0 small">
                                        <i class="fas fa-info-circle text-primary me-1"></i>
                                        มุมมองด้านหน้า แสดงความกว้างและความสูงของอะไหล่
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-150">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">สูง × หนา</h5>
                                </div>
                                <div class="card-body text-center d-flex align-items-center justify-content-center" style="height: 400px; overflow: hidden;">
                                    <img src="uploads/img/หนาสูง.jpg" class="img-fluid rounded" alt="สูง × หนา" style="max-height: 400px; width: auto; object-fit: contain;">
                                </div>
                                <div class="card-footer bg-light">
                                    <p class="mb-0 small">
                                        <i class="fas fa-info-circle text-primary me-1"></i>
                                        มุมมองด้านข้าง แสดงความสูงและความหนาของอะไหล่
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-lightbulb me-2"></i>คำอธิบายเพิ่มเติม</h5>
                                <ul class="mb-0">
                                    <li><strong>ความกว้าง (Width):</strong> วัดจากด้านซ้ายไปขวาของอะไหล่ (แนวนอน)</li>
                                    <li><strong>ความสูง (Height):</strong> วัดจากด้านบนลงล่างของอะไหล่ (แนวตั้ง)</li>
                                    <li><strong>ความหนา (Thickness):</strong> วัดความลึกจากด้านหน้าไปด้านหลังของอะไหล่</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
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
    <script src="js/percent_necklace.js"></script>

    <script>
        $(document).ready(function() {
            // กำหนดค่าเริ่มต้นจาก URL parameters
            <?php if (!empty($baht)): ?>
                $('#pn_baht').val('<?php echo htmlspecialchars($baht); ?>');
            <?php endif; ?>

            <?php if (!empty($grams)): ?>
                $('#pn_grams').val('<?php echo htmlspecialchars($grams); ?>');
            <?php endif; ?>

            <?php if (!empty($width)): ?>
                $('#custom_scale_wire_weight').val('<?php echo htmlspecialchars($width); ?>');
            <?php endif; ?>

            // ทริกเกอร์ event เพื่อให้มีการคำนวณอัตโนมัติ
            if ($('#pn_grams').val() || $('#pn_baht').val() || $('#custom_scale_wire_weight').val()) {
                $('#pn_grams').trigger('input');
                $('#custom_scale_wire_weight').trigger('input');
            }
            // แสดง Modal เมื่อคลิกที่ปุ่ม
            $("#dimensionHelpBtn").on('click', function() {
                $("#dimensionModal").modal('show');
            });
        });
        <?php if (!empty($necklace_details)) : ?>
            // ฟังก์ชั่นสำหรับสร้างแถวรายละเอียดจากข้อมูล PHP
            function generateDetailRows(grams, pricePerGram) {
                let rows = '';
                <?php foreach ($necklace_details as $detail) : ?>
                    <?php
                    $detailType = htmlspecialchars($detail['pnd_type'] ?? '-');
                    $detailName = htmlspecialchars($detail['pnd_name']);
                    $detailWeight = floatval($detail['pnd_weight_grams']);
                    $detailPercent = calculate_percent($total_weight, $detailWeight);
                    ?>

                    // คำนวณน้ำหนักใหม่ตามสัดส่วนของน้ำหนักรวมที่ผู้ใช้กรอก
                    const newWeight_<?php echo $detail['pnd_id']; ?> = grams * (<?php echo $detailPercent; ?> / 100);

                    // คำนวณมูลค่าตามน้ำหนักใหม่
                    const newValue_<?php echo $detail['pnd_id']; ?> = newWeight_<?php echo $detail['pnd_id']; ?> * pricePerGram;

                    // เพิ่มแถวในตาราง
                    rows += `
                    <tr class="text-center">
                        <td><?php echo $detailType; ?></td>
                        <td><?php echo $detailName; ?></td>
                        <td>${newWeight_<?php echo $detail['pnd_id']; ?>.toFixed(2)}</td>
                        <td><?php echo number_format($detailPercent, 1); ?>%</td>
                        <td>${newValue_<?php echo $detail['pnd_id']; ?>.toFixed(2)}</td>
                    </tr>
                    `;
                <?php endforeach; ?>

                return rows;
            }

            // แก้ไขฟังก์ชัน generateRatioRows เพื่อให้รองรับประเภทมัลติ
            function generateRatioRows(scale_wire_weight) {
                let rows = '';

                <?php
                // เก็บค่า scale_wire_weight เดิมจากฐานข้อมูล (ความกว้างเดิมของลวด)
                $original_scale_wire_weight = 0;
                $total_multi_width = 0;
                $multi_count = 0;

                // ตรวจสอบประเภทมัลติก่อน
                foreach ($necklace_details as $detail) {
                    if ($detail['pnd_type'] === 'มัลติ') {
                        $total_multi_width += floatval($detail['scale_wire_weight']);
                        $multi_count++;
                    }
                }

                // ใช้ความกว้างรวมของมัลติถ้ามี
                if ($multi_count > 0) {
                    $original_scale_wire_weight = $total_multi_width;
                } else {
                    // ถ้าไม่มีมัลติ ให้ใช้ค่าจากสร้อยหรือกำไล
                    foreach ($necklace_details as $detail) {
                        if ($detail['pnd_type'] === 'สร้อย' || $detail['pnd_type'] === 'กำไล') {
                            $original_scale_wire_weight = floatval($detail['scale_wire_weight']);
                            break;
                        }
                    }
                }
                ?>

                // ค่า scale_wire_weight เดิมจากฐานข้อมูล
                const original_scale_wire_weight = <?php echo $original_scale_wire_weight; ?>;

                <?php foreach ($necklace_details as $detail) : ?>
                    <?php if ($detail['pnd_type'] === 'อะไหล่') : ?>
                        <?php
                        $detailType = htmlspecialchars($detail['pnd_type'] ?? '-');
                        $detailName = htmlspecialchars($detail['pnd_name']);
                        $parts_weight = floatval($detail['parts_weight']);
                        $parts_height = floatval($detail['parts_height']);
                        $parts_thick = floatval($detail['parts_thick']);
                        ?>

                        // คำนวณ ratio จากความกว้างเดิม
                        const ratio_width_<?php echo $detail['pnd_id']; ?> = <?php echo $parts_weight; ?> / original_scale_wire_weight;
                        const ratio_height_<?php echo $detail['pnd_id']; ?> = <?php echo $parts_height; ?> / original_scale_wire_weight;
                        const ratio_thick_<?php echo $detail['pnd_id']; ?> = <?php echo $parts_thick; ?> / original_scale_wire_weight;

                        // คำนวณขนาดอะไหล่ใหม่จาก scale_wire_weight ที่ผู้ใช้กรอก
                        const new_width_<?php echo $detail['pnd_id']; ?> = ratio_width_<?php echo $detail['pnd_id']; ?> * scale_wire_weight;
                        const new_height_<?php echo $detail['pnd_id']; ?> = ratio_height_<?php echo $detail['pnd_id']; ?> * scale_wire_weight;
                        const new_thick_<?php echo $detail['pnd_id']; ?> = ratio_thick_<?php echo $detail['pnd_id']; ?> * scale_wire_weight;

                        // สร้างแถวข้อมูล
                        rows += `
                    <tr class="text-center part-row" id="row_${<?php echo $detail['pnd_id']; ?>}">
                        <td>${'<?php echo $detailType; ?>'}</td>
                        <td>${'<?php echo $detailName; ?>'}</td>
                        <td>${new_width_<?php echo $detail['pnd_id']; ?>.toFixed(2)}</td>
                        <td>${new_height_<?php echo $detail['pnd_id']; ?>.toFixed(2)}</td>
                        <td>${new_thick_<?php echo $detail['pnd_id']; ?>.toFixed(2)}</td>
                        <td>${ratio_width_<?php echo $detail['pnd_id']; ?>.toFixed(2)}</td>
                        <td>${ratio_height_<?php echo $detail['pnd_id']; ?>.toFixed(2)}</td>
                        <td>${ratio_thick_<?php echo $detail['pnd_id']; ?>.toFixed(2)}</td>
                        <td>
                            <button class="btn btn-sm btn-info toggle-vis-btn" data-part-id="${<?php echo $detail['pnd_id']; ?>}">
                                <i class="fas fa-eye"></i> ดูภาพ
                            </button>
                        </td>
                    </tr>
                    
                    <!-- แถวสำหรับแสดงภาพ -->
                    <tr class="visualization-row" id="vis_row_${<?php echo $detail['pnd_id']; ?>}" style="display: none;">
                        <td colspan="9" class="p-0">
                            <div class="visualization-container p-3 border-top">
                                <div class="visualization-header text-center">
                                    <h5 class="mt-3">ภาพจำลองของ <strong>${'<?php echo $detailName; ?>'}</strong> (ในมือถือขนาดอาจไม่ตรงตามจริง)</h5>
                                </div>
                                <div class="row">
                                    <!-- มุมมองที่ 1: กว้าง x สูง -->
                                    <div class="col-md-6 mt-5">
                                        <div class="visualization-title">มุมมองที่ 1: กว้าง × สูง</div>
                                        <div class="wire-size-visualization">
                                            <div class="mb-2 text-center">
                                                <span class="me-4">
                                                    <strong>กว้าง:</strong> ${new_width_<?php echo $detail['pnd_id']; ?>.toFixed(2)} มม.
                                                </span>
                                                <span>
                                                    <strong>สูง:</strong> ${new_height_<?php echo $detail['pnd_id']; ?>.toFixed(2)} มม.
                                                </span>
                                            </div>
                                            <div class="svg-container">
                                                <svg viewBox="0 0 300 200" preserveAspectRatio="xMidYMid meet" style="width: 100%; height: auto; background: white; border: 1px solid #ddd;">
                                                    ${generateRectSVG(new_width_<?php echo $detail['pnd_id']; ?>, new_height_<?php echo $detail['pnd_id']; ?>)}
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- มุมมองที่ 2: สูง x หนา -->
                                    <div class="col-md-6 mt-5">
                                        <div class="visualization-title">มุมมองที่ 2: สูง × หนา</div>
                                        <div class="wire-size-visualization">
                                            <div class="mb-2 text-center">
                                                <span class="me-4">
                                                    <strong>สูง:</strong> ${new_height_<?php echo $detail['pnd_id']; ?>.toFixed(2)} มม.
                                                </span>
                                                <span>
                                                    <strong>หนา:</strong> ${new_thick_<?php echo $detail['pnd_id']; ?>.toFixed(2)} มม.
                                                </span>
                                            </div>
                                            <div class="svg-container">
                                                <svg viewBox="0 0 300 200" preserveAspectRatio="xMidYMid meet" style="width: 100%; height: auto; background: white; border: 1px solid #ddd;">
                                                    ${generateRectSVG(new_thick_<?php echo $detail['pnd_id']; ?>,new_height_<?php echo $detail['pnd_id']; ?>)}
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <button class="btn btn-sm btn-secondary hide-vis-btn" data-part-id="${<?php echo $detail['pnd_id']; ?>}">
                                        <i class="fas fa-eye-slash"></i> ซ่อนภาพ
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    `;
                    <?php endif; ?>
                <?php endforeach; ?>

                return rows;
            }
        <?php else : ?>
            // กรณีไม่มีข้อมูล
            function generateDetailRows() {
                return '<tr><td colspan="5" class="text-center">ไม่พบข้อมูลรายละเอียด</td></tr>';
            }

            function generateRatioRows() {
                return '<tr><td colspan="9" class="text-center">ไม่พบข้อมูลรายละเอียดอะไหล่</td></tr>';
            }
        <?php endif; ?>
        // เพิ่มฟังก์ชันนี้ใน percent_necklace.js
        function calculateReferenceWidth() {
            let referenceWidth = 0;
            let totalMultiWidth = 0;
            let multiCount = 0;

            <?php if (!empty($necklace_details)) : ?>
                <?php foreach ($necklace_details as $detail) : ?>
                    <?php if ($detail['pnd_type'] === 'มัลติ' && !empty($detail['scale_wire_weight'])) : ?>
                        totalMultiWidth += <?php echo floatval($detail['scale_wire_weight']); ?>;
                        multiCount++;
                    <?php endif; ?>
                <?php endforeach; ?>

                // ถ้ามีประเภทมัลติ ใช้ความกว้างรวมของมัลติทั้งหมด
                if (multiCount > 0) {
                    referenceWidth = totalMultiWidth;
                } else {
                    // ถ้าไม่มีมัลติ ค้นหาสร้อยหรือกำไลอันแรกแทน
                    <?php foreach ($necklace_details as $detail) : ?>
                        <?php if (($detail['pnd_type'] === 'สร้อย' || $detail['pnd_type'] === 'กำไล') && !empty($detail['scale_wire_weight'])) : ?>
                            if (referenceWidth === 0) {
                                referenceWidth = <?php echo floatval($detail['scale_wire_weight']); ?>;
                            }
                        <?php endif; ?>
                    <?php endforeach; ?>
                }
            <?php endif; ?>

            // ถ้ามีการกรอกค่า custom_scale_wire_weight ให้ใช้ค่านั้นแทน
            const customWidth = parseFloat($("#custom_scale_wire_weight").val());
            if (!isNaN(customWidth) && customWidth > 0) {
                return customWidth;
            }

            return referenceWidth;
        }
        // รูปแบบการแสดงผลในตัวเลือก (dropdown)
        function formatNecklaceOption(option) {
            if (!option.id) {
                return option.text;
            }

            // ถ้าเป็นการแสดงในดรอปดาวน์ ใช้ชื่อเต็ม
            const fullName = $(option.element).data('full-name');
            return $('<span>' + fullName + '</span>');
        }

        // รูปแบบการแสดงผลเมื่อเลือกแล้ว
        function formatNecklaceSelection(option) {
            if (!option.id) {
                return option.text;
            }

            // ตรวจสอบขนาดหน้าจอ
            if (window.innerWidth < 768) { // ถ้าเป็นมือถือ
                // แสดงเฉพาะชื่อสั้น ๆ
                return option.text;
            } else {
                // ถ้าหน้าจอใหญ่ แสดงชื่อเต็ม
                const fullName = $(option.element).data('full-name');
                return fullName;
            }
        }

        // ปรับขนาดเมื่อหมุนหน้าจอหรือเปลี่ยนขนาด
        $(window).on('resize', function() {
            // บังคับให้ select2 อัพเดตการแสดงผล
            $("#select_necklace_name").select2('destroy').select2({
                width: '100%',
                placeholder: '-- เลือกสร้อย --',
                templateResult: formatNecklaceOption,
                templateSelection: formatNecklaceSelection
            });
        });
    </script>
</body>

</html>