<?php
session_start();
session_destroy();
header("Location: /hms/login.php");
exit();
?>
