<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <!-- เมนูหลัก -->
                <li>
                    <a href="index.php">
                        <i class="fas fa-home"></i> <!-- เปลี่ยนเป็นไอคอนหน้าหลัก -->
                        <span>สูตรฮั้วสร้อย</span>
                    </a>
                </li>
                <li>
                    <a href="necklace_calculator_index.php">
                        <i class="fas fa-percentage"></i> <!-- เปลี่ยนเป็นไอคอนเปอร์เซ็นต์ -->
                        <span>สัดส่วน%สร้อย</span>
                    </a>
                </li>
                <li>
                    <a href="incaseofsize.php">
                        <i class="fas fa-ruler"></i> <!-- เปลี่ยนเป็นไอคอนไม้บรรทัด -->
                        <span>คำนวณเผื่อไซต์สร้อย</span>
                    </a>
                </li>
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="far fa-copy"></i> <!-- เปลี่ยนเป็นไอคอนสำเนา -->
                        <span>สำเนา</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="margin-left: 25px;">
                        <li>
                            <a href="percent_necklace_copy.php">
                                <i class="far fa-copy"></i> <!-- เปลี่ยนเป็นไอคอนสำเนา -->
                                <span> % สร้อยของฉัน</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="fas fa-database"></i> <!-- เปลี่ยนเป็นไอคอนจัดการข้อมูล -->
                        <span>จัดการข้อมูล</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="margin-left: 25px;">
                        <li>
                            <a href="necklace_detail_management.php">
                                <i class="fas fa-clipboard-list"></i> <!-- เปลี่ยนเป็นไอคอนข้อมูลงาน -->
                                <span> ข้อมูลสร้อย</span>
                            </a>
                        </li>
                        <li>
                            <a href="ratio_data_management.php">
                                <i class="fas fa-clipboard-list"></i>
                                <span> อัตราส่วน</span>
                            </a>
                        </li>
                        <li>
                            <a href="percent_necklace_management.php">
                                <i class="fas fa-percentage"></i> <!-- เปลี่ยนเป็นไอคอนเปอร์เซ็นต์ -->
                                <span> สัดส่วนสร้อย</span>
                            </a>
                        </li>
                        <?php if ($_SESSION['recipenecklace_users_level'] == 'Admin'): ?>
                            <li>
                                <a href="user_management.php">
                                    <i class="fas fa-users"></i> <!-- เปลี่ยนเป็นไอคอนข้อมูลผู้ใช้ -->
                                    <span> ข้อมูลผู้ใช้</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>