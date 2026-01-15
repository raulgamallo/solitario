<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogIn</title>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/login.css">
    <style>
        .error-message {
            background-color: rgba(255, 107, 107, 0.2);
            border: 1px solid var(--danger);
            color: #ff6b6b;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            text-align: center;
        }
    </style>
</head>

<body class="auth-body login-page">
    <main class="auth-shell">
        <div class="card-flip <?= isset($_SESSION['login_error']) ? 'flipped' : '' ?>" id="flipCard">
            <div class="card-face card-back">
                <div class="card-back-glow"></div>
            </div>
            <div class="card-face card-front">
                <section class="auth-card">
                    <div class="card-heading">
                        <p class="eyebrow">Bienvenido de nuevo</p>
                        <h2>Inicia sesión</h2>
                        <p class="muted">Retoma tu partida o prepara una nueva.</p>
                    </div>
                    <?php if (isset($_SESSION['login_error'])): ?>
                        <div class="error-message">
                            <?= htmlspecialchars($_SESSION['login_error']) ?>
                        </div>
                        <?php unset($_SESSION['login_error']); ?>
                    <?php endif; ?>
                    <form class="auth-form" action="/controllers/login.php" method="POST">
                        <label class="field" for="email">
                            <span>Email o Usuario</span>
                            <input type="text" name="email" id="email" required placeholder="tu@correo.com o tu usuario" value="<?= htmlspecialchars($_SESSION['last_email_attempt'] ?? '') ?>">
                        </label>

                        <label class="field" for="password">
                            <span>Contraseña</span>
                            <input type="password" name="password" id="password" required placeholder="••••••••">
                        </label>
                         <?php unset($_SESSION['last_email_attempt']); ?>

                        <div class="form-actions">
                            <button type="submit" class="primary">Entrar</button>
                            <button type="button" class="ghost" id="registerButton">Crear cuenta</button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </main>
    <script>
        const flipCard = document.getElementById('flipCard');
        if (flipCard && !flipCard.classList.contains('flipped')) {
            const handleFlip = () => {
                flipCard.classList.add('flipped');
                flipCard.removeEventListener('click', handleFlip);
            };
            flipCard.addEventListener('click', handleFlip);
        }
        document.getElementById("registerButton").addEventListener("click", function() {
            window.location.href = "register.php";
        });
    </script>
</body>
</html>