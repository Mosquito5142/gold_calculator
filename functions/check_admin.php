<?php
// Check if the user is an Admin or Head Technician
if ($_SESSION['recipenecklace_users_level'] !== 'Admin' && $_SESSION['recipenecklace_users_depart'] !== 'หัวหน้าช่าง') {
    // Redirect if user is neither Admin nor Head Technician
    header('Location: index.php');
    exit();
}