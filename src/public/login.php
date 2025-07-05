<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';

$db = new Database();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $db->query("SELECT * FROM users WHERE email = :email");
    $db->bind(':email', $email);
    $user = $db->single();

    if ($user && password_verify($password, $user->password)) {
        // Set session
        $_SESSION['user_id'] = $user->id;
        $_SESSION['name']    = $user->name;
        $_SESSION['role']    = $user->role;

        // ✅ Redirect ke index.php
        header("Location: index.php");
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Mini Classroom</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
<div class="auth-container">
    <h2>Login</h2>

    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['registered'])): ?>
        <div class="alert success">Pendaftaran berhasil! Silakan login.</div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <input type="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        <input type="password" name="password" placeholder="Kata Sandi" required>
        <button type="submit">Masuk</button>
    </form>

    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
</div>
</body>
</html>
