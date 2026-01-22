<?php 
include 'koneksi.php';

$error = "";
$success = false;
$row = null;

if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

$result = mysqli_query($conn, "SELECT * FROM produk WHERE id = $id");
if(!$result || mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}
$row = mysqli_fetch_assoc($result);

if(isset($_POST['update'])) {
    $nama = trim($_POST['nama_produk']);
    $deskripsi = trim($_POST['deskripsi']);
    
    // Perbaikan: Hilangkan karakter selain angka sebelum simpan ke DB
    $harga = preg_replace('/[^0-9]/', '', $_POST['harga']); 
    $harga = (float)$harga;
    
    $stok = (int)$_POST['stok'];
    $gambar_input = trim($_POST['gambar']);
    $gambar_final = $row['gambar'];

    if(isset($_FILES['file_gambar']) && $_FILES['file_gambar']['size'] > 0) {
        $file = $_FILES['file_gambar'];
        $allowed = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if(in_array($ext, $allowed)) {
            $upload_dir = 'img/';
            if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $new_filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
            if(move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
                $gambar_final = $new_filename;
            }
        }
    } elseif (!empty($gambar_input)) {
        $gambar_final = $gambar_input;
    }

    if(empty($error)) {
        $stmt = $conn->prepare("UPDATE produk SET nama_produk = ?, deskripsi = ?, harga = ?, stok = ?, gambar = ? WHERE id = ?");
        $stmt->bind_param("ssdisi", $nama, $deskripsi, $harga, $stok, $gambar_final, $id);
        
        if($stmt->execute()) {
            $success = true;
            $row['nama_produk'] = $nama;
            $row['harga'] = $harga;
            $row['stok'] = $stok;
            $row['gambar'] = $gambar_final;
            header("refresh:2;url=index.php");
        } else {
            $error = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$foto_path = $row['gambar'];
$display_foto = (filter_var($foto_path, FILTER_VALIDATE_URL)) ? $foto_path : (file_exists('img/'.$foto_path) && !empty($foto_path) ? 'img/'.$foto_path : 'https://via.placeholder.com/300?text=No+Image');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen py-10 text-gray-800">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <a href="index.php" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition font-medium">
            <i class="fas fa-arrow-left"></i> Kembali ke Katalog
        </a>

        <?php if($success): ?>
            <div class="bg-green-500 text-white p-4 rounded-lg mb-6 shadow-lg flex items-center gap-3">
                <i class="fas fa-check-circle text-xl"></i>
                <p>Data berhasil diperbarui! Mengalihkan halaman...</p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-1">
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 sticky top-10">
                    <h3 class="font-bold mb-4 text-gray-600 uppercase text-xs tracking-wider">Preview Produk</h3>
                    <div class="aspect-square rounded-xl overflow-hidden bg-gray-50 border border-gray-100">
                        <img src="<?= $display_foto ?>" class="w-full h-full object-cover">
                    </div>
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                        <p class="text-xs text-blue-600 font-bold uppercase">Harga Sekarang</p>
                        <p class="text-xl font-black text-blue-800">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                    <form method="POST" enctype="multipart/form-data" class="space-y-6">
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Produk</label>
                            <input type="text" name="nama_produk" value="<?= htmlspecialchars($row['nama_produk']) ?>" class="w-full border-2 border-gray-100 p-3 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-50/50 outline-none transition" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Harga (Rupiah)</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                    <input type="text" id="input_harga" name="harga" 
                                           value="<?= number_format($row['harga'], 0, ',', '.') ?>" 
                                           class="w-full border-2 border-gray-100 p-3 pl-12 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-50/50 outline-none transition font-bold text-blue-700" required>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1">* Ketik angka saja, titik otomatis muncul</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Stok Barang</label>
                                <input type="number" name="stok" value="<?= $row['stok'] ?>" class="w-full border-2 border-gray-100 p-3 rounded-xl focus:border-blue-500 outline-none" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Keterangan Produk</label>
                            <textarea name="deskripsi" rows="3" class="w-full border-2 border-gray-100 p-3 rounded-xl focus:border-blue-500 outline-none"><?= htmlspecialchars($row['deskripsi'] ?? '') ?></textarea>
                        </div>

                        <div class="bg-gray-50 p-5 rounded-2xl border-2 border-dashed border-gray-200">
                            <label class="block text-sm font-bold text-gray-700 mb-3">Update Foto</label>
                            <input type="file" name="file_gambar" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 mb-4">
                            <input type="text" name="gambar" placeholder="Atau tempel URL gambar di sini..." class="w-full border-2 border-white p-3 rounded-xl outline-none text-sm shadow-sm" value="<?= (filter_var($row['gambar'], FILTER_VALIDATE_URL)) ? $row['gambar'] : '' ?>">
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button type="submit" name="update" class="flex-1 bg-blue-600 text-white font-bold py-4 rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 active:scale-95">
                                Simpan Perubahan
                            </button>
                            <a href="index.php" class="flex-1 bg-gray-100 text-gray-600 font-bold py-4 rounded-xl hover:bg-gray-200 transition text-center active:scale-95">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const inputHarga = document.getElementById('input_harga');

        inputHarga.addEventListener('keyup', function(e) {
            // Gunakan fungsi formatRupiah saat mengetik
            this.value = formatRupiah(this.value);
        });

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }
    </script>
</body>
</html>