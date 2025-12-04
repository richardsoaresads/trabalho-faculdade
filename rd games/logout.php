<?php
session_start();
unset($_SESSION['admin_id'], $_SESSION['admin_usuario']);
header("Location: login.php");
exit;
