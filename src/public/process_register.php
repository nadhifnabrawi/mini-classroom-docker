<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once '../classes/Database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"] ?? '';
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';
    $role = $_POST["role"] ?? '';

    if ($name && $email && $password && $role) {
        $db = new Database();

        // Cek apakah email sudah digunakan
        $db->query("SELECT * FROM users WHERE email = :email");
        $db->bind(':email', $email);
        $user = $db->single();

        if ($user) {
            $_SESSION['error'] = "Email sudah terdaftar.";
            header("Location: register.php");
            exit;
        }

        // Hash password dan simpan ke database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $db->query("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
        $db->bind(':name', $name);
        $db->bind(':email', $email);
        $db->bind(':password', $hashed_password);
        $db->bind(':role', $role);

        if ($db->execute()) {
            $_SESSION['success'] = "Registrasi berhasil. Silakan login.";
            header("Location: login.php");
            exit;
        } else {
            $_SESSION['error'] = "Terjadi kesalahan saat registrasi.";
        }
    } else {
        $_SESSION['error'] = "Semua field wajib diisi.";
    }

    header("Location: register.php");
    exit;
}
