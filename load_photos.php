<?php
include "koneksi.php";
// load_photos.php - Mengambil semua foto dari database
header('Content-Type: application/json');
require_once 'config.php';

$response = array();

try {
    // Koneksi database
    $database = new Database();
    $pdo = $database->getConnection();
    
    if (!$pdo) {
        throw new Exception("Koneksi database gagal");
    }
    
    // Ambil semua foto dari database
    $stmt = $pdo->prepare("
        SELECT id, filename, original_name, file_path, romantic_quote, upload_date, file_size 
        FROM photos 
        ORDER BY upload_date DESC
    ");
    $stmt->execute();
    
    $photos = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $photos[] = array(
            'id' => $row['id'],
            'filename' => $row['filename'],
            'originalName' => $row['original_name'],
            'path' => $row['file_path'],
            'quote' => $row['romantic_quote'],
            'uploadDate' => $row['upload_date'],
            'fileSize' => $row['file_size']
        );
    }
    
    $response['success'] = true;
    $response['photos'] = $photos;
    $response['total'] = count($photos);
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    $response['photos'] = array();
}

echo json_encode($response);
?>