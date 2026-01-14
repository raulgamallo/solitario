<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogIn</title>
</head>

<body>
    <?php include __DIR__ . '/../components/header.php'; ?>
    <form action="/controllers/login.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        <button type="submit">Login</button>
        <button id="registerButton">Register</button>
    </form>
    <script>
        document.getElementById("registerButton").addEventListener("click", function(event) {
            event.preventDefault();
            window.location.href = "register.php";
        });
    </script>
</body>

</html>