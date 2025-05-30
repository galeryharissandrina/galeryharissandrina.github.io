<?php
$conn = new mysqli("localhost", "root", "", "galeri_kenangan");
$result = $conn->query("SELECT filename FROM photos");
while ($row = $result->fetch_assoc()) {
    echo "<img src='uploads/" . htmlspecialchars($row['filename']) . "' 
width='200' style='margin:10px;'>";
}
?>