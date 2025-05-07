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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>น้ำประสาน</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css" />
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
    <style>
        /* Excel-like table styling */
        .excel-table {
            width: 100%;
            border-collapse: collapse;
        }

        .excel-table th,
        .excel-table td {
            border: 1px solid #c0c0c0;
            padding: 8px;
        }

        .excel-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        /* Target percent styling */
        .target-header {
            background-color: #28a745;
            color: white;
        }

        .target-input {
            background-color: #d1e7dd !important;
            border-color: #28a745 !important;
            color: #000 !important;
            font-weight: 500;
        }

        .target-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25) !important;
        }

        /* Gold section styling */
        .gold-text {
            color: white;
            font-weight: bold;
        }

        .gold-header {
            background-color: #0d6efd;
            color: white;
        }

        .gold-input {
            background-color: #FFFF00 !important;
            border-color: rgb(134, 134, 15) !important;
            color: #000 !important;
            font-weight: 500;
        }

        .gold-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25) !important;
        }

        /* Parts section styling */
        .part-header {
            background-color: rgba(173, 216, 230, 0.7);
            color: #000;
        }

        .part-input {
            background-color: rgba(255, 255, 224, 0.7) !important;
        }

        /* Pink section styling */
        .pink-text {
            color: #D63384;
            font-weight: bold;
        }

        .pink-input {
            border-color: #D63384 !important;
            color: #D63384 !important;
            font-weight: 500;
        }

        .pink-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(214, 51, 132, 0.25) !important;
        }

        /* Borax section styling */
        .borax-text {
            color: #6610f2;
            font-weight: bold;
        }

        .borax-header {
            background-color: #6610f2;
            color: white;
        }

        /* Results styling */
        .result-table th,
        .result-table td {
            padding: 6px 12px;
        }

        .result-header {
            background-color: #9900FF !important;
            color: white !important;
            text-align: center;
            font-weight: bold;
        }

        .result-section {
            background-color: #f8f9fa;
        }

        .result-row {
            background-color: rgba(144, 238, 144, 0.6);
            font-weight: bold;
        }

        /* Summary table styling */
        .gold-row {
            background-color: #0d6efd;
        }

        .gold-row td {
            color: white;
        }

        .other-row td:first-child {
            color: #D63384;
        }

        .total-row {
            background-color: #e8e4f3;
        }

        .total-row td {
            color: #9900FF;
            font-weight: bold;
        }

        .borax-result-row {
            background-color: #6610f230;
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
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="text-primary">หา นป. (ใส่ได้สูงสุดกี่กรัม)</h4>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-primary" id="loadFormulaBtn">
                                    <i class="fas fa-folder-open me-1"></i> โหลดสูตร
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- เปอร์เซ็นต์งานที่ต้องการ -->
                        <div class="mb-4">
                            <div class="table-responsive">
                                <table class="excel-table">
                                    <thead>
                                        <tr>
                                            <th width="30%"></th>
                                            <th width="35%">เปอร์เซ็นต์</th>
                                            <th width="35%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class=""><strong class="text-success">%งาน ที่ต้องการ</strong></td>
                                            <td><input type="number" step="0.01" class="form-control gold-input" id="percent_work" placeholder="เปอร์เซ็นต์"></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- น้ำหนักสร้อยประกอบ -->
                        <div class="mb-4">
                            <div class="table-responsive">
                                <table class="excel-table">
                                    <thead>
                                        <tr>
                                            <th width="30%">รายการ</th>
                                            <th width="35%">น้ำหนัก(กรัม)</th>
                                            <th width="35%">เปอร์เซ็นต์(%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="text-primary"><strong>สร้อย</strong></span></td>
                                            <td><input type="number" step="0.01" class="form-control part-input" id="necklace_gram" placeholder="น้ำหนัก(กรัม)"></td>
                                            <td><input type="number" step="0.01" class="form-control part-input" id="necklace_percent" placeholder="เปอร์เซ็นต์"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- อะไหล่ -->
                        <div class="mb-4">
                            <div class="table-responsive">
                                <table class="excel-table">
                                    <tbody>
                                        <tr class="text-center">
                                            <td class="part-header" width="30%"><strong>รายการ</strong></td>
                                            <td class="part-header" width="35%"><strong>นน.(กรัม)</strong></td>
                                            <td class="part-header" width="35%"><strong>%</strong></td>
                                        </tr>
                                        <td class="result-row"><strong class="text-danger">นป.</strong></td>
                                        <td class="text-center result-row"><strong class="borax-result-highlight" id="maxBoraxWeight">?</strong></td>
                                        <td class="text-center"><input type="number" step="0.01" class="form-control gold-input" id="boraxPercent" placeholder="เปอร์เซ็นต์น้ำประสาน" value="0" required></td>
                                        <?php for ($i = 1; $i <= 10; $i++): ?>
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" id="part<?= $i ?>_name" placeholder="ชื่ออะไหล่">
                                                </td>
                                                <td><input type="number" step="0.01" class="form-control part-input" id="part<?= $i ?>_gram" placeholder="น้ำหนัก(กรัม)"></td>
                                                <td><input type="number" step="0.01" class="form-control part-input" id="part<?= $i ?>_percent" placeholder="เปอร์เซ็นต์"></td>
                                            </tr>
                                        <?php endfor; ?>
                                        <tr>
                                            <td><span class="pink-text">อื่นๆที่ไม่ใช่ทอง</span></td>
                                            <td><input type="number" step="0.01" class="form-control pink-input" id="other_gram" placeholder="ระบุน้ำหนัก"></td>
                                            <td class="text-center text-muted"><small>ไม่มีเปอร์เซ็นต์</small></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- ปุ่มการทำงาน -->
                        <div class="d-flex justify-content-center gap-2 flex-wrap mt-4">
                            <button type="button" id="calculateBtn" class="btn btn-primary">
                                <i class="fas fa-calculator me-1"></i> คำนวณ
                            </button>
                            <button type="button" id="resetBtn" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i> รีเซ็ต
                            </button>
                            <button class="btn btn-success" id="saveFormulaBtn">
                                <i class="fas fa-save me-1"></i> บันทึกสูตร
                            </button>
                        </div>

                        <!-- ผลลัพธ์แบบสรุป -->
                        <div id="resultSection" class="mt-5" style="display: none;">
                            <div class="table-responsive">
                                <table class="excel-table result-table">
                                    <thead>
                                        <tr>
                                            <th colspan="3" class="result-header">สรุปผลการคำนวณ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td width="60%"><strong>รายการ</strong></td>
                                            <td width="40%" class="text-center"><strong>น้ำหนัก (กรัม)</strong></td>
                                        </tr>
                                        <tr class="">
                                            <td class="gold-header gold-row"><span class="gold-text">ทอง + นป.</span></td>
                                            <td class="text-center"><span id="totalGoldBoraxWeight">-</span></td>
                                        </tr>
                                        <tr>
                                            <td>สร้อย + อะไหล่</td>
                                            <td class="text-center"><span id="summary_necklace_parts_weight">-</span></td>
                                        </tr>
                                        <tr class="">
                                            <td><span class="borax-text"><strong>นป.</strong></span></td>
                                            <td class="text-center"><span class="maxBoraxWeight">-</span></td>
                                        </tr>

                                        <tr class="other-row">
                                            <td><span class="pink-text">อื่นๆที่ไม่ใช่ทอง</span></td>
                                            <td class="text-center"><span id="totalNonGoldWeight">-</span></td>
                                        </tr>
                                        <tr class="total-row text-center">
                                            <td><strong>รวม</strong></td>
                                            <td class="text-center"><strong><span id="grandTotalWeight">-</span></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="assets/js/jquery-3.6.0.min.js"></script>
        <script src="assets/js/feather.min.js"></script>
        <script src="assets/js/jquery.slimscroll.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
                // เก็บ ID ของผู้ใช้ปัจจุบัน
                const currentUserId = <?php echo $_SESSION['recipenecklace_users_id'] ?? 0; ?>;

                // ค่าเริ่มต้นสำหรับเปอร์เซ็นต์น้ำประสาน (สามารถปรับเปลี่ยนได้)
                const defaultBoraxPercent = 0;
                $('#boraxPercent').val(defaultBoraxPercent);

                // เก็บจำนวนอะไหล่ที่ใช้งาน
                let partCount = 0;

                // นับจำนวนอะไหล่ที่มีการกรอกข้อมูล
                function countActiveParts() {
                    let count = 0;
                    for (let i = 1; i <= 10; i++) {
                        if ($(`#part${i}_name`).val() || $(`#part${i}_gram`).val() || $(`#part${i}_percent`).val()) {
                            count = i;
                        }
                    }
                    partCount = count;
                    return count;
                }

                // เรียกใช้ฟังก์ชันเพื่อนับจำนวนอะไหล่เริ่มต้น
                countActiveParts();

                // การกำหนดค่าเริ่มต้น (รีเซ็ตฟอร์ม)
                function resetForm() {
                    $('input[type="text"]:not([readonly]), input[type="number"]').val('');
                    $('#resultSection').hide();
                    $('#boraxPercent').val(defaultBoraxPercent);
                }

                // รีเซ็ตฟอร์ม
                $('#resetBtn').on('click', function() {
                    resetForm();
                });

                // คำนวณน้ำประสาน
                $('#calculateBtn').on('click', function() {
                    // ตรวจสอบข้อมูลที่จำเป็น
                    const percentWork = parseFloat($('#percent_work').val());
                    const necklaceGram = parseFloat($('#necklace_gram').val());
                    const necklacePercent = parseFloat($('#necklace_percent').val());
                    const boraxPercent = parseFloat($('#boraxPercent').val() || defaultBoraxPercent);

                    if (!percentWork || !necklaceGram || !necklacePercent) {
                        Swal.fire({
                            title: 'ข้อมูลไม่ครบถ้วน',
                            text: 'กรุณากรอกเปอร์เซ็นต์งานที่ต้องการ, น้ำหนักและเปอร์เซ็นต์ของสร้อยประกอบ',
                            icon: 'warning'
                        });
                        return;
                    }

                    // 1. คำนวณ sumproduct ของสร้อยประกอบและอะไหล่
                    let sumGoldWeight = necklaceGram; // น้ำหนักทองรวม (สร้อย + อะไหล่) ไม่รวมน้ำประสาน
                    let sumProductGold = necklaceGram * necklacePercent / 100; // ผลรวมของ น้ำหนัก x เปอร์เซ็นต์

                    // เพิ่มอะไหล่เข้าไป
                    let partsWeight = 0;
                    for (let i = 1; i <= 10; i++) {
                        const partName = $(`#part${i}_name`).val();
                        const partGram = parseFloat($(`#part${i}_gram`).val()) || 0;
                        const partPercent = parseFloat($(`#part${i}_percent`).val()) || 0;

                        if (partName && partGram > 0) {
                            sumGoldWeight += partGram;
                            sumProductGold += (partGram * partPercent / 100);
                            partsWeight += partGram;
                        }
                    }

                    // 2. น้ำหนักที่ไม่ใช่ทอง
                    const otherWeight = parseFloat($('#other_gram').val()) || 0;

                    // 3. คำนวณน้ำประสานที่สามารถใส่ได้
                    // สูตร: ((sumProductGold) - (sumGoldWeight * percentWork/100)) / ((percentWork/100) - (boraxPercent/100))
                    const maxBoraxWeight = ((sumProductGold) - (sumGoldWeight * percentWork / 100)) / ((percentWork / 100) - (boraxPercent / 100));

                    // 4. คำนวณน้ำหนักรวมทั้งหมด
                    const totalGoldBoraxWeight = sumGoldWeight + maxBoraxWeight; // ทอง + นป.
                    const grandTotalWeight = totalGoldBoraxWeight + otherWeight; // น้ำหนักรวมทั้งหมด

                    // คำนวณน้ำหนักสร้อย + อะไหล่
                    const necklacePartsWeight = necklaceGram + partsWeight;

                    // แสดงผลลัพธ์
                    $('#summary_necklace_parts_weight').text(necklacePartsWeight.toFixed(2) + " กรัม");
                    $('#maxBoraxWeight').text(maxBoraxWeight.toFixed(2) + " กรัม");
                    $('.maxBoraxWeight').text(maxBoraxWeight.toFixed(2) + " กรัม");
                    $('#totalGoldBoraxWeight').text(totalGoldBoraxWeight.toFixed(2) + " กรัม");
                    $('#totalNonGoldWeight').text(otherWeight.toFixed(2) + " กรัม");
                    $('#grandTotalWeight').text(grandTotalWeight.toFixed(2) + " กรัม");
                    $('#percentWorkResult').text(percentWork.toFixed(2) + " %");
                    $('#boraxPercentResult').text(boraxPercent.toFixed(2) + " %");

                    // แสดงค่าน้ำประสานที่แถวน้ำประสานด้านบนด้วย
                    $('.borax-result-highlight').text(maxBoraxWeight.toFixed(2) + " กรัม");

                    // แสดงส่วนผลลัพธ์
                    $('#resultSection').fadeIn();

                    // เลื่อนไปที่ผลลัพธ์
                    $('html, body').animate({
                        scrollTop: $("#resultSection").offset().top - 20
                    }, 500);
                });
                // ฟังก์ชันรวบรวมข้อมูลจากฟอร์มที่กรอก
                function collectFormData() {
                    const items = [];

                    // เปอร์เซ็นต์งานที่ต้องการ
                    items.push({
                        name: 'เปอร์เซ็นต์งาน',
                        weight: null,
                        percentage: $('#percent_work').val() || null
                    });

                    // น้ำหนักสร้อยประกอบ
                    items.push({
                        name: 'น้ำหนักสร้อยประกอบ',
                        weight: $('#necklace_gram').val() || null,
                        percentage: $('#necklace_percent').val() || null
                    });

                    // เก็บอะไหล่
                    for (let i = 1; i <= 10; i++) {
                        const name = $(`#part${i}_name`).val();
                        const weight = $(`#part${i}_gram`).val();
                        const percent = $(`#part${i}_percent`).val();

                        if (name || weight || percent) {
                            items.push({
                                name: name || `อะไหล่ ${i}`,
                                weight: weight || null,
                                percentage: percent || null
                            });
                        }
                    }

                    // อื่นๆที่ไม่ใช่ทอง
                    items.push({
                        name: 'อื่นๆที่ไม่ใช่ทอง',
                        weight: $('#other_gram').val() || null,
                        percentage: null
                    });

                    // เปอร์เซ็นต์น้ำประสาน
                    items.push({
                        name: 'เปอร์เซ็นต์น้ำประสาน',
                        weight: null,
                        percentage: $('#boraxPercent').val() || defaultBoraxPercent
                    });

                    return items;
                }

                // บันทึกสูตร
                $('#saveFormulaBtn').on('click', function() {
                    // ตรวจสอบว่ากรอกข้อมูลหรือยัง
                    const percentWork = $('#percent_work').val();
                    const necklaceGram = $('#necklace_gram').val();

                    if (!percentWork || !necklaceGram) {
                        Swal.fire({
                            title: 'ข้อมูลไม่ครบถ้วน',
                            text: 'กรุณากรอกเปอร์เซ็นต์งานที่ต้องการและน้ำหนักสร้อยประกอบ',
                            icon: 'warning'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'บันทึกสูตร',
                        input: 'text',
                        inputLabel: 'กรุณาระบุชื่อสูตร',
                        inputPlaceholder: 'เช่น สูตรน้ำประสานสร้อย 5 บาท',
                        showCancelButton: true,
                        confirmButtonText: 'บันทึก',
                        cancelButtonText: 'ยกเลิก',
                        inputValidator: (value) => {
                            if (!value) {
                                return 'กรุณาระบุชื่อสูตร';
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const items = collectFormData();

                            $.ajax({
                                url: 'api/save_formula_borax.php',
                                type: 'POST',
                                contentType: 'application/json',
                                dataType: 'json',
                                data: JSON.stringify({
                                    formula_name: result.value,
                                    items: items
                                }),
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'บันทึกสำเร็จ',
                                            text: 'บันทึกสูตรเรียบร้อยแล้ว'
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'เกิดข้อผิดพลาด',
                                            text: response.message || 'ไม่สามารถบันทึกข้อมูลได้'
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error("Error details:", xhr.responseText);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'เกิดข้อผิดพลาด',
                                        text: 'ไม่สามารถบันทึกข้อมูลได้'
                                    });
                                }
                            });
                        }
                    });
                });

                // โหลดสูตร
                $('#loadFormulaBtn').on('click', function() {
                    $.ajax({
                        url: 'api/get_formulas_borax.php',
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success && response.formulas && response.formulas.length > 0) {
                                showFormulasModal(response.formulas);
                            } else {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'ไม่พบข้อมูล',
                                    text: response.message || 'ยังไม่มีสูตรที่บันทึกไว้'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error details:", xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถโหลดข้อมูลได้'
                            });
                        }
                    });
                });

                function showFormulasModal(formulas) {
                    let html = `
                    <div class="formula-modal">
                        <div class="search-container mb-2">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-right-0 py-1">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" id="formulaSearch" class="form-control border-left-0 py-1" placeholder="ค้นหาสูตร...">
                            </div>
                        </div>
                        
                        <div class="table-responsive formula-table">
                            <table class="table table-hover table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th class="border-top-0">ชื่อสูตร</th>
                                        <th class="border-top-0">เจ้าของสูตร</th>
                                        <th class="border-top-0">วันที่บันทึก</th>
                                        <th width="80px" class="border-top-0 text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    if (formulas.length === 0) {
                        html += `
                        <tr>
                            <td colspan="4" class="text-center py-3">
                                <div class="empty-state">
                                    <i class="fas fa-folder-open text-muted mb-2" style="font-size: 2rem;"></i>
                                    <p class="text-muted small">ไม่พบสูตรที่บันทึกไว้</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    } else {
                        formulas.forEach(formula => {
                            const date = new Date(formula.created_at);
                            const formattedDate = date.toLocaleDateString('th-TH', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });

                            // แสดงชื่อ-นามสกุลเจ้าของสูตร (ถ้ามี)
                            let ownerName = '';

                            if (formula.first_name && formula.last_name) {
                                ownerName = `${formula.first_name} ${formula.last_name}`;
                            } else if (formula.username) {
                                ownerName = formula.username;
                            } else {
                                ownerName = `ID: ${formula.created_by}`;
                            }

                            // ตรวจสอบว่าเป็นสูตรของตัวเองหรือไม่
                            const isOwnFormula = formula.created_by == currentUserId;

                            // ปุ่มลบจะแสดงเฉพาะสูตรของตัวเอง
                            const deleteButton = isOwnFormula ?
                                `<button class="btn btn-sm btn-outline-danger delete-formula" data-id="${formula.formula_id}" data-name="${formula.formula_name}" style="padding:2px 5px;">
                    <i class="fas fa-trash-alt"></i>
                </button>` : '';

                            html += `
                <tr class="formula-item">
                    <td class="align-middle py-1">${formula.formula_name}</td>
                    <td class="align-middle py-1 small">${ownerName}</td>
                    <td class="align-middle py-1 small">${formattedDate}</td>
                    <td class="text-center py-1">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary load-formula" data-id="${formula.formula_id}" style="padding:2px 5px;">
                                <i class="fas fa-file-import"></i>
                            </button>
                            ${deleteButton}
                        </div>
                    </td>
                </tr>
                `;
                        });
                    }

                    html += `
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <div class="formula-count small text-muted">
                                พบทั้งหมด ${formulas.length} รายการ
                            </div>
                        </div>
                    </div>
                    `;

                    // เพิ่ม CSS เฉพาะสำหรับ Modal
                    const modalCSS = `
                    <style>
                        /* กำหนดขนาดของ Modal */
                        .swal2-popup {
                            padding: 0.5rem !important;
                            max-width: 98vw !important; 
                            width: auto !important;
                        }
                        
                        .swal2-html-container {
                            margin: 0.5rem !important;
                            padding: 0 !important;
                            overflow: hidden !important;
                        }
                        
                        .formula-modal {
                            max-height: calc(95vh - 100px);
                            margin: 0;
                            padding: 0;
                        }
                        
                        .formula-table {
                            max-height: calc(95vh - 170px);
                            overflow-y: auto;
                            border-radius: 4px;
                            margin: 0;
                            padding: 0;
                        }
                        
                        .formula-table::-webkit-scrollbar {
                            width: 4px;
                        }
                        
                        .formula-table::-webkit-scrollbar-track {
                            background: #f8f9fa;
                        }
                        
                        .formula-table::-webkit-scrollbar-thumb {
                            background: #adb5bd;
                            border-radius: 2px;
                        }
                        
                        /* ปรับขนาดตาราง */
                        .formula-table table {
                            margin-bottom: 0 !important;
                            width: 100% !important;
                        }
                        
                        .formula-table th, .formula-table td {
                            padding: 0.25rem 0.5rem !important;
                            vertical-align: middle;
                            white-space: nowrap;
                        }
                        
                        /* ทำให้ชื่อสูตรสามารถตัดคำได้ */
                        .formula-table td:first-child {
                            white-space: normal;
                            min-width: 180px;
                            max-width: 300px;
                        }
                        
                        /* ปรับขนาดส่วนหัว modal */
                        .swal2-title {
                            font-size: 1.1rem !important;
                            padding: 0.5rem 0 !important;
                            margin: 0 !important;
                        }
                        
                        /* ลดขนาดของตัวอักษรตาราง */
                        .formula-table {
                            font-size: 0.85rem;
                        }
                        
                        /* ปรับแต่งปุ่มในตาราง */
                        .formula-table .btn {
                            min-width: 28px;
                            height: 28px;
                            line-height: 1;
                            padding: 2px 5px !important;
                        }
                        
                        /* ปรับขนาดช่องค้นหา */
                        .search-container {
                            padding: 0;
                            margin-bottom: 0.5rem !important;
                        }
                        
                        /* ปรับ footer */
                        .swal2-actions {
                            margin: 0.5rem 0 0 0 !important;
                            padding: 0 !important;
                        }
                        
                        /* ซ่อน padding ที่ไม่จำเป็น */
                        .swal2-content {
                            padding: 0 !important;
                        }
                    </style>
                    `;

                    // แสดง SweetAlert2 แบบเต็มหน้าจอ
                    Swal.fire({
                        title: 'รายการสูตร',
                        html: modalCSS + html,
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText: 'ปิด',
                        buttonsStyling: true,
                        customClass: {
                            container: 'swal-fullscreen-container',
                            popup: 'swal-fullscreen-popup',
                            content: 'p-0 m-0',
                            actions: 'mt-1'
                        },
                        width: '95%',
                        padding: 0,
                        didOpen: (modal) => {
                            // เพิ่ม tooltip สำหรับชื่อสูตรที่ยาวเกินไป
                            $('.formula-item td:first-child').each(function() {
                                $(this).attr('title', $(this).text());
                            });

                            // ปรับขนาดตามอุปกรณ์
                            const screenWidth = window.innerWidth;

                            // ปรับขนาดคอลัมน์ตามขนาดหน้าจอ
                            if (screenWidth < 576) { // มือถือ
                                $('.formula-table td:nth-child(2)').hide();
                                $('.formula-table th:nth-child(2)').hide();
                            }

                            // โฟกัสที่ช่องค้นหา
                            setTimeout(() => {
                                $('#formulaSearch').focus();
                            }, 200);
                        }
                    });

                    // ฟังก์ชันค้นหาสูตร
                    $('#formulaSearch').on('keyup', function() {
                        const value = $(this).val().toLowerCase();
                        $(".formula-item").filter(function() {
                            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                        });

                        // อัพเดทจำนวนที่พบ
                        const visibleCount = $(".formula-item:visible").length;
                        $('.formula-count').text(`พบ ${visibleCount} จาก ${formulas.length} รายการ`);
                    });

                    // เมื่อคลิกปุ่มเลือกสูตร
                    $('.load-formula').on('click', function() {
                        const formula_id = $(this).data('id');
                        loadFormulaDetail(formula_id);
                        Swal.close();
                    });

                    // เมื่อคลิกปุ่มลบสูตร
                    $('.delete-formula').on('click', function() {
                        const formula_id = $(this).data('id');
                        const formula_name = $(this).data('name');

                        deleteFormula(formula_id, formula_name);
                    });

                    // เพิ่มความสามารถในการกดดับเบิลคลิกเพื่อเลือกสูตร
                    $('.formula-item').on('dblclick', function() {
                        const formula_id = $(this).find('.load-formula').data('id');
                        if (formula_id) {
                            loadFormulaDetail(formula_id);
                            Swal.close();
                        }
                    });
                }

                // โหลดรายละเอียดสูตร
                function loadFormulaDetail(formula_id) {
                    $.ajax({
                        url: `api/get_formula_detail_borax.php?id=${formula_id}`,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                applyFormulaToForm(response.formula);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: response.message || 'ไม่สามารถโหลดข้อมูลได้'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error details:", xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถโหลดข้อมูลได้'
                            });
                        }
                    });
                }

                // นำข้อมูลสูตรมาใส่ในฟอร์ม
                function applyFormulaToForm(formula) {
                    // รีเซ็ตฟอร์มก่อน
                    resetForm();

                    // ใส่ข้อมูลแต่ละรายการ
                    if (formula.items && formula.items.length > 0) {
                        let partIndex = 1;

                        formula.items.forEach(item => {
                            const itemName = item.item_name;

                            if (itemName === 'เปอร์เซ็นต์งาน' && item.percentage) {
                                $('#percent_work').val(item.percentage);
                            } else if (itemName === 'น้ำหนักสร้อยประกอบ') {
                                if (item.weight) $('#necklace_gram').val(item.weight);
                                if (item.percentage) $('#necklace_percent').val(item.percentage);
                            } else if (itemName === 'อื่นๆที่ไม่ใช่ทอง' && item.weight) {
                                $('#other_gram').val(item.weight);
                            } else if (itemName === 'เปอร์เซ็นต์น้ำประสาน' && item.percentage) {
                                $('#boraxPercent').val(item.percentage);
                            } else if (itemName.includes('อะไหล่') || itemName) {
                                // อะไหล่หรือรายการอื่นๆ
                                if (partIndex <= 10) {
                                    $(`#part${partIndex}_name`).val(itemName);
                                    if (item.weight) $(`#part${partIndex}_gram`).val(item.weight);
                                    if (item.percentage) $(`#part${partIndex}_percent`).val(item.percentage);
                                    partIndex++;
                                }
                            }
                        });
                    }

                    // คำนวณผลลัพธ์ใหม่
                    $('#calculateBtn').click();

                    Swal.fire({
                        icon: 'success',
                        title: 'โหลดสำเร็จ',
                        text: `โหลดสูตร "${formula.formula_name}" เรียบร้อยแล้ว`,
                        timer: 1500,
                        showConfirmButton: false
                    });
                }

                // ลบสูตร
                function deleteFormula(formula_id, formula_name) {
                    Swal.fire({
                        title: 'ยืนยันการลบ',
                        text: `คุณต้องการลบสูตร "${formula_name}" ใช่หรือไม่?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'ใช่, ลบเลย',
                        cancelButtonText: 'ยกเลิก',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'api/delete_formula_borax.php',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    formula_id: formula_id
                                },
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'ลบสำเร็จ',
                                            text: 'ลบสูตรเรียบร้อยแล้ว',
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => {
                                            // โหลดรายการสูตรใหม่
                                            $.ajax({
                                                url: 'api/get_formulas_borax.php',
                                                type: 'GET',
                                                dataType: 'json',
                                                success: function(response) {
                                                    if (response.success && response.formulas && response.formulas.length > 0) {
                                                        showFormulasModal(response.formulas);
                                                    } else {
                                                        Swal.close();
                                                        Swal.fire({
                                                            icon: 'info',
                                                            title: 'ไม่พบข้อมูล',
                                                            text: 'ไม่มีสูตรที่บันทึกไว้แล้ว'
                                                        });
                                                    }
                                                }
                                            });
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'เกิดข้อผิดพลาด',
                                            text: response.message || 'ไม่สามารถลบข้อมูลได้'
                                        });
                                    }
                                },
                                error: function() {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'เกิดข้อผิดพลาด',
                                        text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้'
                                    });
                                }
                            });
                        }
                    });
                }
            });
        </script>
    </div>
</body>

</html>