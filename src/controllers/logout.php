<?php
session_start();
// Borramos cookie
setcookie('auth_token', '', time() - 3600, '/');
// Destruimos sesión PHP
session_destroy();
// Redirigimos al login
header("Location: /views/login.php");
exit();
