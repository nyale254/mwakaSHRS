<?php
session_start();

session_unset();
session_destroy();

header("Location:/Mwaka.SHRS.2/index.php");
exit();
?>
