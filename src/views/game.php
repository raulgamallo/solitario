<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/game.css">
    <title>Solitario</title>
</head>

<body class="game-body">
    <?php include __DIR__ . '/../components/header.php'; ?>

    <main id="game-board" aria-label="Tablero de solitario">
        <section class="hud" aria-label="Indicadores de partida">
            <div class="hud-item">
                <span class="label">Tiempo</span>
                <span id="timer" class="value">00:00</span>
            </div>
            <div class="hud-item">
                <span class="label">Movimientos</span>
                <span id="moves" class="value">0</span>
            </div>
            <div class="hud-actions">
                <button id="restartBtn" class="ghost">Reiniciar</button>
            </div>
        </section>

        <section class="top-section" aria-label="Zona superior">
            <div class="stock-wrapper">
                <div id="stock" class="pile stock" data-dropzone="stock" aria-label="Mazo"></div>
                <div id="waste" class="pile waste" data-dropzone="waste" aria-label="Descarte"></div>
            </div>
            <div class="foundation-wrapper" aria-label="Bases">
                <div class="pile foundation" data-dropzone="foundation" data-foundation="0"></div>
                <div class="pile foundation" data-dropzone="foundation" data-foundation="1"></div>
                <div class="pile foundation" data-dropzone="foundation" data-foundation="2"></div>
                <div class="pile foundation" data-dropzone="foundation" data-foundation="3"></div>
            </div>
        </section>

        <section class="tableau-section" aria-label="Columnas">
            <div class="column" data-dropzone="tableau" data-column="0"></div>
            <div class="column" data-dropzone="tableau" data-column="1"></div>
            <div class="column" data-dropzone="tableau" data-column="2"></div>
            <div class="column" data-dropzone="tableau" data-column="3"></div>
            <div class="column" data-dropzone="tableau" data-column="4"></div>
            <div class="column" data-dropzone="tableau" data-column="5"></div>
            <div class="column" data-dropzone="tableau" data-column="6"></div>
        </section>
    </main>

    <section id="endModal" class="modal hidden" aria-live="polite" aria-label="Resultado de la partida">
        <div class="modal-content">
            <h2 id="endTitle"></h2>
            <p id="endSummary"></p>
            <div class="modal-actions">
                <button id="menuBtn">Volver al menú</button>
                <button id="playAgainBtn" class="ghost">Jugar de nuevo</button>
            </div>
        </div>
    </section>

    <div id="drag-layer" aria-hidden="true"></div>

    <script src="../js/game.js"></script>
</body>

</html>