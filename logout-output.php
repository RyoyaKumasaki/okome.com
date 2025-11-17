<?php session_start(); ?>
<?php
if (isset($_SESSION['customer'])) {
    unset($_SESSION['customer']);
}
header("Location: top.php");
exit;
?>
<?php require 'header.php'; ?>
<?php require 'menu.php'; ?>
<?php require 'footer.php'; ?>