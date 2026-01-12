<?php
require_once 'classes/Postgres.php';

session_start();

$error = "";

function clean_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = clean_input($_POST['username'] ?? '');
    $password = clean_input($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Por favor ingresa usuario y contraseña.";
    } else {
        try {
            // Buscamos el usuario por nombre de usuario
            $sql = "SELECT id, username, password_hash FROM users WHERE username = $1";
            $params = [$username];
            
            $result = $postgres->query($sql, $params);

            if ($result && count($result) > 0) {
                $user = $result[0];
                // Verificamos el hash de la contraseña
                if (password_verify($password, $user['password_hash'])) {
                    // Login exitoso
                    $_SESSION['loggedin'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    
                    header("Location: menu.php");
                    exit();
                } else {
                    $error = "Contraseña incorrecta.";
                }
            } else {
                $error = "El usuario no existe.";
            }

        } catch (Exception $e) {
            error_log($e->getMessage());
            $error = "Error de conexión. Inténtalo más tarde.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Solitario</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Iniciar Sesión</h2>
        
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if(isset($_GET['registered'])): ?>
            <div style="color: green; text-align: center; margin-bottom: 1rem;">
                ¡Registro exitoso! Por favor inicia sesión.
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Ingresar</button>
        </form>

        <div class="link">
            ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
        </div>
    </div>
</body>
</html>