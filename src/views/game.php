<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game</title>
</head>

<body>
    <?php include __DIR__ . '/../components/header.php'; ?>
    <div id="game-board">
        <div class="top-section">
            <div id="stock" class="pile"></div>
            <div id="waste" class="pile"></div>
            <div class="spacer"></div>
            <div id="timer-display" class="timer">00:00</div>
            <div class="spacer"></div>
            <div id="foundation-hearts" class="pile foundation" data-suit="corazones"></div>
            <div id="foundation-diamonds" class="pile foundation" data-suit="diamantes"></div>
            <div id="foundation-clubs" class="pile foundation" data-suit="trevoles"></div>
            <div id="foundation-spades" class="pile foundation" data-suit="picas"></div>
        </div>
        <div class="tableau-section">
            <div id="tabla-0" class="column"></div>
            <div id="tabla-1" class="column"></div>
            <div id="tabla-2" class="column"></div>
            <div id="tabla-3" class="column"></div>
            <div id="tabla-4" class="column"></div>
            <div id="tabla-5" class="column"></div>
            <div id="tabla-6" class="column"></div>
        </div>
    </div>
    <script src="/js/game.js"></script>
</body>

</html>