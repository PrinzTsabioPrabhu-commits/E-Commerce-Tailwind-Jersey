<?php 
include 'koneksi.php'; 

// Proteksi Sederhana & Logika Hapus
if(isset($_GET['hapus'])) {
    $id_keranjang = filter_var($_GET['hapus'], FILTER_SANITIZE_NUMBER_INT);
    $stmt = $conn->prepare("DELETE FROM keranjang WHERE id_keranjang = ?");
    $stmt->bind_param("i", $id_keranjang);
    if($stmt->execute()) {
        header("location: keranjang.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart — Jersey Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.01em; }
        .glass-nav { background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .checkout-card { position: sticky; top: 120px; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .cart-item-row { transition: transform 0.2s ease, background-color 0.2s ease; }
        .cart-item-row:hover { transform: scale(1.005); background-color: rgba(248, 250, 252, 0.8); }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#fcfdfe] text-slate-900 antialiased">

<nav class="glass-nav sticky top-0 z-50 border-b border-slate-100/50">
    <div class="container mx-auto px-8 py-6 flex justify-between items-center">
        <a href="index.php" class="group flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                <i class="fas fa-arrow-left text-[10px]"></i>
            </div>
            <span class="text-[11px] font-extrabold uppercase tracking-[0.25em] text-slate-400 group-hover:text-blue-600 transition-colors">Belanja Lagi</span>
        </a>
        <div class="absolute left-1/2 -translate-x-1/2">
            <h1 class="text-xl font-[800] italic tracking-tighter uppercase">My <span class="text-blue-600">Bag</span></h1>
        </div>
        <div class="flex items-center gap-4 text-slate-400">
            <i class="fas fa-shield-halved text-xs"></i>
            <span class="text-[10px] font-bold uppercase tracking-widest hidden sm:block">Secure Checkout</span>
        </div>
    </div>
</nav>

<main class="container mx-auto px-8 max-w-7xl py-16">
    <div class="flex flex-col lg:flex-row gap-16">
        
        <div class="flex-[1.5]">
            <div class="mb-8 flex items-end justify-between px-2">
                <div>
                    <h2 class="text-3xl font-black tracking-tight italic uppercase">Review <span class="text-blue-600">Items</span></h2>
                    <p class="text-slate-400 text-sm font-medium mt-1">Kelola item pilihanmu sebelum checkout.</p>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.02)] overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left">
                        <thead class="border-b border-slate-50 text-[10px] uppercase tracking-[0.2em] text-slate-400 font-bold">
                            <tr>
                                <th class="px-8 py-6">Product Information</th>
                                <th class="px-8 py-6 hidden md:table-cell">Price</th>
                                <th class="px-8 py-6 text-center">Qty</th>
                                <th class="px-8 py-6">Total</th>
                                <th class="px-8 py-6"></th>
                            </tr>
                        </thead>

                        



                        <tbody class="divide-y divide-slate-50">
                            <?php
                            $total_belanja = 0;
                            $query = "SELECT keranjang.*, produk.nama_produk, produk.harga, produk.gambar FROM keranjang JOIN produk ON keranjang.id_produk = produk.id";
                            $ambil_keranjang = mysqli_query($conn, $query);
                            
                            if(mysqli_num_rows($ambil_keranjang) > 0):
                                while($item = mysqli_fetch_assoc($ambil_keranjang)):
                                    $subtotal = $item['harga'] * $item['jumlah'];
                                    $total_belanja += $subtotal;
                                    $foto = (strpos($item['gambar'], 'http') === 0) ? $item['gambar'] : 'img/'.$item['gambar'];
                            ?>
                            <tr class="cart-item-row group">
                                <td class="px-8 py-10">
                                    <div class="flex items-center gap-8">
                                        <div class="w-28 h-28 shrink-0 rounded-[2rem] overflow-hidden bg-slate-50 border border-slate-100 group-hover:shadow-xl transition-all duration-500">
                                            <img src="<?= $foto ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-500 text-[10px] uppercase tracking-widest mb-1">Authentic Gear</p>
                                            <h3 class="font-black text-slate-900 text-xl tracking-tighter italic uppercase leading-none mb-3"><?= htmlspecialchars($item['nama_produk']) ?></h3>
                                            <div class="flex gap-2">
                                                <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                                                <span class="w-3 h-3 rounded-full bg-slate-200"></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-10 font-semibold text-slate-400 hidden md:table-cell italic text-sm">
                                    Rp<?= number_format($item['harga'], 0, ',', '.') ?>
                                </td>
                                <td class="px-8 py-10 text-center">
                                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 text-slate-900 font-extrabold text-sm border border-slate-100">
                                        <?= $item['jumlah'] ?>
                                    </span>
                                </td>
                                <td class="px-8 py-10 font-black text-slate-900 text-xl tracking-tighter italic">
                                    Rp<?= number_format($subtotal, 0, ',', '.') ?>
                                </td>
                                <td class="px-8 py-10 text-right">
                                    <a href="keranjang.php?hapus=<?= $item['id_keranjang'] ?>" 
                                       class="w-10 h-10 inline-flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-300 hover:text-red-500 hover:border-red-100 hover:bg-red-50 transition-all active:scale-90"
                                       onclick="return confirm('Hapus item?')">
                                        <i class="fas fa-xmark text-sm"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="5" class="py-40 text-center">
                                    <div class="max-w-xs mx-auto">
                                        <div class="mb-6 relative inline-block">
                                            <div class="absolute inset-0 bg-blue-100 rounded-full blur-3xl opacity-50 animate-pulse"></div>
                                            <i class="fas fa-shopping-bag text-6xl text-slate-200 relative"></i>
                                        </div>
                                        <h3 class="text-xl font-black italic uppercase tracking-tighter mb-2">Keranjang Kosong</h3>
                                        <p class="text-slate-400 text-xs font-medium leading-relaxed mb-8 uppercase tracking-widest">Sepertinya Anda belum memilih jersey terbaik hari ini.</p>
                                        <a href="index.php" class="inline-block w-full bg-blue-600 text-white py-5 rounded-2xl font-black italic uppercase tracking-widest text-[10px] shadow-lg shadow-blue-200 hover:-translate-y-1 transition-all">Mulai Eksplorasi</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <aside class="flex-1">
            <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-[0_20px_50px_rgba(0,0,0,0.04)] checkout-card">
                <h3 class="text-xl font-black tracking-tighter italic uppercase mb-10 pb-6 border-b border-slate-50">Order <span class="text-blue-600">Summary</span></h3>
                
                <div class="space-y-5 mb-10">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Price</span>
                        <span class="font-bold italic text-slate-700">Rp<?= number_format($total_belanja, 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tax & Service</span>
                        <span class="text-[10px] font-black text-green-500 bg-green-50 px-3 py-1 rounded-full uppercase italic">Included</span>
                    </div>
                    <div class="flex justify-between items-center pt-5 border-t border-slate-50">
                        <div>
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-1">Grand Total</p>
                            <p class="text-4xl font-black tracking-tighter italic text-slate-900">
                                <span class="text-lg mr-1 text-slate-300 font-normal">Rp</span><?= number_format($total_belanja, 0, ',', '.') ?>
                            </p>
                        </div>
                    </div>
                </div>

                <button onclick="checkoutWA()" class="w-full bg-slate-900 text-white py-7 rounded-[2rem] font-[800] uppercase italic tracking-[0.15em] text-[11px] flex items-center justify-center gap-4 group hover:bg-blue-600 transition-all duration-500 shadow-xl active:scale-[0.97]">
                    Pay via WhatsApp
                    <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-2 transition-transform"></i>
                </button>

                <div class="mt-10 grid grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                        <i class="fas fa-truck-fast text-blue-600 mb-2"></i>
                        <p class="text-[9px] font-black uppercase tracking-tighter leading-none">Express<br>Shipping</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                        <i class="fas fa-rotate-left text-blue-600 mb-2"></i>
                        <p class="text-[9px] font-black uppercase tracking-tighter leading-none">Easy<br>Returns</p>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</main>

<footer class="py-20 text-center">
    <div class="inline-flex items-center gap-4 mb-6">
        <div class="h-[1px] w-8 bg-slate-200"></div>
        <i class="fas fa-star text-[10px] text-slate-200"></i>
        <div class="h-[1px] w-8 bg-slate-200"></div>
    </div>
    <p class="text-[10px] font-extrabold text-slate-300 uppercase tracking-[0.8em]">Jersey Home Official &bull; 2026</p>
</footer>

<script>
function checkoutWA() {
    const total = "<?= number_format($total_belanja, 0, ',', '.') ?>";
    if(total === "0") {
        alert("Pilih produk terlebih dahulu!");
        return;
    }

    const waNumber = "6281234567890";
    let text = "⚡ *NEW ORDER - JERSEY HOME*\n";
    text += "━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    <?php 
    if(mysqli_num_rows($ambil_keranjang) > 0):
        mysqli_data_seek($ambil_keranjang, 0); 
        while($item = mysqli_fetch_assoc($ambil_keranjang)): ?>
            text += "🏷️ *<?= strtoupper($item['nama_produk']) ?>*\n";
            text += "   Qty: <?= $item['jumlah'] ?> pcs • Rp<?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?>\n\n";
    <?php endwhile; endif; ?>

    text += "━━━━━━━━━━━━━━━━━━━━━━\n";
    text += "🔥 *GRAND TOTAL: Rp " + total + "*\n\n";
    text += "Halo Admin, saya ingin memproses pesanan di atas. Mohon info nomor rekening dan total ongkirnya.";
    
    window.open(`https://wa.me/${waNumber}?text=${encodeURIComponent(text)}`, '_blank');
}
</script>

</body>
</html>