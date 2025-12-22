<?php
require __DIR__ . "/classes/Postgres.php";
var_dump($postgres->query("SELECT version();"));
$postgres->disconnect();
// phpinfo();
