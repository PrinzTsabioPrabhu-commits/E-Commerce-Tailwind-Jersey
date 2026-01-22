<?php
include 'koneksi.php';

// Fitur Pencarian Langsung ke Modal
$auto_open = null;
if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
    $cek_produk = mysqli_query($conn, "SELECT * FROM produk WHERE nama_produk LIKE '%$keyword%' LIMIT 1");
    if (mysqli_num_rows($cek_produk) > 0) {
        $auto_open = mysqli_fetch_assoc($cek_produk);
    }
}

// Ambil jumlah total item di keranjang
$query_cart_count = mysqli_query($conn, "SELECT SUM(jumlah) as total FROM keranjang");
$data_cart = mysqli_fetch_assoc($query_cart_count);
$total_item = $data_cart['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jersey Home - Premium Kit Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .product-card { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .product-card:hover { transform: translateY(-12px); box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1); }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.8); backdrop-filter: blur(8px); }
        .modal.show { display: flex; align-items: center; justify-content: center; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #3b82f6; border-radius: 10px; }
    </style>
</head>
<body class="bg-gray-50 text-slate-900">

    <nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-black italic tracking-tighter text-blue-600 flex items-center gap-2">
                <i class="fas fa-bolt text-yellow-400"></i> JERSEY HOME
            </a>

            <div class="hidden lg:flex flex-1 max-w-md mx-8">
                <form action="index.php" method="GET" class="w-full relative group">
                    <input type="text" name="keyword" placeholder="Cari kit impianmu..."
                        value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>"
                        class="w-full bg-gray-100 border-2 border-transparent rounded-2xl py-2.5 px-12 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all outline-none text-sm font-semibold">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition"></i>
                </form>
            </div>

            <div class="flex items-center gap-3 md:gap-6">
                <a href="keranjang.php" class="relative p-2.5 bg-gray-100 rounded-2xl hover:bg-blue-50 transition group">
                    <i class="fas fa-shopping-basket text-xl text-gray-600 group-hover:text-blue-600 transition"></i>
                    <?php if($total_item > 0): ?>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-black w-5 h-5 flex items-center justify-center rounded-full border-2 border-white animate-pulse">
                        <?= $total_item ?>
                    </span>
                    <?php endif; ?>
                </a>

                <a href="tambah.php" class="bg-slate-900 text-white px-5 py-2.5 rounded-2xl font-bold hover:bg-blue-600 transition flex items-center gap-2 shadow-xl shadow-slate-200 text-sm italic uppercase tracking-tighter">
                    <i class="fas fa-plus-circle"></i> <span class="hidden md:inline">Jual Jersey</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="relative bg-blue-600 py-16 md:py-24 overflow-hidden">
        <div class="container mx-auto px-4 relative z-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-12">
                <div class="text-center md:text-left">
                    <span class="inline-block bg-blue-500 text-blue-100 px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-[0.3em] mb-4">Official Store v2.0</span>
                    <h2 class="text-4xl md:text-7xl font-black text-white mb-6 uppercase italic leading-tight tracking-tighter">
                        Jersey Terbaik <br><span class="text-blue-300">Sang Juara</span>
                    </h2>
                    <p class="text-blue-100 max-w-md italic opacity-80 mb-8 text-sm md:text-lg">Koleksi kit premium dari F1, Sepakbola, hingga seri Retro klasik.</p>
                    <div class="flex flex-wrap justify-center md:justify-start gap-4">
                        <div class="bg-white/10 px-6 py-3 rounded-2xl backdrop-blur-md border border-white/20">
                            <p class="text-xl font-black text-white">100%</p>
                            <p class="text-[9px] uppercase tracking-widest text-blue-200">Authentic</p>
                        </div>
                        <div class="bg-white/10 px-6 py-3 rounded-2xl backdrop-blur-md border border-white/20">
                            <p class="text-xl font-black text-white">FREE</p>
                            <p class="text-[9px] uppercase tracking-widest text-blue-200">Shipping</p>
                        </div>
                    </div>
                </div>
                <div class="hidden lg:block relative">
                    <div class="w-80 h-80 bg-blue-500 rounded-[3rem] rotate-12 absolute inset-0 opacity-30"></div>
                    <i class="fas fa-shirt text-[15rem] text-white/20 relative z-10 -rotate-12"></i>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-[0]">
            <svg class="relative block w-full h-[60px]" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C58.47,112.49,123.89,111,184,91.22,244,71.4,285,63.17,321.39,56.44Z" style="fill: #f8fafc;"></path>
            </svg>
        </div>
    </div>

    <div class="container mx-auto px-4 py-16">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-4">
            <div>
                <h3 class="text-3xl font-black italic tracking-tighter uppercase flex items-center gap-3">
                    <span class="w-2 h-10 bg-blue-600 rounded-full"></span> Koleksi Terbaru
                </h3>
                <p class="text-gray-400 text-sm mt-1">Update stok harian jersey pilihan terbaik</p>
            </div>
            <form action="index.php" method="GET" class="lg:hidden w-full relative group">
                <input type="text" name="keyword" placeholder="Cari jersey..." class="w-full bg-white border border-gray-200 rounded-xl py-3 px-10 outline-none focus:border-blue-500 transition">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
            </form>
        </div>

        <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'berhasil_keranjang'): ?>
            <div class="bg-green-600 text-white p-4 rounded-2xl mb-8 shadow-xl shadow-green-100 flex items-center gap-3 animate-bounce">
                <i class="fas fa-check-circle"></i> 
                <span class="text-sm font-bold uppercase tracking-widest">Berhasil! Barang masuk ke keranjang.</span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-10">
            <?php
            $query_str = "SELECT * FROM produk";
            if (isset($_GET['keyword'])) {
                $kw = mysqli_real_escape_string($conn, $_GET['keyword']);
                $query_str .= " WHERE nama_produk LIKE '%$kw%'";
            }
            $query_str .= " ORDER BY id DESC";
            $ambil_data = mysqli_query($conn, $query_str);

            if (mysqli_num_rows($ambil_data) == 0) {
                echo "<div class='col-span-full text-center py-20 bg-white rounded-[3rem] border-2 border-dashed border-gray-200'>
                        <i class='fas fa-search text-5xl text-gray-200 mb-4'></i>
                        <p class='text-gray-400 font-bold italic uppercase tracking-widest'>Jersey tidak ditemukan...</p>
                      </div>";
            }

            while ($row = mysqli_fetch_array($ambil_data)) {
                $foto = (!empty($row['gambar']) && strpos($row['gambar'], 'http') === 0) ? $row['gambar'] : 'img/' . $row['gambar'];
                if (empty($row['gambar'])) $foto = 'https://via.placeholder.com/400x500?text=No+Image';
            ?>
                <div class="product-card bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden group">
                    <div class="relative h-56 md:h-72 bg-gray-50 overflow-hidden cursor-pointer" onclick="openModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                        <img src="<?php echo $foto; ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-700" alt="Jersey">
                        
                        <div class="absolute inset-0 bg-blue-900/40 opacity-0 group-hover:opacity-100 transition duration-500 flex items-center justify-center">
                            <span class="bg-white text-blue-600 px-6 py-2.5 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-2xl scale-75 group-hover:scale-100 transition duration-500">Lihat Detail</span>
                        </div>

                        <a href="tambah_keranjang.php?id=<?= $row['id'] ?>" onclick="event.stopPropagation();" class="absolute bottom-4 right-4 bg-white text-blue-600 w-12 h-12 rounded-2xl shadow-2xl flex items-center justify-center translate-y-20 group-hover:translate-y-0 transition-all duration-500 hover:bg-blue-600 hover:text-white">
                            <i class="fas fa-cart-plus text-lg"></i>
                        </a>
                    </div>

                    <div class="p-6">
                        <span class="inline-block text-[9px] text-blue-600 font-black uppercase tracking-[0.2em] mb-2 px-2 py-1 bg-blue-50 rounded-lg italic">Premium Kit</span>
                        <h5 class="font-black text-gray-900 truncate mb-1 group-hover:text-blue-600 transition cursor-pointer" onclick="openModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                            <?php echo htmlspecialchars($row['nama_produk']); ?>
                        </h5>
                        <div class="flex items-baseline gap-1 mb-6">
                            <span class="text-xs font-bold text-red-500">Rp</span>
                            <span class="text-xl font-black text-red-600 tracking-tighter"><?php echo number_format($row['harga'], 0, ',', '.'); ?></span>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                            <div class="flex gap-4">
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="text-gray-300 hover:text-blue-500 transition-all"><i class="fas fa-pencil-alt text-sm"></i></a>
                                <a href="hapus.php?id=<?php echo $row['id']; ?>" class="text-gray-300 hover:text-red-500 transition-all" onclick="return confirm('Hapus produk ini?')"><i class="fas fa-trash-alt text-sm"></i></a>
                            </div>
                            <span class="text-[9px] font-black text-gray-300 uppercase italic">Stock: <?= $row['stok'] ?></span>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <footer class="bg-white border-t border-gray-100 py-12">
        <div class="container mx-auto px-4 text-center">
            <h4 class="text-lg font-black italic tracking-tighter text-blue-600 mb-4">JERSEY HOME</h4>
            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.3em] mb-8">&copy; 2026 Crafted with Passion for Football Fans</p>
            <div class="flex justify-center gap-6 text-gray-300">
                <i class="fab fa-instagram hover:text-pink-500 transition cursor-pointer"></i>
                <i class="fab fa-tiktok hover:text-black transition cursor-pointer"></i>
                <i class="fab fa-whatsapp hover:text-green-500 transition cursor-pointer"></i>
            </div>
        </div>
    </footer>

    <div id="productModal" class="modal">
        <div class="bg-white rounded-[3rem] shadow-2xl overflow-hidden w-[95%] max-w-5xl flex flex-col md:flex-row relative animate-modal">
            <button onclick="closeModal()" class="absolute top-6 right-6 z-50 bg-white/80 backdrop-blur-sm text-gray-900 w-12 h-12 rounded-2xl flex items-center justify-center font-bold hover:bg-red-500 hover:text-white transition-all shadow-xl group">
                <i class="fas fa-times group-hover:rotate-90 transition duration-300"></i>
            </button>

            <div class="w-full md:w-[55%] bg-gray-50 h-[300px] md:h-auto p-4 md:p-8 flex items-center justify-center">
                <img id="modalImage" src="" class="w-full h-full object-contain drop-shadow-2xl rounded-3xl" alt="Preview">
            </div>

            <div class="w-full md:w-[45%] p-8 md:p-12 flex flex-col bg-white">
                <div class="mb-4">
                    <span class="bg-blue-600 text-white px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest italic shadow-lg shadow-blue-100">Official Store Kit</span>
                </div>

                <h3 id="modalNama" class="text-3xl md:text-4xl font-black mb-2 text-slate-900 leading-[0.9] tracking-tighter italic uppercase"></h3>
                <div class="flex items-baseline gap-1 mb-8">
                    <span class="text-lg font-black text-red-500">Rp</span>
                    <span id="modalHarga" class="text-4xl font-black text-red-600 tracking-tighter"></span>
                </div>

                <div class="flex-grow overflow-y-auto custom-scroll pr-4 mb-8">
                    <p class="text-slate-400 text-[10px] font-black uppercase mb-3 tracking-[0.2em] sticky top-0 bg-white">Product Detail</p>
                    <p id="modalDeskripsi" class="text-slate-600 text-sm md:text-base leading-relaxed font-medium italic"></p>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <a id="waBtn" href="#" target="_blank" class="w-full bg-green-500 text-white py-5 rounded-[1.5rem] font-black italic uppercase tracking-wider flex items-center justify-center gap-3 hover:bg-green-600 transition shadow-2xl shadow-green-100 group">
                        <i class="fab fa-whatsapp text-2xl group-hover:scale-125 transition"></i> Beli via WhatsApp
                    </a>

                    <a id="modalCartBtn" href="#" class="w-full bg-blue-600 text-white py-5 rounded-[1.5rem] font-black italic uppercase tracking-wider flex items-center justify-center gap-3 hover:bg-blue-700 transition shadow-2xl shadow-blue-100">
                        <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                    </a>
                </div>
                
                <p class="text-center mt-6 text-[9px] text-slate-300 font-bold uppercase tracking-widest leading-relaxed">
                    Pengiriman seluruh Indonesia • Cicilan 0% tersedia • Garansi Retur 7 Hari
                </p>
            </div>
        </div>
    </div>

    <script>
        window.onload = () => {
            <?php if ($auto_open): ?>
                openModal(<?php echo json_encode($auto_open); ?>);
            <?php endif; ?>
        }

        function openModal(product) {
            const modal = document.getElementById('productModal');
            let foto = (product.gambar && product.gambar.startsWith('http')) ? product.gambar : 'img/' + product.gambar;
            if (!product.gambar) foto = 'https://via.placeholder.com/400x500?text=No+Image';

            document.getElementById('modalImage').src = foto;
            document.getElementById('modalNama').textContent = product.nama_produk;
            document.getElementById('modalHarga').textContent = parseInt(product.harga).toLocaleString('id-ID');
            document.getElementById('modalDeskripsi').textContent = product.deskripsi || "Tidak ada rincian tambahan untuk koleksi ini. Silakan hubungi admin untuk detail material.";

            document.getElementById('modalCartBtn').href = 'tambah_keranjang.php?id=' + product.id;

            const waNumber = "6281234567890"; // Ganti Nomor Anda
            const waText = `Halo Admin, saya tertarik dengan jersey *${product.nama_produk}* seharga *Rp ${parseInt(product.harga).toLocaleString('id-ID')}*. Apakah stok masih ada?`;
            document.getElementById('waBtn').href = `https://wa.me/${waNumber}?text=${encodeURIComponent(waText)}`;

            modal.classList.add('show');
            document.body.style.overflow = 'hidden'; // Stop scroll
        }

        function closeModal() {
            document.getElementById('productModal').classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        window.onclick = (e) => {
            if (e.target == document.getElementById('productModal')) closeModal();
        }
    </script>
</body>
</html>