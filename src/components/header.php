<header class="app-header">
    <div class="header-left">
        <button id="homeButton" class="icon-button ghost" aria-label="Ir al inicio">
            <span class="icon-dot"></span>
            <span class="sr-only">Inicio</span>
        </button>
        <h1 class="brand">Solitar.io</h1>
    </div>
    <div class="header-actions">
        <button id="profileButton" class="icon-button ghost" aria-label="Ir al perfil">
            <span class="avatar-circle" aria-hidden="true"></span>
            <span class="sr-only">Perfil</span>
        </button>
    </div>
    <script>
        document.getElementById('homeButton').onclick = function() {
            window.location.href = '/';
        };
        document.getElementById('profileButton').onclick = function() {
            window.location.href = '/profile';
        };
    </script>
</header>