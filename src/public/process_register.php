<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $db->query("SELECT * FROM users WHERE email = :email");
    $db->bind(':email', $email);
    $user = $db->single();

    if ($user) {
        header("Location: register.php?error=Email sudah digunakan");
        exit;
    }

    $db->query("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
    $db->bind(':name', $name);
    $db->bind(':email', $email);
    $db->bind(':password', $password);
    $db->bind(':role', $role);
    $db->execute();

    header("Location: login.php");
    exit;
} else {
    header("Location: register.php?error=Permintaan tidak valid");
    exit;
}
