<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $db->query("SELECT * FROM users WHERE email = :email");
    $db->bind(':email', $email);
    $user = $db->single();

    if ($user && password_verify($password, $user->password)) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['name'] = $user->name;
        $_SESSION['role'] = $user->role;
        header("Location: index.php");
        exit;
    } else {
        header("Location: login.php?error=Email atau password salah");
        exit;
    }
} else {
    header("Location: login.php?error=Permintaan tidak valid");
    exit;
}
