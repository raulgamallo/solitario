<?php
require_once __DIR__ . '/../middleware/auth.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/menu.css">
</head>

<body class="menu-body">
    <?php include __DIR__ . '/../components/header.php'; ?>
    <main class="menu-shell">
        <section class="menu-hero">
            <div class="hero-copy">
                <p class="eyebrow">Mesa preparada</p>
                <h1>Bienvenido a Solitar.io</h1>
                <p class="muted">Elige tu siguiente movimiento y mantén el ritmo de la partida.</p>
                <div class="hero-actions">
                    <button id="playButton" class="primary">Jugar ahora</button>
                    <button id="rankingButton" class="ghost">Ranking</button>
                </div>
            </div>
            <div class="hero-visual">
                <div class="chip chip-main">★</div>
                <div class="chip chip-secondary"></div>
                <div class="chip chip-tertiary"></div>
            </div>
        </section>

        <section class="menu-grid">
            <article class="menu-card">
                <h3>Partida rápida</h3>
                <p>Empieza en segundos y sigue donde lo dejaste.</p>
                <button id="playButtonSecondary" class="primary">Ir al juego</button>
            </article>
            <article class="menu-card">
                <h3>Ranking</h3>
                <p>Consulta tu posición y mejora tu marca personal.</p>
                <button id="rankingButtonSecondary" class="ghost">Ver ranking</button>
            </article>
        </section>
    </main>
    <script>
        document.getElementById("playButton").addEventListener("click", function() {
            window.location.href = "game.php";
        });
        document.getElementById("playButtonSecondary").addEventListener("click", function() {
            window.location.href = "game.php";
        });
        document.getElementById("rankingButton").addEventListener("click", function() {
            window.location.href = "ranking.php";
        });
        document.getElementById("rankingButtonSecondary").addEventListener("click", function() {
            window.location.href = "ranking.php";
        });
    </script>
</body>
</html>