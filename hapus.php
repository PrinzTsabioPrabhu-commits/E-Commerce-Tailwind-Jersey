<?php 
include 'koneksi.php';

if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Gunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("DELETE FROM produk WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        header("Location: index.php?pesan=berhasil_hapus");
    } else {
        header("Location: index.php?pesan=gagal_hapus");
    }
    $stmt->close();
} else {
    header("Location: index.php");
}
mysqli_close($conn);
?>
