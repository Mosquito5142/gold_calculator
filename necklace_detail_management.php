<?php
// แสดง error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'functions/check_login.php';
require 'config/db_connect.php';
require 'functions/management_necklace_detail.php';

// ตรวจสอบว่า users_depart มีอยู่ใน session หรือไม่ก่อนใช้
$user_dept = isset($_SESSION['recipenecklace_users_depart']) ? $_SESSION['recipenecklace_users_depart'] : '';
$current_user_id = isset($_SESSION['recipenecklace_users_id']) ? $_SESSION['recipenecklace_users_id'] : 0;
$is_admin = $_SESSION['recipenecklace_users_level'] === 'Admin';

if ($_SESSION['recipenecklace_users_level'] === 'Admin' || $user_dept === 'SG' || $user_dept === 'YS' || $user_dept === 'หัวหน้าช่าง') {
    // ถ้าเป็น Admin หรืออยู่ในแผนก SG ให้ดึงข้อมูลทั้งหมด
    $necklace_all_details = get_necklace_all_details($pdo);
} else {
    // ถ้าไม่ใช่ Admin ให้ดึงเฉพาะข้อมูลที่ผู้ใช้เพิ่มเอง
    $necklace_all_details = get_necklace_details_by_user($pdo, $_SESSION['recipenecklace_users_id']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสร้อย</title>
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
        .details-control {
            cursor: pointer;
            font-size: 1.2em;
            color: #007bff;
        }

        .details-control:hover {
            color: #0056b3;
        }

        tr.shown td.details-control i.fa-plus-circle {
            display: none;
        }

        tr.shown td.details-control i.fa-minus-circle {
            display: inline;
        }

        tr:not(.shown) td.details-control i.fa-plus-circle {
            display: inline;
        }

        tr:not(.shown) td.details-control i.fa-minus-circle {
            display: none;
        }

        .child-row {
            padding: 1rem;
            background: #f8f9fa;
        }

        .detail-section {
            margin-bottom: 1.5rem;
        }

        #preview {
            max-height: 200px;
            max-width: 100%;
            display: none;
            margin-top: 10px;
        }

        .img-thumbnail {
            cursor: pointer;
        }

        @media (max-width: 767.98px) {
            table#necklaceTable thead {
                display: none;
            }

            table#necklaceTable tbody tr {
                display: block;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 10px;
                background-color: #fff;
                position: relative;
            }

            table#necklaceTable tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 10px;
                border: none;
                border-bottom: 1px solid #eee;
            }

            table#necklaceTable tbody td:last-child {
                border-bottom: none;
            }

            table#necklaceTable tbody td::before {
                content: attr(data-label);
                font-weight: bold;
                flex: 1;
                color: #333;
                margin-right: 10px;
            }

            table#necklaceTable tbody td img {
                max-height: 80px;
                height: auto;
            }

            table#necklaceTable tbody .details-control {
                position: absolute;
                top: 10px;
                right: 10px;
                padding: 0;
                display: flex;
                gap: 5px;
            }

            table#necklaceTable tbody .details-control i {
                font-size: 16px;
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
                            <h4>จัดการสร้อย</h4>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#necklaceModal" onclick="resetForm()">
                                <i class="fas fa-plus"></i> เพิ่มข้อมูล
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="necklaceTable">
                                <thead>
                                    <tr>
                                        <th width="40"></th>
                                        <th>รูป</th>
                                        <th>ลายสร้อย</th>
                                        <th>ประเภท</th>
                                        <th>วันที่บันทึก</th>
                                        <th>ผู้บันทึก</th>
                                        <th width="120">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($necklace_all_details)): ?>
                                        <?php foreach ($necklace_all_details as $detail): ?>
                                            <tr data-necklace-id="<?php echo $detail['necklace_detail_id']; ?>">
                                                <td class="details-control text-center" data-label="">
                                                    <i class="fas fa-plus-circle"></i>
                                                    <i class="fas fa-minus-circle"></i>
                                                </td>
                                                <td class="text-center" data-label="รูป">
                                                    <?php if (!empty($detail['image'])): ?>
                                                        <img src="uploads/img/necklace_detail/<?php echo htmlspecialchars($detail['image']); ?>"
                                                            class="img-thumbnail" style="max-height: 50px; width: auto;"
                                                            onclick="showFullImage('uploads/img/necklace_detail/<?php echo htmlspecialchars($detail['image']); ?>', '<?php echo htmlspecialchars($detail['name']); ?>')">
                                                    <?php else: ?>
                                                        <i class="fas fa-image text-muted"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td data-label="ลายสร้อย"><?php echo htmlspecialchars($detail['name']); ?></td>
                                                <td data-label="ประเภท"><?php echo htmlspecialchars($detail['type']); ?></td>
                                                <td data-label="วันที่บันทึก"><?php echo htmlspecialchars($detail['updated_at']); ?></td>
                                                <td data-label="ผู้บันทึก"><?php echo htmlspecialchars($detail['first_name']); ?></td>
                                                <td data-label="จัดการ">
                                                    <button class="btn btn-info btn-sm view-btn" data-id="<?php echo $detail['necklace_detail_id']; ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>

                                                    <?php if ($current_user_id == $detail['updated_users_id'] || $is_admin || $user_dept === 'หัวหน้าช่าง'): ?>
                                                        <!-- เพิ่มปุ่มแชร์ -->
                                                        <button type="button" class="btn btn-primary btn-sm share-btn" data-id="<?php echo $detail['necklace_detail_id']; ?>" data-name="<?php echo htmlspecialchars($detail['name']); ?>">
                                                            <i class="fas fa-share-alt"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-warning btn-sm" onclick="editNecklace(<?php echo $detail['necklace_detail_id']; ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteNecklace(<?php echo $detail['necklace_detail_id']; ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-secondary btn-sm" disabled title="คุณไม่มีสิทธิ์แก้ไขรายการนี้">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-secondary btn-sm" disabled title="คุณไม่มีสิทธิ์ลบรายการนี้">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>

                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal สำหรับการแชร์ข้อมูล -->
    <div class="modal fade" id="ShareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shareModalLabel">แชร์รายการสร้อย</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="shareForm">
                        <input type="hidden" id="share_necklace_id" name="necklace_id">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h6>รายการสร้อย: <span id="share_necklace_name" class="text-primary"></span></h6>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="share_users" class="form-label">เลือกผู้ใช้ที่ต้องการแชร์</label>
                                <select id="share_users" name="users_id[]" class="form-control select2" multiple>
                                    <!-- ตัวเลือกผู้ใช้จะถูกเพิ่มด้วย JavaScript -->
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <h6>ผู้ใช้ที่มีสิทธิ์เข้าถึง</h6>
                            <div class="table-responsive mt-2">
                                <table class="table table-bordered table-sm" id="currentSharingTable">
                                    <thead>
                                        <tr class="text-center">
                                            <th>ชื่อผู้ใช้</th>
                                            <th>แผนก</th>
                                            <th>ผู้แชร์</th>
                                            <th>วันที่แชร์</th>
                                            <th>จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- ข้อมูลผู้ใช้ที่มีสิทธิ์จะถูกเพิ่มด้วย JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-primary" id="btn_save_share">บันทึกการแชร์</button>
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
    <script src="assets/plugins/select2/js/select2.min.js"></script>
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="js/necklace-form.js"></script>
    <script src="js/necklace-crud.js"></script>
    <script src="js/necklace-table.js"></script>
    <script src="js/necklace-sharing.js"></script>
    <script>
        $(document).ready(function() {
            // ตัวแปร global สำหรับ DataTable
            var necklaceTable;

            // ฟังก์ชันแสดงรูปภาพเต็ม
            window.showFullImage = function(imageSrc, title) {
                $('#fullImage').attr('src', imageSrc);
                $('#imageModalTitle').text(title);
                $('#imageModal').modal('show');
            };

            // กำหนดรูปแบบการแสดงรายละเอียดเพิ่มเติมเมื่อคลิก
            function formatDetails(d) {
                let necklaceId = $(d).data('necklace-id');

                return `
                    <div class="child-row py-3 px-2 bg-light rounded">
                        <div class="row g-3">
                            <!-- สร้อยต้นแบบ -->
                            <div class="col-md-6 col-lg-3">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="border-bottom pb-2 mb-3">สร้อยต้นแบบ</h6>
                                    <p><strong>หนา:</strong> <span class="ptt-thick"></span></p>
                                    <p><strong>ไส้:</strong> <span class="ptt-core"></span></p>
                                    <p><strong>อัตราส่วน:</strong> <span class="ptt-ratio"></span></p>
                                </div>
                            </div>

                            <!-- ลวดอกาโฟโต้ -->
                            <div class="col-md-6 col-lg-3">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="border-bottom pb-2 mb-3">ลวดอกาโฟโต้ (ยังไม่สกัด)</h6>
                                    <p><strong>ยังไม่สกัด.รูลวด:</strong> <span class="agpt-thick"></span></p>
                                    <p><strong>ยังไม่สกัด.นน.ลวดก่อนสกัด:</strong> <span class="agpt-core"></span></p>
                                    <p><strong>ยังไม่สกัด.ค.ยาวลวด:</strong> <span class="agpt-ratio"></span></p>
                                </div>
                            </div>

                            <!-- ทองอย่างเดียว -->
                            <div class="col-md-6 col-lg-3">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="border-bottom pb-2 mb-3">สร้อยต้นแบบ (นน.ทองอย่างเดียว)</h6>
                                    <p><strong>สร้อยยาว:</strong> <span class="true-length"></span></p>
                                    <p><strong>น้ำหนัก:</strong> <span class="true-weight"></span></p>
                                </div>
                            </div>

                            <!-- สัดส่วนสร้อย -->
                            <div class="col-md-6 col-lg-3">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="border-bottom pb-2 mb-3">สัดส่วนสร้อย</h6>
                                    <p><strong>รูลวด:</strong> <span class="proportions-size"></span></p>
                                    <p><strong>หน้ากว้าง (มม.):</strong> <span class="proportions-width"></span></p>
                                    <p><strong>หนา (มม.):</strong> <span class="proportions-thick"></span></p>
                                    <p><strong>Ratio หน้ากว้าง:</strong> <span class="ratio-width"></span></p>
                                    <p><strong>Ratio หนา:</strong> <span class="ratio-thick"></span></p>
                                    <p><strong>หมายเหตุ:</strong> <span class="comment"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>`;
            }
            // สร้าง DataTable
            function initializeDataTable() {
                necklaceTable = $('#necklaceTable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
                    },
                    responsive: true,
                    order: [
                        [2, 'asc']
                    ], // เรียงตามชื่อลายสร้อย
                    columnDefs: [{
                            orderable: false,
                            targets: [0, 1, 6]
                        }, // ห้ามเรียงลำดับคอลัมน์ expand, รูปภาพ และปุ่มจัดการ
                    ]
                });

                // ปรับปรุง UI สำหรับการค้นหา
                $('#necklaceTable_filter input').attr('placeholder', 'ค้นหา...');
            }

            // คำนวณค่า ratio จากข้อมูล
            function calculateRatios(detail) {
                // คำนวณ ratio หน้ากว้าง: proportions_width / proportions_size
                let ratioWidth = '-';
                if (detail.proportions_width && detail.proportions_size) {
                    const width = parseFloat(detail.proportions_width);
                    const size = parseFloat(detail.proportions_size);
                    if (!isNaN(width) && !isNaN(size) && size > 0) {
                        ratioWidth = (width / size).toFixed(2);
                    }
                }

                // คำนวณ ratio หนา: proportions_thick / proportions_size
                let ratioThick = '-';
                if (detail.proportions_thick && detail.proportions_size) {
                    const thick = parseFloat(detail.proportions_thick);
                    const size = parseFloat(detail.proportions_size);
                    if (!isNaN(thick) && !isNaN(size) && size > 0) {
                        ratioThick = (thick / size).toFixed(2);
                    }
                }

                return {
                    ratioWidth: ratioWidth,
                    ratioThick: ratioThick
                };
            }

            // จัดการเมื่อคลิกปุ่มขยายรายละเอียด
            function handleDetailsClick() {
                var tr = $(this).closest('tr');
                var row = necklaceTable.row(tr);
                var necklaceId = tr.data('necklace-id');

                if (row.child.isShown()) {
                    // ซ่อนรายละเอียด
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // แสดงรายละเอียด
                    row.child(formatDetails(tr[0])).show();
                    tr.addClass('shown');

                    // ดึงข้อมูลรายละเอียดผ่าน AJAX
                    $.ajax({
                        url: 'actions/get_necklace.php',
                        type: 'GET',
                        data: {
                            id: necklaceId
                        },
                        dataType: 'json',
                        success: function(response) {
                            // เติมข้อมูลลงในรายละเอียด
                            var child = row.child();
                            if (response.success) {
                                var detail = response.necklace;
                                // เติมข้อมูลสร้อยพื้นฐาน
                                child.find('.ptt-thick').text(detail.ptt_thick || '-');
                                child.find('.ptt-core').text(detail.ptt_core || '-');
                                child.find('.ptt-ratio').text(detail.ptt_ratio || '-');

                                child.find('.agpt-thick').text(detail.agpt_thick || '-');
                                child.find('.agpt-core').text(detail.agpt_core || '-');
                                child.find('.agpt-ratio').text(detail.agpt_ratio || '-');

                                child.find('.true-length').text(detail.true_length || '-');
                                child.find('.true-weight').text(detail.true_weight || '-');

                                child.find('.proportions-size').text(detail.proportions_size || '-');
                                child.find('.proportions-width').text(detail.proportions_width || '-');
                                child.find('.proportions-thick').text(detail.proportions_thick || '-');

                                // คำนวณค่า ratio ใหม่หากไม่มีในข้อมูล
                                let ratios;
                                if (detail.ratio_width && detail.ratio_thick) {
                                    // ใช้ค่าที่มีอยู่แล้วในฐานข้อมูล
                                    ratios = {
                                        ratioWidth: detail.ratio_width ? Number(detail.ratio_width).toFixed(2) : '-',
                                        ratioThick: detail.ratio_thick ? Number(detail.ratio_thick).toFixed(2) : '-'
                                    };
                                } else {
                                    // คำนวณค่าใหม่
                                    ratios = calculateRatios(detail);
                                }

                                child.find('.ratio-width').text(ratios.ratioWidth);
                                child.find('.ratio-thick').text(ratios.ratioThick);
                                child.find('.comment').text(detail.comment || '-');

                                // เติมข้อมูล TBS
                                var tbsTable = child.find('.tbs-table tbody');
                                tbsTable.empty();

                                // ตรวจสอบและแสดงข้อมูล TBS
                                var tbsData = detail.tbs;
                                if (Array.isArray(tbsData) && tbsData.length > 0) {
                                    tbsData.forEach(function(tbs) {
                                        tbsTable.append(
                                            '<tr>' +
                                            '<td>' + (tbs.tbs_name || '-') + '</td>' +
                                            '<td>' + (tbs.tbs_before || '-') + '</td>' +
                                            '<td>' + (tbs.tbs_after || '-') + '</td>' +
                                            '</tr>'
                                        );
                                    });
                                } else {
                                    tbsTable.append('<tr><td colspan="3" class="text-center">ไม่พบข้อมูล TBS</td></tr>');
                                }
                            } else {
                                child.find('div.child-row').html('<div class="alert alert-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + response.message + '</div>');
                            }
                        },
                        error: function(xhr, status, error) {
                            // Debug: ดูข้อผิดพลาดที่เกิดขึ้น
                            console.error("API Error:", error);
                            console.error("Status:", status);
                            console.error("Response:", xhr.responseText);

                            var child = row.child();
                            child.find('div.child-row').html('<div class="alert alert-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + error + '</div>');
                        }
                    });
                }
            }

            // จัดการเมื่อคลิกปุ่มดูรายละเอียด
            function handleViewButtonClick() {
                var tr = $(this).closest('tr');
                var row = necklaceTable.row(tr);
                var td = tr.find('td.details-control');

                // จำลองการกดปุ่ม +/-
                if (row.child.isShown()) {
                    // ถ้าเปิดอยู่แล้วให้ปิด
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // ถ้าปิดอยู่ให้เปิด
                    td.trigger('click');
                }
            }

            // รีเซ็ตฟอร์ม
            window.resetForm = function() {
                const form = document.getElementById("necklaceForm");
                form.reset();
                document.getElementById("necklace_detail_id").value = '';

                // ถ้ามีการแสดงรูปตัวอย่าง ให้ซ่อน
                if (document.getElementById("preview")) {
                    document.getElementById("preview").style.display = 'none';
                }

                // ถ้ามี TBS rows ให้รีเซ็ต
                if ($('.tbs-rows').length > 0) {
                    $('.tbs-rows').empty();
                    // ถ้ามีฟังก์ชัน addTbsRow ให้เรียกเพื่อเพิ่มแถวว่าง
                    if (typeof addTbsRow === 'function') {
                        addTbsRow();
                    }
                }

                // ตรวจสอบสถานะฟอร์มหลัง reset
                $('select[name="type"]').trigger("change");
                $('select[name="shapeshape_necklace"]').trigger("change");
            };

            // ตั้งค่า Event Handlers
            function setupEventHandlers() {
                // การแสดงรายละเอียดเพิ่มเติม
                $('#necklaceTable tbody').on('click', 'td.details-control', handleDetailsClick);

                // ปุ่มดูรายละเอียด
                $('#necklaceTable').on('click', '.view-btn', handleViewButtonClick);

                // ปรับขนาด DataTable เมื่อแสดงหน้าจอ
                $(window).resize(function() {
                    necklaceTable.columns.adjust().responsive.recalc();
                });
            }

            // เริ่มการทำงาน
            function initialize() {
                initializeDataTable();
                setupEventHandlers();
            }

            // เรียกใช้งานฟังก์ชันเริ่มต้น
            initialize();
        });
    </script>
</body>

</html>