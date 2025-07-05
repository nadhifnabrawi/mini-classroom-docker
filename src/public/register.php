<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';

$db = new Database();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role     = $_POST['role'];

    // Validasi dasar
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = "Semua kolom wajib diisi!";
    } else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $db->query("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
            $db->bind(':name', $name);
            $db->bind(':email', $email);
            $db->bind(':password', $hashedPassword);
            $db->bind(':role', $role);

            $db->execute();
            // Berhasil daftar, redirect ke login
            header("Location: login.php?registered=1");
            exit;

        } catch (PDOException $e) {
            // Cek duplikasi email
            if ($e->getCode() === '23505') {
                $error = "Email sudah terdaftar!";
            } else {
                $error = "Terjadi kesalahan saat registrasi.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Mini Classroom</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
<div class="auth-container">
    <h2>Register</h2>

    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <input type="text" name="name" placeholder="Nama Lengkap" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        <input type="password" name="password" placeholder="Kata Sandi" required>

        <select name="role" required>
            <option value="">Pilih Peran</option>
            <option value="guru" <?= (($_POST['role'] ?? '') === 'guru') ? 'selected' : '' ?>>Guru</option>
            <option value="siswa" <?= (($_POST['role'] ?? '') === 'siswa') ? 'selected' : '' ?>>Siswa</option>
        </select>

        <button type="submit">Daftar</button>
    </form>

    <p>Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
</div>
</body>
</html>
