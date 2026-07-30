<?php 
include 'header.php';

// Action: Tambah Kategori
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_kategori'];
    $admin = $_SESSION['admin'];
    
    $stmt_add_kat = $conn->prepare("INSERT INTO kategori (nama_kategori) VALUES (?)");
    $stmt_add_kat->bind_param("s", $nama);
    
    if ($stmt_add_kat->execute()) {
        mysqli_query($conn, "INSERT INTO log_aktivitas (admin, aksi) VALUES ('$admin', 'Menambah kategori baru: $nama')");
    }
    
    echo "<script>window.location.href='kategori.php';</script>";
}

// Action: Hapus Kategori
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $admin = $_SESSION['admin'];
    
    $stmt_get_kat = $conn->prepare("SELECT nama_kategori FROM kategori WHERE id = ?");
    $stmt_get_kat->bind_param("i", $id);
    $stmt_get_kat->execute();
    $kat = $stmt_get_kat->get_result()->fetch_assoc();
    
    if ($kat) {
        $nama_kat = $kat['nama_kategori'];
        
        // Pindahkan produk ke kategori 'Lainnya' jika kategori dihapus
        $cek_lainnya = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM kategori WHERE nama_kategori='Lainnya'"));
        if ($cek_lainnya == 0) {
            mysqli_query($conn, "INSERT INTO kategori (nama_kategori) VALUES ('Lainnya')");
        }
        
        $stmt_upd_prod = $conn->prepare("UPDATE produk SET kategori = 'Lainnya' WHERE kategori = ?");
        $stmt_upd_prod->bind_param("s", $nama_kat);
        $stmt_upd_prod->execute();
        
        $stmt_del_kat = $conn->prepare("DELETE FROM kategori WHERE id = ?");
        $stmt_del_kat->bind_param("i", $id);
        $stmt_del_kat->execute();
        
        mysqli_query($conn, "INSERT INTO log_aktivitas (admin, aksi) VALUES ('$admin', 'Menghapus kategori: $nama_kat')");
    }
    
    echo "<script>window.location.href='kategori.php';</script>";
}
?>

<!-- PAGE HEADER -->
<div class="mb-8">
    <h1 class="text-2xl font-extrabold text-[#1d2327] ">Kategori</h1>
    <p class="text-sm text-[#646970] font-medium mt-1">Kelola kategori produk untuk pengelompokan yang lebih baik</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
    
    <!-- LEFT: ADD CATEGORY FORM -->
    <div class="lg:col-span-1">
        <div class="wp-card">
            <div class="p-5 border-b border-[#dcdcde] bg-gray-50/50">
                <h3 class="text-xs font-black text-[#1d2327] uppercase  flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-[#2271b1]"></i>
                    Tambah Kategori
                </h3>
            </div>
            <div class="p-6">
                <form method="POST" class="space-y-5">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Nama Kategori</label>
                        <input type="text" name="nama_kategori" required placeholder="Contoh: Makanan, Fashion, dll"
                            class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all">
                        <p class="text-[10px] text-gray-400 italic ml-1 leading-relaxed">Nama kategori akan muncul di filter halaman katalog produk.</p>
                    </div>
                    <button type="submit" name="tambah" 
                        class="w-full bg-[#2271b1] text-white py-3 rounded-lg font-bold text-xs hover:bg-[#135e96] transition-all shadow-md shadow-blue-500/10 uppercase ">
                        Simpan Kategori
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- RIGHT: CATEGORY LIST -->
    <div class="lg:col-span-2">
        <div class="wp-card overflow-hidden">
            <div class="p-5 border-b border-[#dcdcde] bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-xs font-black text-[#1d2327] uppercase  flex items-center gap-2">
                    <i class="fa-solid fa-tags text-[#2271b1]"></i>
                    Daftar Kategori
                </h3>
                <?php
                $total_kat = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM kategori"));
                ?>
                <span class="text-[10px] font-black text-[#646970] bg-gray-200 px-2 py-0.5 rounded-full uppercase "><?= $total_kat ?> Kategori</span>
            </div>
            <div class="overflow-x-auto">
                <table class="wp-table">
                    <thead>
                        <tr>
                            <th class="w-16 text-center">ID</th>
                            <th>Nama Kategori</th>
                            <th class="text-center">Jumlah Produk</th>
                            <th class="text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($conn, "SELECT k.*, COUNT(p.id) as jumlah_produk FROM kategori k LEFT JOIN produk p ON p.kategori = k.nama_kategori GROUP BY k.id ORDER BY k.nama_kategori ASC");
                        while ($kat = mysqli_fetch_assoc($query)):
                        ?>
                        <tr>
                            <td class="text-center font-bold text-gray-300">#<?= str_pad($kat['id'], 3, '0', STR_PAD_LEFT) ?></td>
                            <td>
                                <div class="font-bold text-[#1d2327]"><?= $kat['nama_kategori'] ?></div>
                            </td>
                            <td class="text-center">
                                <span class="px-2.5 py-1 bg-gray-100 text-[#1d2327] rounded-full text-[10px] font-bold">
                                    <?= $kat['jumlah_produk'] ?> Produk
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if($kat['nama_kategori'] != 'Lainnya'): ?>
                                <a href="kategori.php?hapus=<?= $kat['id'] ?>" onclick="return confirm('Yakin hapus kategori ini? Semua produk dalam kategori ini akan dipindah ke kategori \'Lainnya\'')" 
                                    class="w-8 h-8 inline-flex items-center justify-center bg-red-50 text-red-500 rounded hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Hapus">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </a>
                                <?php else: ?>
                                <span class="text-[10px] text-gray-400 font-bold uppercase italic">Default</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>
