<?php
session_start();
if (!isset($_SESSION['recipenecklace_users_id'])) {
    header("Location: login.php");
    exit();
}
