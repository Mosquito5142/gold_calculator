<?php
// Check if the user is an Admin
if ($_SESSION['recipenecklace_users_level'] !== 'Admin') {
    // Redirect to a different page or show an error message
    header('Location: index.php');
    exit();
}
