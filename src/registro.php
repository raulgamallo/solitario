<?php
require_once 'classes/Postgres.php';

$message = "";
$error = "";

function clean_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = clean_input($_POST['username'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $password = clean_input($_POST['password'] ?? '');
    
    // Validaciones básicas
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        try {
            // Hash password para seguridad
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Llamada a la función SQL registro_usuario(email, username, password_hash)
            // Usamos parámetros ($1, $2...) para prevenir inyección SQL
            $sql = "SELECT registro_usuario($1, $2, $3) as id";
            $params = [$email, $username, $passwordHash];

            $result = $postgres->query($sql, $params);
            
            if ($result && isset($result[0]['id'])) {
                 // Registro exitoso
                 // Opcional: Iniciar sesión automáticamente o redirigir al login
                 header("Location: login.php?registered=1");
                 exit();
            } else {
                $error = "No se pudo completar el registro.";
            }

        } catch (Exception $e) {
            $msg = $e->getMessage();
            error_log($msg);

            // Intentar detectar si esta duplicado 
            if (strpos($msg, 'users_email_key') !== false) {
                 $error = "El correo electrónico ya está registrado.";
            } elseif (strpos($msg, 'users_username_key') !== false) {
                 $error = "El nombre de usuario ya está en uso.";
            } else {
                 $error = "Ocurrió un error en el registro.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Solitario</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Crear Cuenta</h2>
        
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="registro.php" method="post">
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" required value="<?php echo isset($username) ? $username : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?php echo isset($email) ? $email : ''; ?>">
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Registrarse</button>
        </form>
        
        <div class="link">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
        </div>
    </div>
</body>
</html>
