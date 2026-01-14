<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogIn</title>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/login.css">
</head>

<body class="auth-body login-page">
    <main class="auth-shell">
        <section class="auth-card">
            <div class="card-heading">
                <p class="eyebrow">Bienvenido de nuevo</p>
                <h2>Inicia sesión</h2>
                <p class="muted">Retoma tu partida o prepara una nueva.</p>
            </div>
            <form class="auth-form" action="/controllers/login.php" method="POST">
                <label class="field" for="email">
                    <span>Email</span>
                    <input type="email" name="email" id="email" required placeholder="usuario@correo.com">
                </label>

                <label class="field" for="password">
                    <span>Contraseña</span>
                    <input type="password" name="password" id="password" required placeholder="••••••••">
                </label>

                <div class="form-actions">
                    <button type="submit" class="primary">Entrar</button>
                    <button type="button" class="ghost" id="registerButton">Crear cuenta</button>
                </div>
            </form>
        </section>
    </main>
    <script>
        document.getElementById("registerButton").addEventListener("click", function() {
            window.location.href = "register.php";
        });
    </script>
</body>

</html>