<?php
$conn = new mysqli("localhost", "root", "", "galeri_kenangan");

// Cek koneksi database
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->autocommit(true);
$conn->set_charset("utf8");

if (isset($_POST["submit"])) {
    $target_dir = "uploads/";
    
    // Buat folder uploads jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    $uploaded_files = 0;
    $errors = array();
    
    // Cek apakah ada file yang diupload
    if (isset($_FILES["photos"]) && !empty($_FILES["photos"]["name"][0])) {
        
        // Loop untuk setiap file (karena multiple upload)
        for ($i = 0; $i < count($_FILES["photos"]["name"]); $i++) {
            
            $filename = basename($_FILES["photos"]["name"][$i]);
            $temp_file = $_FILES["photos"]["tmp_name"][$i];
            
            // Buat nama file unik untuk menghindari duplikasi
            $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
            $file_name_only = pathinfo($filename, PATHINFO_FILENAME);
            $unique_filename = $file_name_only . '_' . time() . '_' . $i . '.' . $file_extension;
            $target_file = $target_dir . $unique_filename;
            
            // Cek apakah file benar gambar
            $check = getimagesize($temp_file);
            if ($check !== false) {
                
                // Cek ukuran file (max 5MB)
                if ($_FILES["photos"]["size"][$i] <= 5000000) {
                    
                    // Cek format file yang diizinkan
                    $allowed_formats = array("jpg", "jpeg", "png", "gif");
                    if (in_array(strtolower($file_extension), $allowed_formats)) {
                        
                        // Upload file
                        if (move_uploaded_file($temp_file, $target_file)) {
                            
                            // Data untuk database
                            $file_path = $target_file;
                            $file_size = $_FILES["photos"]["size"][$i];
                            $mime_type = $check['mime'];
                            $romantic_quote = ""; // Bisa diisi sesuai kebutuhan
                            
                            // Simpan ke database dengan struktur tabel yang benar
                            $sql = "INSERT INTO photos (filename, original_name, file_path, romantic_quote, upload_date, file_size, mime_type) VALUES (?, ?, ?, ?, NOW(), ?, ?)";
                            $stmt = $conn->prepare($sql);
                            
                            if ($stmt) {
                                $stmt->bind_param("ssssis", $unique_filename, $filename, $file_path, $romantic_quote, $file_size, $mime_type);
                                
                                if ($stmt->execute()) {
                                    $uploaded_files++;
                                } else {
                                    $errors[] = "Gagal menyimpan $filename ke database: " . $stmt->error;
                                    // Hapus file jika gagal save ke database
                                    unlink($target_file);
                                }
                                $stmt->close();
                            } else {
                                $errors[] = "Gagal mempersiapkan query untuk $filename: " . $conn->error;
                                // Hapus file jika gagal save ke database
                                unlink($target_file);
                            }
                            
                        } else {
                            $errors[] = "Gagal memindahkan file $filename";
                        }
                        
                    } else {
                        $errors[] = "$filename - Format file tidak diizinkan. Gunakan JPG, JPEG, PNG, atau GIF";
                    }
                    
                } else {
                    $errors[] = "$filename - Ukuran file terlalu besar (max 5MB)";
                }
                
            } else {
                $errors[] = "$filename - File bukan gambar";
            }
        }
        
        // Response hasil upload
        if ($uploaded_files > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => "$uploaded_files foto berhasil diupload dan disimpan",
                'uploaded' => $uploaded_files,
                'errors' => $errors
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Tidak ada foto yang berhasil diupload',
                'errors' => $errors
            ]);
        }
        
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tidak ada file yang dipilih'
        ]);
    }
    
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Form tidak disubmit dengan benar'
    ]);
}

$conn->close();
?>