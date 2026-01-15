<?php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../classes/Games.php';

// Obtener estadísticas del usuario
$gameStats = new GameStats($postgres);
$lastGames = $gameStats->getLastGames($currentUser->uuid, 10);
$gamesPerDay = $gameStats->getGamesPerDay($currentUser->uuid, 7);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Solitar.io</title>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/menu.css"> 
    <link rel="stylesheet" href="/css/profile.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>


<body class="menu-body">
    <?php include __DIR__ . '/../components/header.php'; ?>
    
    <main>
        <div class="profile-container">
            <?php if (isset($_SESSION['profile_message'])): ?>
                <div class="message <?= $_SESSION['profile_success'] ? 'success' : 'error' ?>">
                    <?= htmlspecialchars($_SESSION['profile_message']) ?>
                </div>
                <?php unset($_SESSION['profile_message'], $_SESSION['profile_success']); ?>
            <?php endif; ?>

            <div class="profile-avatar-wrapper">
                <?php if (!empty($currentUser->pfp)): ?>
                    <img src="<?= htmlspecialchars($currentUser->pfp) ?>" alt="Foto de perfil" class="profile-avatar">
                <?php else: ?>
                    <div class="profile-avatar-placeholder">
                        <?= strtoupper(substr($currentUser->username, 0, 1)) ?>
                    </div>
                <?php endif; ?>
                
                <form action="/controllers/profile.php" method="POST" enctype="multipart/form-data" id="profileForm" style="display:inline;">
                    <button class="upload-btn-wrapper btn-upload" style="position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); white-space: nowrap; border:none; background: var(--accent-gold); color: #1a1a1a; padding: 6px 16px; font-size: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                        Cambiar
                        <input type="file" name="pfp" class="upload-input" accept="image/*" onchange="document.getElementById('profileForm').submit()">
                    </button>
                </form>
            </div>

            <div class="user-info">
                <h2><?= htmlspecialchars($currentUser->username) ?></h2>
                <p><?= htmlspecialchars($currentUser->email) ?></p>
            </div>

            <!-- Sección de Gráficos -->
            <div class="stats-grid">
                <div class="chart-card">
                    <h3>Progresión de Movimientos</h3>
                    <canvas id="movementsChart"></canvas>
                </div>
                <div class="chart-card">
                    <h3>Partidas por Día (Última semana)</h3>
                    <canvas id="activityChart"></canvas>
                </div>
            </div>

            <div class="actions-row">
                <a href="/views/menu.php" class="back-link">← Menú</a>
                <a href="/controllers/logout.php" class="logout-btn">Cerrar Sesión</a>
            </div>
        </div>
    </main>
    
    <script>
        // Datos desde PHP
        const lastGames = <?= json_encode($lastGames) ?>;
        const gamesPerDay = <?= json_encode($gamesPerDay) ?>;

        // Configuración común
        Chart.defaults.color = 'rgba(255, 255, 255, 0.7)';
        Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';
        Chart.defaults.font.family = "'Manrope', sans-serif";

        // Gráfico 1: Movimientos
        const ctxMov = document.getElementById('movementsChart').getContext('2d');
        
        // Preparar etiquetas y datos si hay partidas
        const movLabels = lastGames.map((g, i) => `P${i+1}`);
        const movData = lastGames.map(g => g.movements);

        new Chart(ctxMov, {
            type: 'line',
            data: {
                labels: movLabels,
                datasets: [{
                    label: 'Movimientos',
                    data: movData,
                    borderColor: '#f5d364', // accent-gold
                    backgroundColor: 'rgba(245, 211, 100, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    pointBackgroundColor: '#f5d364',
                    pointRadius: 4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeOutQuart'
                }
            }
        });

        // Gráfico 2: Actividad
        const ctxAct = document.getElementById('activityChart').getContext('2d');
        
        const actLabels = gamesPerDay.map(d => {
            const date = new Date(d.game_date);
            return date.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric' });
        });
        const actData = gamesPerDay.map(d => d.count);

        new Chart(ctxAct, {
            type: 'bar',
            data: {
                labels: actLabels,
                datasets: [{
                    label: 'Partidas',
                    data: actData,
                    backgroundColor: 'rgba(126, 216, 137, 0.8)', // success color variant
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        },
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeOutBounce',
                    delay: 500 // Retardo para que empiece después del otro
                }
            }
        });
    </script>
</body>

</html>
