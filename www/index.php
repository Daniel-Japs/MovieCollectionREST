<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="base.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>

<h2>Login</h2>

<?php if (isset($_GET['error'])): ?>
    <p class="error"><?php echo $_GET['error']; ?></p>
<?php endif; ?>

<form action="login.php" method="post">
    <div>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <input type="submit" value="Login">
    </div>
</form>

</body>
</html>