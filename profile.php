<?php
require 'config/db_connect.php';
require 'functions/check_login.php';
require 'functions/management_user.php';

$users_id = $_SESSION['recipenecklace_users_id'];

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$user = getUserById($pdo, $users_id);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
    <meta name="description" content="POS - Bootstrap Admin Template" />
    <meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, html5, responsive" />
    <meta name="author" content="Dreamguys - Bootstrap Admin Template" />
    <meta name="robots" content="noindex, nofollow" />
    <title>จัดการโปรไฟล์</title>

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css" />
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        /* ปรับปรุงไม้บรรทัดให้เรียบง่ายขึ้น */
        .calibration-ruler {
            height: 50px;
            background-color: #fff;
            position: relative;
            border: 1px solid #e0e0e0;
            margin: 20px 0;
            padding: 5px 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ลบเส้นแบ่งและตัวเลข */
        .ruler-unit {
            position: absolute;
            font-size: 14px;
            color: #333;
            font-weight: bold;
        }

        /* เพิ่มกรอบอ้างอิงสำหรับแถบ 10mm */
        .reference-object {
            margin: 20px auto;
            text-align: center;
            padding: 5px;
            color: #333;
            max-width: 200px;
        }

        /* แถบสี 10mm */
        .reference-bar {
            height: 30px;
            background-color: #007bff;
            margin: 0 auto;
            border: 2px solid #0056b3;
            position: relative;
        }

        .reference-bar:after {
            content: '10 มม.';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: bold;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.5);
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
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="card mb-0">
                            <div class="card-body">
                                <h4 class="card-title">โปรไฟล์ของคุณ</h4>
                                <div class="table-responsive dataview">
                                    <!-- แสดงค่าที่เก็บไว้ -->
                                    <?php if ($user): ?>
                                        <div class="text-center position-relative">
                                            <i class="me-2" data-feather="user" width="150" height="150"></i>
                                            <div class="overlay-text" style="display: none; color: green;">สามารถแก้ไขรูปโปรไฟล์</div>
                                            <h3><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>แผนก:</strong> <?php echo htmlspecialchars($user['users_depart']); ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>ชื่อผู้ใช้:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                                                </div>
                                            </div>
                                            <button class="btn btn-primary m-4" data-bs-toggle="modal" data-bs-target="#editProfileModal">แก้ไขโปรไฟล์</button>
                                        </div>
                                    <?php else: ?>
                                        <p>ไม่สามารถดึงข้อมูลผู้ใช้ได้</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- เพิ่มส่วนการปรับแต่งค่า PxPerMM -->
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">การปรับแต่งการแสดงผลขนาด</h4>

                                <div class="calibration-container">
                                    <div class="alert alert-primary">
                                        <i class="fa fa-info-circle me-2"></i>
                                        <strong>วิธีปรับแต่งขนาด:</strong> ปรับค่าจนกว่าแถบสีน้ำเงินด้านล่างจะมีความยาว 10 มิลลิเมตร (วัดด้วยไม้บรรทัดจริง)
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <!-- ขนาด 10mm -->
                                            <div class="reference-object">
                                                <div id="mm10-block" class="reference-bar"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ไม้บรรทัดขนาด 10 มม. -->
                                    <div class="calibration-ruler" id="mm-ruler">
                                        <div class="ruler-unit">10 มม.</div>
                                    </div>

                                    <div class="row mt-4 align-items-center">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pxPerMMValue">ค่า Pixel ต่อ มิลลิเมตร</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" min="0.1" max="10" class="form-control" id="pxPerMMValue">
                                                    <span class="input-group-text">px/mm</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>ปรับขนาดด้วย Slider</label>
                                                <input type="range" class="form-range" min="0.5" max="10" step="0.01" id="pxPerMMSlider">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12 d-flex justify-content-end gap-2">
                                            <button class="btn btn-info" id="autoCalibrate"><i class="fa fa-magic me-1"></i>คำนวณอัตโนมัติ</button>
                                            <button class="btn btn-secondary" id="resetCalibration">คืนค่าเริ่มต้น</button>
                                            <button class="btn btn-primary" id="saveCalibration">บันทึกการปรับแต่ง</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal แก้ไขโปรไฟล์ -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">แก้ไขโปรไฟล์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" id="editUserId" name="users_id" value="<?php echo htmlspecialchars($user['users_id']); ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editFirstName">ชื่อ</label>
                                    <input type="text" class="form-control" id="editFirstName" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editLastName">นามสกุล</label>
                                    <input type="text" class="form-control" id="editLastName" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editLevel">ระดับสิทธิ</label>
                                    <input type="text" class="form-control" id="editLevel" name="users_level" value="<?php echo htmlspecialchars($user['users_level']); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_users_depart" class="form-label">แผนก</label>
                                <input type="text" class="form-control" id="edit_users_depart" name="users_depart" value="<?php echo htmlspecialchars($user['users_depart']); ?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <input type="hidden" id="editUsername" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                            <input type="hidden" id="editStatus" name="users_status" value="<?php echo htmlspecialchars($user['users_status']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="editPassword">รหัสผ่านใหม่ (ถ้าไม่เปลี่ยนให้เว้นว่าง)</label>
                            <input type="password" class="form-control" id="editPassword" name="password">
                        </div>
                        <div class="form-group">
                            <label for="confirmEditPassword">ยืนยันรหัสผ่านใหม่</label>
                            <input type="password" class="form-control" id="confirmEditPassword">
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                                <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">ปิด</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/feather.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
    <script src="assets/plugins/sweetalert/sweetalerts.min.js"></script>
    <script src="assets/plugins/select2/js/select2.min.js"></script>
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        $(document).ready(function() {
            // Handle edit profile form submission
            $('#editProfileForm').on('submit', function(e) {
                e.preventDefault();

                // ตรวจสอบว่ารหัสผ่านทั้งสองช่องตรงกันหรือไม่ (ถ้ามีการกรอก)
                var password = $('#editPassword').val();
                var confirmPassword = $('#confirmEditPassword').val();
                if (password && password !== confirmPassword) {
                    Swal.fire('Error', 'รหัสผ่านไม่ตรงกัน', 'error');
                    return;
                }

                $.ajax({
                    url: 'actions/user_actions.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        const res = JSON.parse(response);
                        if (res.status === 'success') {
                            Swal.fire({
                                title: 'Success',
                                text: 'บันทึกการเปลี่ยนแปลงเรียบร้อยแล้ว',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'เกิดข้อผิดพลาดในการส่งข้อมูล', 'error');
                    }
                });
            });
            // ฟังก์ชันคำนวณ PxPerMM แบบอัตโนมัติ
            function calculatePxPerMM() {
                const div = document.createElement('div');
                div.style.width = '1mm';
                div.style.position = 'absolute';
                div.style.visibility = 'hidden';
                document.body.appendChild(div);
                const pxPerMM = div.offsetWidth;
                document.body.removeChild(div);
                return pxPerMM;
            }

            function calculateBetterPxPerMM() {
                // ลองใช้ Screen DPI ถ้าเบราว์เซอร์รองรับ
                if (window.screen && window.screen.logicalXDPI) {
                    return window.screen.logicalXDPI / 25.4; // 25.4 mm = 1 inch
                }

                // ใช้ Device Pixel Ratio
                const standardDPI = 96;
                const mmPerInch = 25.4;
                const devicePixelRatio = window.devicePixelRatio || 1;

                return (standardDPI * devicePixelRatio) / mmPerInch;
            }

            // สร้างเส้นไม้บรรทัด
            function createRuler(pxPerMM) {
                // ขนาดของแถบ (10 มม.)
                const rulerWidthMM = 10;
                const rulerWidthPx = rulerWidthMM * pxPerMM;

                // กำหนดขนาดให้แถบสี
                const referenceBlock = document.getElementById('mm10-block');
                referenceBlock.style.width = rulerWidthPx + 'px';
            }

            // โหลดค่า PxPerMM จาก localStorage หรือคำนวณใหม่ถ้าไม่มี
            let currentPxPerMM = localStorage.getItem('pxPerMMCalibration');

            if (!currentPxPerMM) {
                currentPxPerMM = calculateBetterPxPerMM();
            }

            currentPxPerMM = parseFloat(currentPxPerMM);

            // แสดงค่าปัจจุบัน
            $('#pxPerMMValue').val(currentPxPerMM.toFixed(2));
            $('#pxPerMMSlider').val(currentPxPerMM);

            // สร้างไม้บรรทัด
            createRuler(currentPxPerMM);

            // อัพเดทค่าเมื่อปรับ Slider
            $('#pxPerMMSlider').on('input', function() {
                const newValue = parseFloat($(this).val());
                $('#pxPerMMValue').val(newValue.toFixed(2));
                createRuler(newValue);
            });

            // อัพเดทค่าเมื่อป้อนตัวเลข
            $('#pxPerMMValue').on('input', function() {
                let newValue = parseFloat($(this).val());
                // ตรวจสอบช่วงค่า
                if (isNaN(newValue) || newValue < 0.1) newValue = 0.1;
                if (newValue > 10) newValue = 10; // ค่าสูงสุดคือ 10 (เดิมก็เป็น 10 อยู่แล้ว)

                $('#pxPerMMSlider').val(newValue);
                createRuler(newValue);
            });

            // เพิ่มปุ่มคำนวณอัตโนมัติ
            $('#autoCalibrate').click(function() {
                const autoValue = calculateBetterPxPerMM();
                $('#pxPerMMValue').val(autoValue.toFixed(2));
                $('#pxPerMMSlider').val(autoValue);
                createRuler(autoValue);

                Swal.fire({
                    title: 'คำนวณอัตโนมัติ',
                    text: 'ระบบได้คำนวณค่า PxPerMM = ' + autoValue.toFixed(2) + ' โดยอัตโนมัติ กรุณาตรวจสอบความถูกต้องโดยใช้ไม้บรรทัดจริงวัด',
                    icon: 'info',
                    confirmButtonText: 'เข้าใจแล้ว'
                });
            });

            // บันทึกค่าลง localStorage
            $('#saveCalibration').click(function() {
                const valueToSave = parseFloat($('#pxPerMMValue').val());

                if (isNaN(valueToSave) || valueToSave <= 0) {
                    Swal.fire('ข้อผิดพลาด', 'ค่า PxPerMM ต้องเป็นตัวเลขมากกว่า 0', 'error');
                    return;
                }

                localStorage.setItem('pxPerMMCalibration', valueToSave);

                Swal.fire({
                    title: 'บันทึกสำเร็จ',
                    text: 'ค่า PxPerMM ถูกบันทึกเรียบร้อยแล้ว',
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                });
            });

            // รีเซ็ตค่ากลับเป็นค่าเริ่มต้น
            $('#resetCalibration').click(function() {
                const defaultValue = calculatePxPerMM();
                $('#pxPerMMValue').val(defaultValue.toFixed(2));
                $('#pxPerMMSlider').val(defaultValue);
                createRuler(defaultValue);

                Swal.fire({
                    title: 'รีเซ็ตสำเร็จ',
                    text: 'ค่า PxPerMM ถูกรีเซ็ตกลับเป็นค่าเริ่มต้น',
                    icon: 'info',
                    confirmButtonText: 'ตกลง'
                });
            });
        });
    </script>
</body>

</html>