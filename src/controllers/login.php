<?php
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $errors = [];
    try {
        $user = UserLoginDTO::fromRequest($_POST);

        if (empty($errors)) {
            header("Location: /views/menu.php");
        } else {
            header("Location: /views/login.php");
        }
    } catch (\Throwable $th) {
        //throw $th;
    }
}
