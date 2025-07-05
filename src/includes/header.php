<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Classroom</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="index.php">Mini Classroom</a></h1>
            <nav>
                <ul>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'guru'): ?>
                        <li><a href="index.php?page=create_class">Buat Kelas Baru</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container main-content">
