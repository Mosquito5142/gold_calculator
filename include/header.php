<?php
?>
<div class="header">
    <div class="header-left ">
        <a href="index.php" class="logo"><img src="assets/img/logo.png" alt="" /></a>
        <a href="index.php" class="logo-small"><img src="assets/img/favicon.ico" alt="" /></a>
        <a id="toggle_btn" href="javascript:void(0);"> </a>
    </div>

    <!-- Toggle Sidebar -->
    <a id="mobile_btn" class="mobile_btn" href="#sidebar">
        <span class="bar-icon">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </a>

    <!-- Menu Profile -->
    <ul class="nav user-menu">
        <li class="nav-item">
            <div class="top-nav-search">
                <a href="javascript:void(0);" class="responsive-search"><i class="fa fa-search"></i></a>
                <form action="search.php" method="post">
                    <div class="searchinputs">
                        <input type="text" name="word" placeholder="Search Here ..." />
                        <div class="search-addon">
                            <span><img src="assets/img/icons/closes.svg" alt="img" /></span>
                        </div>
                    </div>

                    <button class="btn" id="searchdiv" type="submit"><img src="assets/img/icons/search.svg" alt="img"></button>
                </form>
            </div>
        </li>

        <?php if (isset($_SESSION['recipenecklace_users_id'])): ?>
            <li class="nav-item dropdown has-arrow main-drop">
                <a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
                    <span class="user-img">
                        <i class="me-2" data-feather="user"></i>
                    </span>
                </a>

                <div class="dropdown-menu menu-drop-user">
                    <div class="profilename">
                        <div class="profileset">
                            <span class="user-img"><i class="me-2" data-feather="user"></i>
                            </span>

                            <div class="profilesets">
                                <h6><?php echo $_SESSION['recipenecklace_username']; ?></h6>
                                <h5><?php echo $_SESSION['recipenecklace_users_depart']; ?></h5>
                            </div>

                        </div>

                        <hr class="m-0" />
                        <a class="dropdown-item" href="profile.php"><i class="me-2" data-feather="user"></i> My Profile</a>
                        <hr class="m-0" />
                        <a class="dropdown-item logout pb-0" href="functions/logout.php"><img src="assets/img/icons/log-out.svg" class="me-2" alt="img" />Logout</a>
                    </div>
                </div>
            </li>
        <?php else: ?>
            <li class="nav-item">
                <a href="login.php" class="btn btn-sm pd-2">
                    <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <!-- Mobile Responsive User Menu -->
    <div class="dropdown mobile-user-menu">
        <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-ellipsis-v"></i>
        </a>

        <div class="dropdown-menu dropdown-menu-right">
            <?php if (isset($_SESSION['recipenecklace_users_id'])): ?>
                <a class="dropdown-item" href="profile.php">My Profile</a>
                <a class="dropdown-item" href="functions/logout.php">Logout</a>
            <?php else: ?>
                <a class="dropdown-item" href="login.php">เข้าสู่ระบบ</a>
            <?php endif; ?>
        </div>
    </div>
</div>