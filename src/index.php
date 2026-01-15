<?php
require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/classes/Postgres.php";

if (isset($_COOKIE['auth_token'])) {
    header("Location: views/menu.php");
} else {
    header("Location: views/login.php");
}
exit;
