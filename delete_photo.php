<?php
include "koneksi.php";
// delete_photo.php - Menghapus foto dari database dan file system
header('Content-Type: application/json');
require_once 'config.php';

$response = array();

try {
    // Cek method request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Method tidak diizinkan");
    }
    
    // Ambil data JSON dari request body
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['photo_id']) || empty($input['photo_id'])) {
        throw new Exception("ID foto tidak ditemukan");
    }
    
    $photoId = (int)$input['photo_id'];
    
    // Koneksi database
    $database = new Database();
    $pdo = $database->getConnection();
    
    if (!$pdo) {
        throw new Exception("Koneksi database gagal");
    }
    
    // Ambil data foto untuk mendapatkan path file
    $stmt = $pdo->prepare("SELECT file_path FROM photos WHERE id = ?");
    $stmt->execute([$photoId]);
    $photo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$photo) {
        throw new Exception("Foto tidak ditemukan");
    }
    
    // Hapus file dari sistem file
    if (file_exists($photo['file_path'])) {
        unlink($photo['file_path']);
    }
    
    // Hapus data dari database
    $stmt = $pdo->prepare("DELETE FROM photos WHERE id = ?");
    $result = $stmt->execute([$photoId]);
    
    if ($result) {
        $response['success'] = true;
        $response['message'] = "Foto berhasil dihapus 💔";
    } else {
        throw new Exception("Gagal menghapus foto dari database");
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>