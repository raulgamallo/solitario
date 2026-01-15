<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/register.css">
</head>

<body class="auth-body register-page">
    <main class="auth-shell">
        <section class="auth-card">
            <div class="card-heading">
                <p class="eyebrow">Crear cuenta</p>
                <h2>Únete a Solitar.io</h2>
                <p class="muted">Perfila tu mesa de juego y entra con estilo.</p>
            </div>
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
            window.location.href = "/login.php";
        });
    </script>
</body>

</html>