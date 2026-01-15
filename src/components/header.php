<header class="app-header">
    <div class="header-left">
        <button id="homeButton" class="icon-button ghost" aria-label="Ir al inicio">
            <span class="icon-dot"></span>
            <span class="sr-only">Inicio</span>
        </button>
        <h1 class="brand">Solitar.io</h1>
    </div>
    <div class="header-actions">
        <button id="profileButton" class="icon-button ghost" aria-label="Ir al perfil" style="background-image: url('<?= htmlspecialchars($currentUser->pfp ?? '') ?>'); background-size: cover; background-position: center;">
            <?php if (empty($currentUser->pfp)): ?>
                <span class="avatar-circle" aria-hidden="true"></span>
            <?php endif; ?>
            <span class="sr-only">Perfil</span>
        </button>
    </div>
    <script>
        document.getElementById('homeButton').onclick = function() {
            window.location.href = '/views/menu.php';
        };
        document.getElementById('profileButton').onclick = function() {
            window.location.href = '/views/profile.php';
        };
    </script>
</header>