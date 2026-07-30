<?php 
include 'header.php';

if (isset($_GET['hapus'])) {
    $id_ulasan = mysqli_real_escape_string($conn, $_GET['hapus']);
    $admin = $_SESSION['admin'];
    
    // Get info for log
    $info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama FROM ulasan WHERE id='$id_ulasan'"));
    $nama_pengulas = $info['nama'];
    
    mysqli_query($conn, "DELETE FROM ulasan WHERE id='$id_ulasan'");
    mysqli_query($conn, "INSERT INTO log_aktivitas (admin, aksi) VALUES ('$admin', 'Menghapus ulasan dari: $nama_pengulas')");
    
    echo "<script>window.location.href='ulasan.php';</script>";
    exit();
}

$ulasan_query = mysqli_query($conn, "SELECT u.*, p.nama_produk, p.gambar FROM ulasan u JOIN produk p ON p.id = u.produk_id ORDER BY u.waktu DESC");
$total = mysqli_num_rows($ulasan_query);
?>

<!-- PAGE HEADER -->
<div class="mb-8">
    <h1 class="text-2xl font-extrabold text-[#1d2327] ">Ulasan Pelanggan</h1>
    <p class="text-sm text-[#646970] font-medium mt-1">Pantau dan kelola feedback dari pembeli produk Tefa Store</p>
</div>

<!-- STATS SUMMARY -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="wp-card p-6 flex items-center gap-5">
        <div class="w-12 h-12 bg-yellow-50 text-yellow-500 rounded-xl flex items-center justify-center text-xl">
            <i class="fa-solid fa-star"></i>
        </div>
        <div>
            <div class="text-xl font-black text-[#1d2327] leading-none mb-1"><?= number_format($total, 0, ',', '.') ?></div>
            <div class="text-[10px] font-bold text-[#646970] uppercase ">Total Ulasan</div>
        </div>
    </div>
</div>

<!-- REVIEWS LIST -->
<div class="space-y-6">
    <?php if ($total > 0): ?>
        <?php while ($ul = mysqli_fetch_assoc($ulasan_query)): ?>
        <div class="wp-card group hover:border-[#2271b1] transition-all">
            <div class="p-6 flex flex-col md:flex-row gap-6">
                <!-- Product Thumbnail -->
                <div class="w-20 h-20 rounded-lg overflow-hidden border border-gray-100 bg-gray-50 flex-shrink-0">
                    <img src="<?= $ul['gambar'] ? '../assets/images/' . $ul['gambar'] : 'https://via.placeholder.com/80' ?>" alt="P" class="w-full h-full object-cover">
                </div>

                <!-- Review Content -->
                <div class="flex-1 space-y-3">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-2">
                        <div>
                            <h4 class="text-sm font-bold text-[#1d2327] hover:text-[#2271b1] transition-colors"><?= $ul['nama_produk'] ?></h4>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[11px] font-bold text-[#646970]">Oleh: <span class="text-[#1d2327]"><?= htmlspecialchars($ul['nama']) ?></span></span>
                                <span class="text-gray-300">•</span>
                                <span class="text-[10px] text-gray-400 font-medium italic"><?= date('d M Y, H:i', strtotime($ul['waktu'])) ?></span>
                            </div>
                        </div>
                        
                        <!-- Rating Stars -->
                        <div class="flex items-center gap-0.5 text-xs text-yellow-400">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <i class="fa-<?= $i <= $ul['bintang'] ? 'solid' : 'regular' ?> fa-star"></i>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="bg-gray-50/80 p-4 rounded-xl border border-gray-100 italic text-sm text-[#50575e] leading-relaxed">
                        "<?= htmlspecialchars($ul['komentar']) ?>"
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex md:flex-col items-center justify-center gap-2 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6">
                    <a href="ulasan.php?hapus=<?= $ul['id'] ?>" onclick="return confirm('Yakin hapus ulasan ini?')" 
                        class="flex items-center gap-2 px-4 py-2 bg-red-50 text-red-500 rounded-lg font-bold text-[10px] hover:bg-red-500 hover:text-white transition-all uppercase  group/del">
                        <i class="fa-solid fa-trash-can group-hover/del:animate-bounce"></i>
                        Hapus
                    </a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="wp-card p-20 flex flex-col items-center justify-center text-center space-y-4">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-200 text-4xl">
                <i class="fa-solid fa-comment-slash"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-[#1d2327]">Belum ada ulasan</h3>
                <p class="text-sm text-[#646970]">Ulasan dari pembeli akan muncul di sini.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
