<?php
require 'functions/check_login.php';
require 'config/db_connect.php';
require 'functions/management_percent_necklace.php';

// เก็บ ID ของผู้ใช้ปัจจุบัน
$current_user_id = isset($_SESSION['recipenecklace_users_id']) ? $_SESSION['recipenecklace_users_id'] : 0;

// ดึงข้อมูลทั้งหมดมาแสดง
$percent_necklaces = get_percent_necklace($pdo);

// ดึงรายชื่อผู้สร้าง (users) ทั้งหมดเพื่อทำตัวกรอง
$creators = [];
foreach ($percent_necklaces as $necklace) {
    if (!empty($necklace['first_name']) && !empty($necklace['users_id'])) {
        if (!isset($creators[$necklace['users_id']])) {
            $creators[$necklace['users_id']] = $necklace['first_name'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบคำนวณสัดส่วน % สร้อย</title>
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
        .necklace-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 25px;
            border-radius: 10px;
            overflow: hidden;
        }

        .necklace-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
            height: 180px;
            object-fit: cover;
            background-color: #f8f9fa;
        }

        .card-img-container {
            position: relative;
            overflow: hidden;
            height: 180px;
        }

        .no-image {
            display: flex;
            height: 180px;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            color: #6c757d;
        }

        .card-body {
            padding: 15px;
        }

        .necklace-title {
            font-weight: 600;
            margin-bottom: 8px;
            height: 48px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
        }

        .meta-info {
            color: #6c757d;
            font-size: 0.85rem;
        }

        .btn-view {
            width: 100%;
        }

        .search-container {
            margin-bottom: 20px;
        }

        .filter-buttons {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .filter-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .btn-filter {
            background-color: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
            padding: 6px 15px;
            position: relative;
        }

        .btn-filter.active {
            font-weight: bold;
            border-width: 2px;
            border-color: #333;
            box-shadow: none;
        }

        .btn-filter.active::after {
            content: " ✓";
        }

        .btn-sort {
            padding: 4px 12px;
            font-size: 0.85rem;
        }

        .btn-sort.active {
            font-weight: bold;
            background-color: #17a2b8;
            color: white;
        }

        .stats-box {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
        }

        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 20px;
            color: white;
        }

        .stats-details h5 {
            margin-bottom: 5px;
            font-size: 18px;
        }

        .stats-details p {
            margin-bottom: 0;
            color: #6c757d;
            font-size: 14px;
        }

        .creator-filter {
            display: none;
            margin-top: 10px;
        }

        .no-data {
            padding: 50px 0;
            text-align: center;
        }

        .no-data i {
            font-size: 48px;
            color: #d9d9d9;
            margin-bottom: 15px;
            display: block;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 38px;
            padding: 5px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        /* ขยายความกว้างของ dropdown */
        .creator-filter {
            width: 100%;
            max-width: 400px;
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
                            <h3 class="page-title">ระบบคำนวณสัดส่วน % สร้อย</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">ระบบคำนวณสัดส่วน % สร้อย</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- ส่วนค้นหาและกรอง -->
                <div class="card">
                    <div class="card-body">
                        <div class="search-container">
                            <div class="row">
                                <div class="col-md-8 mt-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="searchInput" placeholder="ค้นหาสร้อย...">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search"></i> ค้นหา
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mt-2 text-end">
                                    <a href="percent_necklace_management.php" class="btn btn-success">
                                        <i class="fas fa-plus-circle"></i> เพิ่มข้อมูลใหม่
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="filter-buttons">
                            <!-- ปุ่มกรองข้อมูลเดิม -->
                            <button class="btn btn-outline-secondary btn-filter active" data-filter="all">
                                <i class="fas fa-layer-group me-1"></i> ทั้งหมด
                            </button>
                            <button class="btn btn-outline-secondary btn-filter" data-filter="your">
                                <i class="fas fa-user me-1"></i> ข้อมูลของคุณ
                            </button>
                            <button class="btn btn-outline-secondary btn-filter" data-filter="by-creator">
                                <i class="fas fa-users me-1"></i> ตามผู้บันทึก
                            </button>
                        </div>
                        <!-- ส่วนกรองตามผู้สร้าง -->
                        <div class="creator-filter" id="creatorFilter">
                            <div class="row">
                                <div class="col-md-12">
                                    <select class="form-control select2" id="creatorSelect">
                                        <option value="">-- เลือกผู้บันทึกข้อมูล --</option>
                                        <?php foreach ($creators as $user_id => $name): ?>
                                            <option value="<?php echo $user_id; ?>"><?php echo htmlspecialchars($name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- เพิ่มเส้นแบ่งระหว่างกลุ่มปุ่ม -->
                        <div class="border-top my-2 mt-2 w-100"></div>

                        <!-- ปุ่มเรียงลำดับเพิ่มเติม -->
                        <div class="w-100 mb-1 text-secondary small">เรียงลำดับตาม:</div>
                        <button class="btn btn-outline-info btn-sort" data-sort="name-asc">
                            <i class="fas fa-sort-alpha-down me-1"></i> ชื่อ (A-Z)
                        </button>
                        <button class="btn btn-outline-info btn-sort" data-sort="name-desc">
                            <i class="fas fa-sort-alpha-up me-1"></i> ชื่อ (Z-A)
                        </button>
                        <button class="btn btn-outline-info btn-sort" data-sort="date-asc">
                            <i class="fas fa-sort-numeric-down me-1"></i> เก่าสุด
                        </button>
                        <button class="btn btn-outline-info btn-sort" data-sort="date-desc">
                            <i class="fas fa-sort-numeric-up me-1"></i> ใหม่สุด
                        </button>
                        <button class="btn btn-outline-info btn-sort" data-sort="grams-asc">
                            <i class="fas fa-weight-hanging me-1"></i> น้ำหนักน้อย→มาก
                        </button>
                        <button class="btn btn-outline-info btn-sort" data-sort="grams-desc">
                            <i class="fas fa-weight-hanging fa-flip-vertical me-1"></i> น้ำหนักมาก→น้อย
                        </button>

                    </div>
                </div>

                <!-- แสดงรายการสร้อยในรูปแบบการ์ด -->
                <div class="row" id="necklaceContainer">
                    <?php foreach ($percent_necklaces as $necklace) : ?>
                        <!-- แก้ไขส่วนการ์ดแสดงรายการ -->
                        <div class="col-md-3 col-sm-6 necklace-item" data-name="<?php echo htmlspecialchars($necklace['pn_name']); ?>" data-date="<?php echo $necklace['updated_at']; ?>" data-creator="<?php echo $necklace['users_id']; ?>" data-grams="<?php echo $necklace['pn_grams']; ?>">
                            <div class="card necklace-card">
                                <div class="card-img-container">
                                    <?php if (!empty($necklace['image'])) : ?>
                                        <img src="uploads/img/percent_necklace/<?php echo htmlspecialchars($necklace['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($necklace['pn_name']); ?>" onclick="showFullImage('uploads/img/percent_necklace/<?php echo htmlspecialchars($necklace['image']); ?>', '<?php echo htmlspecialchars($necklace['pn_name']); ?>')">
                                    <?php else : ?>
                                        <div class="no-image">
                                            <i class="fas fa-image fa-3x"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title necklace-title"><?php echo htmlspecialchars($necklace['pn_name']); ?></h5>
                                    <div class="card-text">
                                        <div class="meta-info mb-2">
                                            <i class="fas fa-weight"></i> <?php echo number_format(floatval($necklace['pn_grams']), 2); ?> กรัม
                                        </div>
                                        <div class="meta-info mb-2">
                                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($necklace['first_name'] ?? 'ไม่ระบุ'); ?>
                                        </div>
                                        <div class="meta-info mb-2">
                                            <i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($necklace['updated_at'])); ?>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-info btn-sm" onclick="viewPercent(<?php echo $necklace['pn_id']; ?>)">
                                                <i class="fas fa-eye"></i> ดูรายละเอียด
                                            </button>
                                            <a href="percent_necklace.php?pn_id=<?php echo $necklace['pn_id']; ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-calculator"></i> คำนวณ
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- แสดงเมื่อไม่พบข้อมูล -->
                <div id="noDataMessage" class="no-data" style="display: none;">
                    <i class="fas fa-exclamation-circle"></i>
                    <h4>ไม่พบข้อมูล</h4>
                    <p>ไม่พบรายการสร้อยที่ตรงกับเงื่อนไขที่ค้นหา</p>
                </div>

                <?php if (empty($percent_necklaces)) : ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <h5>ไม่พบข้อมูล</h5>
                        <p>ยังไม่มีรายการสร้อยในระบบ กรุณาเพิ่มข้อมูลก่อน</p>
                        <a href="percent_necklace_management.php" class="btn btn-primary mt-2">
                            <i class="fas fa-plus"></i> เพิ่มข้อมูลใหม่
                        </a>
                    </div>
                <?php endif; ?>
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
    <?php include 'modal/percent_necklace_management_modal.php'; ?>

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
            // เก็บข้อมูล user_id ของผู้ใช้ปัจจุบัน
            const currentUserId = <?php echo $current_user_id; ?>;

            // Initialize select2 - ปรับให้เข้ากับ Bootstrap 5
            $('.select2').select2({
                width: '100%',
                dropdownParent: $('#creatorFilter')
            });

            // ฟังก์ชันแสดงรูปภาพเต็ม
            window.showFullImage = function(imageSrc, title) {
                $('#fullImage').attr('src', imageSrc);
                $('#imageModalTitle').text(title);
                $('#imageModal').modal('show');
            };

            // ค้นหาด้วยชื่อ
            $("#searchInput").on("keyup", function() {
                filterItems();
            });

            $(".btn-filter").on("click", function(e) {
                e.preventDefault();

                // เอาคลาส active ออกจากปุ่มทั้งหมด และเพิ่มให้ปุ่มที่คลิก
                $(".btn-filter").removeClass("active");
                $(this).addClass("active");

                const filter = $(this).data("filter");

                // ซ่อน/แสดงส่วนกรองตามผู้สร้าง
                if (filter === 'by-creator') {
                    $('#creatorFilter').slideDown();
                } else {
                    $('#creatorFilter').slideUp();
                }

                // กรองรายการตามตัวกรอง
                filterItems();
            });

            // การเรียงลำดับ
            $(".btn-sort").on("click", function(e) {
                e.preventDefault();

                // เอาคลาส active ออกจากปุ่มทั้งหมด และเพิ่มให้ปุ่มที่คลิก
                $(".btn-sort").removeClass("active");
                $(this).addClass("active");

                const sortType = $(this).data("sort");
                sortItems(sortType);
            });

            // ฟังก์ชันเรียงข้อมูล
            function sortItems(sortType) {
                const container = $('#necklaceContainer');
                const items = container.children('.necklace-item').get();

                items.sort(function(a, b) {
                    // เรียงตามชื่อ A-Z
                    if (sortType === 'name-asc') {
                        const nameA = $(a).data('name').toLowerCase();
                        const nameB = $(b).data('name').toLowerCase();
                        return nameA.localeCompare(nameB);
                    }
                    // เรียงตามชื่อ Z-A
                    else if (sortType === 'name-desc') {
                        const nameA = $(a).data('name').toLowerCase();
                        const nameB = $(b).data('name').toLowerCase();
                        return nameB.localeCompare(nameA);
                    }
                    // เรียงตามวันที่เก่า→ใหม่
                    else if (sortType === 'date-asc') {
                        const dateA = $(a).data('date');
                        const dateB = $(b).data('date');
                        return dateA.localeCompare(dateB);
                    }
                    // เรียงตามวันที่ใหม่→เก่า
                    else if (sortType === 'date-desc') {
                        const dateA = $(a).data('date');
                        const dateB = $(b).data('date');
                        return dateB.localeCompare(dateA);
                    }
                    // เรียงตามกรัมน้อย→มาก
                    else if (sortType === 'grams-asc') {
                        const gramsA = parseFloat($(a).data('grams') || 0);
                        const gramsB = parseFloat($(b).data('grams') || 0);
                        return gramsA - gramsB;
                    }
                    // เรียงตามกรัมมาก→น้อย
                    else if (sortType === 'grams-desc') {
                        const gramsA = parseFloat($(a).data('grams') || 0);
                        const gramsB = parseFloat($(b).data('grams') || 0);
                        return gramsB - gramsA;
                    }
                });

                $.each(items, function(index, item) {
                    container.append(item);
                });
            }

            // อีเวนต์เมื่อเลือกผู้สร้าง
            $('#creatorSelect').on('change', function() {
                filterItems();
            });

            // ฟังก์ชันตรวจสอบว่ามีรายการแสดงหรือไม่
            function checkVisibleItems() {
                const visibleItems = $('.necklace-item:visible').length;
                if (visibleItems === 0) {
                    $('#noDataMessage').show();
                } else {
                    $('#noDataMessage').hide();
                }
            }

            // ฟังก์ชันกรองรายการตามเงื่อนไข
            function filterItems() {
                const searchText = $('#searchInput').val().toLowerCase();
                const currentFilter = $('.btn-filter.active').data('filter');
                const selectedCreator = $('#creatorSelect').val();

                $('.necklace-item').hide();

                $('.necklace-item').each(function() {
                    const item = $(this);
                    const itemName = item.data('name').toLowerCase();
                    const itemCreator = item.data('creator');
                    const itemDate = item.data('date');

                    let showBySearch = true;
                    let showByFilter = true;
                    let showByCreator = true;

                    // กรองตามคำค้นหา
                    if (searchText) {
                        showBySearch = itemName.includes(searchText);
                    }

                    if (currentFilter === 'your') {
                        showByFilter = (itemCreator == currentUserId);
                    } else if (currentFilter === 'by-creator' && selectedCreator) {
                        showByCreator = (itemCreator == selectedCreator);
                    } else {
                        // กรณี all หรือ by-creator แต่ไม่ได้เลือกผู้สร้าง
                        showByCreator = true;
                    }

                    // แสดงเมื่อผ่านเงื่อนไขทั้งหมด
                    if (showBySearch && showByFilter && showByCreator) {
                        item.show();
                    }
                });

                checkVisibleItems();
            }

            // เรียงลำดับรายการตามวันที่
            function sortByDate() {
                const container = $('#necklaceContainer');
                const items = container.children('.necklace-item').get();

                items.sort(function(a, b) {
                    const dateA = $(a).data('date');
                    const dateB = $(b).data('date');
                    return dateB.localeCompare(dateA); // เรียงจากใหม่ไปเก่า
                });

                $.each(items, function(index, item) {
                    container.append(item);
                });
            }

            // ตั้งค่าเริ่มต้น - เลือกตัวกรองทั้งหมด
            $('#filter-all').prop('checked', true).trigger('change');
        });

        function viewPercent(id) {
            $.getJSON(`actions/get_percent.php?id=${id}`, function(data) {
                if (!data.success) {
                    Swal.fire({
                        icon: "error",
                        title: "เกิดข้อผิดพลาด",
                        text: data.message || "ไม่สามารถโหลดข้อมูลได้",
                    });
                    return;
                }

                let html = `
            <div class="row mb-3">
                <div class="col-md-4 text-center">
                    ${
                    data.percent.image
                        ? `<img src="uploads/img/percent_necklace/${data.percent.image}" class="img-thumbnail mb-2" style="max-height:150px;">`
                        : '<i class="fas fa-image fa-4x text-muted"></i>'
                    }
                </div>
                <div class="col-md-8">
                    <h5>${data.percent.pn_name}</h5>
                    <div>น้ำหนัก (กรัม): <b>${data.percent.pn_grams}</b></div>
                    <div>บาท: <b>${(parseFloat(data.percent.pn_grams) / 15.2).toFixed(
                    2
                    )}</b></div>
                </div>
            </div>
            <hr>
            <h6>รายละเอียด</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ประเภท</th>
                            <th>ชื่อ</th>
                            <th>น้ำหนัก (กรัม)</th>
                            <th>%</th>
                            <th>ความยาว</th>
                        </tr>
                    </thead>
                    <tbody>`;

                // Calculate total weight for percentage calculation
                let totalWeight = 0;
                if (data.specialDetails?.length) {
                    data.specialDetails.forEach(
                        (d) => (totalWeight += parseFloat(d.pnd_weight_grams) || 0)
                    );
                }
                if (data.details?.length) {
                    data.details.forEach(
                        (d) => (totalWeight += parseFloat(d.pnd_weight_grams) || 0)
                    );
                }

                // รายการพิเศษ (special items)
                if (data.specialDetails?.length) {
                    data.specialDetails.forEach(function(d) {
                        const percent = (
                            ((parseFloat(d.pnd_weight_grams) || 0) / totalWeight) *
                            100
                        ).toFixed(2);
                        html += `<tr class="table-light">
            <td><span class="badge bg-secondary">พิเศษ</span></td>
            <td>${d.pnd_name}</td>
            <td>${d.pnd_weight_grams}</td>
            <td>${percent}%</td>
            <td>${d.pnd_long_inch || "-"}</td>
            </tr>`;
                    });
                }

                // รายการปกติ (regular items)
                if (data.details?.length) {
                    data.details.forEach(function(d) {
                        const percent = (
                            ((parseFloat(d.pnd_weight_grams) || 0) / totalWeight) *
                            100
                        ).toFixed(2);
                        html += `<tr>
                <td>${d.pnd_type || "-"}</td>
                <td>${d.pnd_name}</td>
                <td>${d.pnd_weight_grams}</td>
                <td>${percent}%</td>
                <td>${d.pnd_long_inch || "-"}</td>
            </tr>`;
                    });
                }

                // รายการรวม (total row)
                html += `<tr class="table-primary">
            <td colspan="2" class="text-end fw-bold">รวม</td>
            <td class="fw-bold">${totalWeight.toFixed(2)}</td>
            <td class="fw-bold">100%</td>
            <td></td>
            </tr>`;

                html += `</tbody></table></div>`;

                // ค้นหาค่า reference สำหรับการคำนวณ ratio
                let referenceWidth = null;
                if (data.details?.length) {
                    // หาค่ากว้างแรกของสร้อย
                    for (const d of data.details) {
                        if (d.pnd_type === "สร้อย" && d.parts?.scale_wire_weight) {
                            referenceWidth = parseFloat(d.parts.scale_wire_weight);
                            break;
                        }
                    }
                }

                // Display ratio information
                if (data.details?.some((d) => d.parts)) {
                    html += `<h6 class="mt-4">ข้อมูลสัดส่วน (Ratio)</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ชื่อ</th>
                            <th>ประเภท</th>
                            <th>ขนาด</th>
                            <th>Ratio</th>
                        </tr>
                    </thead>
                    <tbody>`;

                    // First show necklace items
                    const necklaceItems = data.details.filter(
                        (d) => d.pnd_type === "สร้อย" && d.parts
                    );
                    if (necklaceItems.length > 0) {
                        html += `<tr class="table-secondary"><td colspan="4" class="fw-bold">สร้อย</td></tr>`;

                        necklaceItems.forEach(function(d) {
                            const wireWidth = parseFloat(d.parts.scale_wire_weight) || 0;
                            const wireThick = parseFloat(d.parts.scale_wire_thick) || 0;

                            html += `<tr>
                <td>${d.pnd_name}</td>
                <td>สร้อย</td>
                <td>
                    <div>รูลวด: ${d.parts.wire_hole || "-"}</div>
                    <div>หนา: ${d.parts.wire_thick || "-"}</div>
                    <div>ไส้: ${d.parts.wire_core || "-"}</div>
                    <div>กว้าง: ${d.parts.scale_wire_weight || "-"} มม.</div>
                    <div>หนา: ${d.parts.scale_wire_thick || "-"} มม.</div>
                </td>
                <td></td>
            </tr>`;
                        });
                    }

                    // Then show parts items
                    const partsItems = data.details.filter(
                        (d) => d.pnd_type === "อะไหล่" && d.parts
                    );
                    if (partsItems.length > 0) {
                        html += `<tr class="table-secondary"><td colspan="4" class="fw-bold">อะไหล่</td></tr>`;

                        partsItems.forEach(function(d) {
                            const partsWidth = parseFloat(d.parts.parts_weight) || 0;
                            const partsHeight = parseFloat(d.parts.parts_height) || 0;
                            const partsThick = parseFloat(d.parts.parts_thick) || 0;

                            let ratioInfo = "";
                            if (referenceWidth && partsWidth) {
                                const widthRatio = (partsWidth / referenceWidth).toFixed(2);
                                ratioInfo += `<div>กว้าง: ${widthRatio}</div>`;
                            }

                            if (referenceWidth && partsHeight) {
                                const heightRatio = (partsHeight / referenceWidth).toFixed(2);
                                ratioInfo += `<div>สูง: ${heightRatio}</div>`;
                            }

                            if (referenceWidth && partsThick) {
                                const thickRatio = (partsThick / referenceWidth).toFixed(2);
                                ratioInfo += `<div>หนา: ${thickRatio}</div>`;
                            }

                            html += `<tr>
                <td>${d.pnd_name}</td>
                <td>อะไหล่</td>
                <td>
                    <div>กว้าง: ${d.parts.parts_weight || "-"} มม.</div>
                    <div>สูง: ${d.parts.parts_height || "-"} มม.</div>
                    <div>หนา: ${d.parts.parts_thick || "-"} มม.</div>
                </td>
                <td>${ratioInfo || "-"}</td>
            </tr>`;
                        });
                    }

                    html += `</tbody></table></div>`;
                }

                $("#percentViewFooter").html(`
            <a href="percent_necklace.php?pn_id=${id}" class="btn btn-success">
            <i class="fas fa-calculator"></i> ไปหน้าคำนวน
            </a>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            `);

                $("#percentViewContent").html(html);
                $("#percentViewModal").modal("show");
            }).fail(function(jqXHR, textStatus, errorThrown) {
                Swal.fire({
                    icon: "error",
                    title: "เกิดข้อผิดพลาด",
                    text: errorThrown || "ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้",
                });
            });
        }
    </script>
</body>

</html>