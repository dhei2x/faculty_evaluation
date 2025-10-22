<?php
function require_role($roles) {
    if (!isset($_SESSION['role'])) {
        header("Location: login.php");
        exit;
    }

    // allow single or multiple roles
    if (is_array($roles)) {
        if (!in_array($_SESSION['role'], $roles)) {
            header("Location: login.php");
            exit;
        }
    } else {
        if ($_SESSION['role'] !== $roles) {
            header("Location: login.php");
            exit;
        }
    }
}
?>
