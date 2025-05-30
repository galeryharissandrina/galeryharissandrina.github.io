<?php
 $folder = "uploads/";
 $allowed_types = ['jpg', 'jpeg', 'png', 'gif']; 
 // Buka folder
 $files = scandir($folder);
 foreach ($files as $file) {
 $file_path = $folder . $file;
 $file_ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
 // Skip . dan .. dan file yang bukan gambar
 if (in_array($file_ext, $allowed_types)) {
 echo "<img src='$file_path' style='width:200px; margin:10px;'>\n";
 }
 }
 ?>