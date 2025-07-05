<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Classroom.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$classroom = new Classroom();

$page = $_GET['page'] ?? 'home';
$class_id = $_GET['class_id'] ?? null;
$material_id = $_GET['material_id'] ?? null;

// Handle form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['role'] === 'guru') {
    if ($_POST['action'] === 'create_class') {
        $classroom->createClass(trim($_POST['class_name']), trim($_POST['class_description']));
        header("Location: index.php");
        exit;
    }

    if ($_POST['action'] === 'update_class') {
        $classroom->updateClass($_POST['class_id'], trim($_POST['class_name']), trim($_POST['class_description']));
        header("Location: index.php");
        exit;
    }

    if ($_POST['action'] === 'delete_class') {
        $classroom->deleteClass($_POST['class_id']);
        header("Location: index.php");
        exit;
    }

    if ($_POST['action'] === 'add_material') {
        $classroom->addMaterial($_POST['class_id'], trim($_POST['material_title']), trim($_POST['material_description']));
        header("Location: index.php?page=view_class&class_id=" . $_POST['class_id']);
        exit;
    }

    if ($_POST['action'] === 'update_material') {
        $classroom->updateMaterial($_POST['material_id'], trim($_POST['material_title']), trim($_POST['material_description']));
        header("Location: index.php?page=view_class&class_id=" . $_POST['class_id']);
        exit;
    }

    if ($_POST['action'] === 'delete_material') {
        $classroom->deleteMaterial($_POST['material_id']);
        header("Location: index.php?page=view_class&class_id=" . $_POST['class_id']);
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="wrapper">
    <div class="top-bar">
        <p>Halo, <?= htmlspecialchars($_SESSION['name']) ?> (<?= $_SESSION['role'] ?>) |
            <a href="logout.php">Logout</a></p>
    </div>

    <div class="container main-content">
        <?php
        switch ($page):
            case 'home':
                $classes = $classroom->getAllClasses(); ?>
                <h2>Daftar Kelas</h2>

                <?php if (empty($classes)): ?>
                    <p>Tidak ada kelas.</p>
                <?php else: ?>
                    <div class="card-list">
                        <?php foreach ($classes as $class): ?>
    <div class="card">
        <h3><?= htmlspecialchars($class->name) ?></h3>
        <p><?= htmlspecialchars(substr($class->description, 0, 100)) ?><?= strlen($class->description) > 100 ? '...' : '' ?></p>

        <div class="actions">
            <!-- Tombol View untuk semua role -->
            <a href="index.php?page=view_class&class_id=<?= $class->id ?>" class="btn success">View</a>

            <!-- Edit & Delete hanya untuk guru -->
            <?php if ($_SESSION['role'] === 'guru'): ?>
                <a href="index.php?page=edit_class&class_id=<?= $class->id ?>" class="btn success">Edit</a>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus kelas ini?')">
                    <input type="hidden" name="action" value="delete_class">
                    <input type="hidden" name="class_id" value="<?= $class->id ?>">
                    <button type="submit" class="btn danger">Delete</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>

                    </div>
                <?php endif;
                break;

            case 'create_class': ?>
                <h2>Buat Kelas Baru</h2>
                <form action="index.php" method="POST">
                    <input type="hidden" name="action" value="create_class">
                    <div class="form-group">
                        <label>Nama Kelas:</label>
                        <input type="text" name="class_name" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi:</label>
                        <textarea name="class_description" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn">Buat</button>
                </form>
                <?php break;

            case 'edit_class':
                $class = $classroom->getClassById($class_id); ?>
                <h2>Edit Kelas</h2>
                <form action="index.php" method="POST">
                    <input type="hidden" name="action" value="update_class">
                    <input type="hidden" name="class_id" value="<?= $class_id ?>">
                    <div class="form-group">
                        <label>Nama Kelas:</label>
                        <input type="text" name="class_name" value="<?= htmlspecialchars($class->name) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi:</label>
                        <textarea name="class_description" rows="4"><?= htmlspecialchars($class->description) ?></textarea>
                    </div>
                    <button type="submit" class="btn">Update</button>
                </form>
                <?php break;

            case 'view_class':
                $class = $classroom->getClassById($class_id);
                $materials = $classroom->getMaterialsByClassId($class_id); ?>
                <h2><?= htmlspecialchars($class->name) ?></h2>
                <p><strong>Deskripsi:</strong> <?= htmlspecialchars($class->description) ?></p>

                <h3>Materi</h3>
                <?php if (empty($materials)): ?>
                    <p>Belum ada materi.</p>
                <?php else: ?>
                    <div class="material-list">
                        <?php foreach ($materials as $m): ?>
                            <div class="material-card">
                                <h4><?= htmlspecialchars($m->title) ?></h4>
                                <p><?= htmlspecialchars($m->description) ?></p>
                                <form action="upload_material.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="class_id" value="<?= $class_id ?>">
                                    
                                </form>
                                <small>Diposting: <?= $m->created_at ?></small>
                                <?php if ($_SESSION['role'] === 'guru'): ?>
                                    <div class="actions">
                                        <a href="index.php?page=edit_material&material_id=<?= $m->id ?>&class_id=<?= $class_id ?>" class="btn warning small">Edit</a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus materi ini?')">
                                            <input type="hidden" name="action" value="delete_material">
                                            <input type="hidden" name="material_id" value="<?= $m->id ?>">
                                            <input type="hidden" name="class_id" value="<?= $class_id ?>">
                                            <button type="submit" class="btn danger small">Hapus</button>
                                        </form>

                                        
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'guru'): ?>
                    <hr>
                    <h3>Tambah Materi</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_material">
                        <input type="hidden" name="class_id" value="<?= $class_id ?>">
                        <div class="form-group">
                            <label>Judul:</label>
                            <input type="text" name="material_title" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi:</label>
                            <textarea name="material_description"></textarea>
                        </div>
                        <button type="submit" class="btn">Tambah</button>
                    </form>
                <?php endif;
                break;

            case 'edit_material':
                $material = $classroom->getMaterialById($material_id); ?>
                <h2>Edit Materi</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="update_material">
                    <input type="hidden" name="material_id" value="<?= $material_id ?>">
                    <input type="hidden" name="class_id" value="<?= $class_id ?>">
                    <div class="form-group">
                        <label>Judul:</label>
                        <input type="text" name="material_title" value="<?= htmlspecialchars($material->title) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi:</label>
                        <textarea name="material_description"><?= htmlspecialchars($material->description) ?></textarea>
                    </div>
                    <button type="submit" class="btn">Simpan</button>
                </form>
                <?php break;

            default:
                echo '<p class="alert error">Halaman tidak ditemukan.</p>';
                break;
        endswitch;
        ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
