<?php 
include 'koneksi.php';

$error = "";
$success = false;

if(isset($_POST['simpan'])){
    $nama = trim($_POST['nama_produk']);
    $deskripsi = trim($_POST['deskripsi']);
    $harga = (float)$_POST['harga'];
    $stok = (int)$_POST['stok'];
    $gambar = trim($_POST['gambar']);
    
    if(isset($_FILES['file_gambar']) && $_FILES['file_gambar']['size'] > 0) {
        $file = $_FILES['file_gambar'];
        $filename = basename($file['name']);
        $tmp_name = $file['tmp_name'];
        $error_file = $file['error'];
        
        $allowed = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        $filename_parts = explode('.', $filename);
        $file_ext = strtolower(end($filename_parts));
        
        if($error_file === UPLOAD_ERR_OK) {
            if(in_array($file_ext, $allowed)) {
                $new_filename = time() . '_' . rand(1000, 9999) . '.' . $file_ext;
                $upload_dir = __DIR__ . '/img/';
                $upload_path = $upload_dir . $new_filename;
                
                if(!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                if(move_uploaded_file($tmp_name, $upload_path)) {
                    chmod($upload_path, 0666);
                    $gambar = $new_filename; 
                } else {
                    $error = "Gagal upload file! Pastikan folder img writable.";
                }
            } else {
                $error = "Format file tidak didukung! Gunakan: jpg, jpeg, png, gif, webp";
            }
        } elseif($error_file !== UPLOAD_ERR_NO_FILE) {
            $error = "Error upload file: " . $error_file;
        }
    }
    
    if(empty($nama)) {
        $error = "Nama produk tidak boleh kosong!";
    } else if($harga < 0) {
        $error = "Harga tidak boleh negatif!";
    } else if($stok < 0) {
        $error = "Stok tidak boleh negatif!";
    } else if(empty($gambar)) {
        $error = "Gambar harus diisi (file atau URL)!";
    } else if(empty($error)) {
        $stmt = $conn->prepare("INSERT INTO produk (nama_produk, deskripsi, harga, stok, gambar) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $nama, $deskripsi, $harga, $stok, $gambar);
        
        if($stmt->execute()) {
            $success = true;
            header("refresh:2;url=index.php");
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Produk - Jersey Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

<div class="min-h-screen flex flex-col">
    <nav class="bg-white border-b border-gray-100 py-4">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <a href="index.php" class="text-sm font-bold text-blue-600 flex items-center gap-2 group">
                <i class="fas fa-chevron-left group-hover:-translate-x-1 transition"></i> KEMBALI KE KATALOG
            </a>
            <span class="text-[10px] font-black tracking-widest text-gray-400 uppercase italic">Admin Dashboard v2.0</span>
        </div>
    </nav>

    <div class="flex-grow flex items-center justify-center py-12 px-4">
        <div class="max-w-xl w-full">
            
            <?php if(!empty($error)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl mb-6 shadow-sm flex items-center gap-3 animate-bounce">
                    <i class="fas fa-circle-xmark text-red-500 text-xl"></i>
                    <p class="text-red-700 text-sm font-bold"><?= $error; ?></p>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-xl mb-6 shadow-sm flex items-center gap-3">
                    <i class="fas fa-circle-check text-green-500 text-xl"></i>
                    <p class="text-green-700 text-sm font-bold">Produk berhasil disimpan! Mengalihkan...</p>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                <div class="bg-blue-600 p-8 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <h1 class="text-2xl font-black italic tracking-tighter uppercase mb-1">Tambah Produk</h1>
                        <p class="text-blue-100 text-xs font-medium uppercase tracking-widest opacity-80">Lengkapi detail jersey terbaru anda</p>
                    </div>
                    <i class="fas fa-shirt absolute -right-4 -bottom-4 text-8xl text-blue-500 opacity-20 rotate-12"></i>
                </div>

                <form method="POST" enctype="multipart/form-data" class="p-8 md:p-10 space-y-6">
                    
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Nama Koleksi</label>
                        <input type="text" name="nama_produk" required 
                               class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-4 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all outline-none font-bold text-gray-700 placeholder:text-gray-300"
                               placeholder="Contoh: Jersey Home 2024/25">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Harga (Rp)</label>
                            <input type="number" name="harga" required min="0"
                                   class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-4 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all outline-none font-bold text-gray-700"
                                   placeholder="0">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Stok Unit</label>
                            <input type="number" name="stok" required min="0"
                                   class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-4 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all outline-none font-bold text-gray-700"
                                   placeholder="0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Deskripsi Produk</label>
                        <textarea name="deskripsi" rows="3"
                                  class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-4 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all outline-none font-medium text-gray-600 placeholder:text-gray-300"
                                  placeholder="Jelaskan detail material, ukuran, dll..."></textarea>
                    </div>

                    <div class="bg-gray-50 rounded-[2rem] p-6 border-2 border-dashed border-gray-200 group hover:border-blue-300 transition-colors">
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-cloud-upload-alt text-blue-500"></i>
                            </div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Media Gambar</p>
                            
                            <input type="file" name="file_gambar" id="file_gambar" accept="image/*" class="hidden">
                            <label for="file_gambar" class="cursor-pointer bg-white border border-gray-200 px-6 py-2 rounded-full text-xs font-bold text-gray-600 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-sm mb-4">
                                Pilih File Lokal
                            </label>

                            <div class="flex items-center gap-3 w-full mb-2">
                                <div class="h-[1px] bg-gray-200 flex-grow"></div>
                                <span class="text-[9px] font-black text-gray-300 uppercase">Atau Gunakan URL</span>
                                <div class="h-[1px] bg-gray-200 flex-grow"></div>
                            </div>

                            <input type="text" name="gambar" 
                                   class="w-full bg-white border border-gray-100 rounded-xl px-4 py-2 text-xs focus:ring-2 focus:ring-blue-100 outline-none text-gray-500"
                                   placeholder="https://link-gambar-anda.jpg">
                        </div>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" name="simpan" 
                                class="flex-[2] bg-blue-600 text-white py-5 rounded-2xl font-black italic uppercase tracking-widest text-sm shadow-xl shadow-blue-100 hover:bg-blue-700 active:scale-95 transition-all flex items-center justify-center gap-3">
                            <i class="fas fa-save"></i> Simpan Data
                        </button>
                        <a href="index.php" 
                           class="flex-1 bg-gray-100 text-gray-400 py-5 rounded-2xl font-black italic uppercase tracking-widest text-sm text-center hover:bg-gray-200 transition-all">
                            Batal
                        </a>
                    </div>

                </form>
            </div>
            
            <p class="text-center mt-8 text-gray-300 text-[10px] font-bold uppercase tracking-[0.3em]">
                &copy; 2026 Jersey Home Exclusive
            </p>
        </div>
    </div>
</div>

<script>
    // Preview nama file sederhana saat diupload
    document.getElementById('file_gambar').onchange = function() {
        if(this.files[0]) {
            alert("File siap: " + this.files[0].name);
        }
    };
</script>

</body>
</html>