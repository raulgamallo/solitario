<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/register.css">
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
        .error-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
    </style>
</head>

<body class="auth-body register-page">
    <main class="auth-shell">
        <section class="auth-card">
            <div class="card-heading">
                <p class="eyebrow">Crear cuenta</p>
                <h2>Únete a Solitar.io</h2>
                <p class="muted">Perfila tu mesa de juego y entra con estilo.</p>
            </div>
            <?php if (isset($_SESSION['register_errors']) && !empty($_SESSION['register_errors'])): ?>
                <div class="error-message">
                    <ul class="error-list">
                        <?php foreach ($_SESSION['register_errors'] as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['register_errors']); ?>
            <?php endif; ?>
            <form class="auth-form" action="/controllers/register.php" method="POST" enctype="multipart/form-data">
                <label class="field" for="email">
                    <span>Email</span>
                    <input type="email" name="email" id="email" required placeholder="usuario@correo.com">
                </label>

                <label class="field" for="username">
                    <span>Nombre de usuario</span>
                    <input type="text" name="username" id="username" required placeholder="Tu alias en la mesa">
                </label>

                <label class="field" for="password">
                    <span>Contraseña</span>
                    <input type="password" name="password" id="password" required placeholder="••••••••">
                </label>

                <label class="field" for="pfp">
                    <span>Foto de perfil</span>
                    <input type="file" name="pfp" id="pfp" accept="image/*">
                </label>

                <div class="form-actions">
                    <button type="submit" class="primary">Crear cuenta</button>
                    <button type="button" class="ghost" id="backLogin">Ya tengo cuenta</button>
                </div>
            </form>
        </section>
    </main>
    <script>
        document.getElementById("backLogin").addEventListener("click", function() {
            window.location.href = "/views/login.php";
        });
    </script>
</body>
</html>