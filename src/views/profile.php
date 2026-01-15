<?php
require_once __DIR__ . '/../middleware/auth.php';
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

            <div class="actions-row">
                <a href="/views/menu.php" class="back-link">← Menú</a>
                <a href="/controllers/logout.php" class="logout-btn">Cerrar Sesión</a>
            </div>
        </div>
    </main>
</body>
</html>
