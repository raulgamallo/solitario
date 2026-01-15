<?php
session_start();
setcookie('auth_token', '', time() - 3600, '/');
session_destroy();
header("Location: /views/login.php");
exit();
