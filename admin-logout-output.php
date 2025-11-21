<?php session_start(); ?>
<?php
if (isset($_SESSION['admin'])) {
    unset($_SESSION['admin']);
}
header("Location: controlltop.php");
exit;
?>
<?php require 'controllheader.php'; ?>
<?php require 'admin-menu.php'; ?>
<?php require 'footer.php'; ?>