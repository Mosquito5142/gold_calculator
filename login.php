<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
    <meta name="description" content="POS - Bootstrap Admin Template" />
    <meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, html5, responsive" />
    <meta name="author" content="Dreamguys - Bootstrap Admin Template" />
    <meta name="robots" content="noindex, nofollow" />
    <title>เข้าสู่ระบบ</title>

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />
    <style>
        .card {
            border-radius: 10px;
        }

        .card-title {
            font-size: 1.5rem;
        }
    </style>
</head>

<body class="bg-light">
    <section class="h-100">
        <div class="container h-100">
            <div class="row justify-content-sm-center h-100">
                <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-7 col-sm-9">
                    <div class="text-center my-5 pt-3">
                    </div>
                    <div class="card shadow-lg">
                        <div class="card-body p-5">
                            <h1 class="fs-4 card-title fw-bold mb-4">เข้าสู่ระบบ</h1>
                            <form id="loginForm">
                                <div class="mb-3">
                                    <label class="mb-2 text-muted" for="username">ชื่อผู้ใช้</label>
                                    <input id="username" type="text" class="form-control" name="username" required autocomplete="off" autofocus>
                                    <div class="invalid-feedback">
                                        กรุณากรอกชื่อผู้ใช้
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="mb-2 w-100">
                                        <label class="text-muted" for="password">รหัสผ่าน</label>
                                    </div>
                                    <input id="password" type="password" class="form-control" name="password" autocomplete="off" required>
                                    <div class="invalid-feedback">
                                        กรุณากรอกรหัสผ่าน
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">เข้าสู่ระบบ</button>
                                    <a class="btn btn-secondary" href="index.php">ยกเลิก</a>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer py-3 border-0">
                            <div class="text-center">
                                ไม่มีบัญชีเข้าสู่ระบบ ? <a href="" class="text-dark">ติดต่อ IT</a>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-5 text-muted">
                        Copyright © 2025 — Shining Gold
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/plugins/sweetalert/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'functions/login.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Success',
                                text: 'เข้าสู่ระบบสำเร็จ',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'index.php';
                                }
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'เกิดข้อผิดพลาดในการส่งข้อมูล', 'error');
                    }
                });
            });
        });
    </script>
</body>

</html>