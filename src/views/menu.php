<?php
// require_once __DIR__ . '/../middleware/auth.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
</head>

<body>
    <?php include __DIR__ . '/../components/header.php'; ?>
    <div>
        <h1>Welcome to Solitar.io</h1>
        <div>
            <div>
                <img src="" alt="play">
                <button id="playButton">Play</button>
            </div>
            <div>
                <img src="" alt="ranking">
                <button>Ranking</button>
            </div>
        </div>
    </div>
    <script>
        document.getElementById("playButton").addEventListener("click", function() {
            window.location.href = "game.php";
        });
    </script>
</body>

</html>