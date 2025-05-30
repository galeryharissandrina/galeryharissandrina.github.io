<?php
$koneksi = mysqli_connect("localhost", "root", "", "galeri_kenangan");
// table user //
// field //
// id_user
// name
// profile

// config.php - Konfigurasi Database
class Database {
    private $host = "localhost";        // Host database Anda
    private $db_name = "galeri_kenangan"; // Nama database
    private $username = "root";         // Username database
    private $password = "";             // Password database



    // Koneksi database
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->database_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

// Fungsi untuk mendapatkan quote romantis secara random
function getRandomRomanticQuote($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT quote_text FROM romantic_quotes ORDER BY RAND() LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['quote_text'] : "Bersamamu, setiap momen adalah kenangan terindah 💕";
    } catch(PDOException $e) {
        return "Bersamamu, setiap momen adalah kenangan terindah 💕";
    }
}

// Fungsi untuk membuat nama file yang unik
function generateUniqueFileName($originalName) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    return 'photo_' . uniqid() . '_' . time() . '.' . $extension;
}

// Fungsi untuk validasi file gambar
function validateImageFile($file) {
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 10 * 1024 * 1024; // 10MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        return "Hanya file gambar (JPEG, PNG, GIF, WebP) yang diizinkan.";
    }
    
    if ($file['size'] > $maxSize) {
        return "Ukuran file terlalu besar. Maksimal 10MB.";
    }
    
    return true;
}
?>