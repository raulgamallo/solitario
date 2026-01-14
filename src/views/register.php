<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>

<body>
    <?php include __DIR__ . '/../components/header.php'; ?>
    <form action="/controllers/register.php" method="POST" enctype="multipart/form-data">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        <label for="pfp">Profile Picture:</label>
        <input type="file" name="pfp" id="pfp">
        <button type="submit">Submit</button>
    </form>
</body>

</html>