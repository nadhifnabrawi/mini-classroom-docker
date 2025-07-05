<?php

class Classroom {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Ambil semua kelas
    public function getAllClasses() {
        $this->db->query("SELECT * FROM classes ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    // Ambil satu kelas berdasarkan ID
    public function getClassById($id) {
        $this->db->query("SELECT * FROM classes WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Buat kelas baru
    public function createClass($name, $description) {
        $this->db->query("INSERT INTO classes (name, description) VALUES (:name, :description)");
        $this->db->bind(':name', $name);
        $this->db->bind(':description', $description);
        return $this->db->execute();
    }

    // Tambahkan materi ke kelas
    public function addMaterial($class_id, $title, $description) {
        $this->db->query("INSERT INTO materials (class_id, title, description) VALUES (:class_id, :title, :description)");
        $this->db->bind(':class_id', $class_id);
        $this->db->bind(':title', $title);
        $this->db->bind(':description', $description);
        return $this->db->execute();
    }

    // Ambil semua materi berdasarkan kelas
    public function getMaterialsByClassId($class_id) {
        $this->db->query("SELECT * FROM materials WHERE class_id = :class_id ORDER BY created_at DESC");
        $this->db->bind(':class_id', $class_id);
        return $this->db->resultSet();
    }

    public function getMaterialById($id) {
    $this->db->query("SELECT * FROM materials WHERE id = :id");
    $this->db->bind(':id', $id);
    return $this->db->single();
    }

    public function updateMaterial($id, $title, $description) {
    $this->db->query("UPDATE materials SET title = :title, description = :description WHERE id = :id");
    $this->db->bind(':title', $title);
    $this->db->bind(':description', $description);
    $this->db->bind(':id', $id);
    return $this->db->execute();
    }

    // Hapus kelas (beserta materi karena FK CASCADE)
    public function deleteClass($id) {
        $this->db->query("DELETE FROM classes WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // ✅ Hapus satu materi
    public function deleteMaterial($id) {
        $this->db->query("DELETE FROM materials WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function updateClass($id, $name, $description) {
    $this->db->query("UPDATE classes SET name = :name, description = :description WHERE id = :id");
    $this->db->bind(':id', $id);
    $this->db->bind(':name', $name);
    $this->db->bind(':description', $description);
    return $this->db->execute();
}

}
