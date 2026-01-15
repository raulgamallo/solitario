<?php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../classes/Ranking.php';

// $postgres is instantiated in Postgres.php which is required by Ranking.php
global $postgres;

$ranking = new Ranking($postgres);

$sortBy = $_GET['sort'] ?? 'time';
$order = $_GET['order'] ?? 'ASC';

$games = $ranking->getRankings($sortBy, $order);

function getSortLink($column, $currentSort, $currentOrder) {
    $newOrder = 'ASC';
    if ($currentSort === $column) {
        $newOrder = ($currentOrder === 'ASC') ? 'DESC' : 'ASC';
    }
    return "?sort=" . urlencode($column) . "&order=" . urlencode($newOrder);
}

function getSortIndicator($column, $currentSort, $currentOrder) {
    if ($currentSort === $column) {
        return ($currentOrder === 'ASC') ? ' ▲' : ' ▼';
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking - Solitar.io</title>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/menu.css"> 
    <link rel="stylesheet" href="/css/ranking.css">
</head>
<body class="menu-body">
    <?php include __DIR__ . '/../components/header.php'; ?>
    
    <main>
        <div class="ranking-container">
            <div class="ranking-header">
                <h1>Ranking de Jugadores</h1>
                <a href="/views/menu.php" class="back-btn">← Volver al menú</a>
            </div>

            <table class="ranking-table">
                <thead>
                    <tr>
                        <th>Posición</th>
                        <th>Usuario</th>
                        <th>
                            <a href="<?= getSortLink('time', $sortBy, $order) ?>">
                                Tiempo <?= getSortIndicator('time', $sortBy, $order) ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?= getSortLink('movements', $sortBy, $order) ?>">
                                Movimientos <?= getSortIndicator('movements', $sortBy, $order) ?>
                            </a>
                        </th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($games)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem;">
                                Aún no hay partidas registradas. ¡Sé el primero en jugar!
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($games as $index => $game): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($game['username'] ?? 'Anónimo') ?></td>
                                <td><?= htmlspecialchars($game['duration_formatted'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($game['movements']) ?></td>
                                <td>
                                    <?= date('d/m/Y', strtotime($game['finished'])) ?> <br>
                                    <small><?= date('H:i:s', strtotime($game['finished'])) ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
