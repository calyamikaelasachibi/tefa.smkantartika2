<?php include 'header.php'; ?>

<!-- DASHBOARD HEADER -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-[#1d2327] ">Dashboard</h1>
        <p class="text-sm text-[#646970] font-medium mt-1">Ringkasan aktivitas dan manajemen produk Tefa Store</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="tambah.php" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#2271b1] text-white rounded font-bold text-xs hover:bg-[#135e96] transition-all shadow-sm uppercase ">
            <i class="fa-solid fa-plus"></i>
            Tambah Produk
        </a>
    </div>
</div>

<!-- STATS GRID -->
<?php
$total_produk = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM produk"));
$total_kategori = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM kategori"));
$stok_habis = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM produk WHERE stok = 0"));
$total_nilai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(harga * stok) as total FROM produk"))['total'];
?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <!-- Total Produk -->
    <div class="wp-card p-6 flex items-center gap-5 group hover:border-[#2271b1] transition-colors">
        <div class="w-14 h-14 bg-blue-50 text-[#2271b1] rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-box-open"></i>
        </div>
        <div>
            <div class="text-2xl font-black text-[#1d2327] leading-none mb-1"><?= number_format($total_produk, 0, ',', '.') ?></div>
            <div class="text-[10px] font-bold text-[#646970] uppercase ">Total Produk</div>
        </div>
    </div>

    <!-- Total Kategori -->
    <div class="wp-card p-6 flex items-center gap-5 group hover:border-[#2271b1] transition-colors">
        <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-tags"></i>
        </div>
        <div>
            <div class="text-2xl font-black text-[#1d2327] leading-none mb-1"><?= number_format($total_kategori, 0, ',', '.') ?></div>
            <div class="text-[10px] font-bold text-[#646970] uppercase ">Kategori</div>
        </div>
    </div>

    <!-- Stok Habis -->
    <div class="wp-card p-6 flex items-center gap-5 group hover:border-[#2271b1] transition-colors">
        <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
            <div class="text-2xl font-black text-[#1d2327] leading-none mb-1"><?= number_format($stok_habis, 0, ',', '.') ?></div>
            <div class="text-[10px] font-bold text-[#646970] uppercase ">Stok Habis</div>
        </div>
    </div>

    <!-- Total Nilai -->
    <div class="wp-card p-6 flex items-center gap-5 group hover:border-[#2271b1] transition-colors">
        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-sack-dollar"></i>
        </div>
        <div>
            <div class="text-xl font-black text-[#1d2327] leading-none mb-1">Rp <?= number_format($total_nilai, 0, ',', '.') ?></div>
            <div class="text-[10px] font-bold text-[#646970] uppercase ">Nilai Inventaris</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    
    <!-- LEFT COLUMN: PRODUCT LIST -->
    <div class="xl:col-span-2 space-y-8">
        <!-- FILTER & SEARCH BAR -->
        <div class="wp-card p-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <form method="GET" class="flex items-center gap-2 w-full md:w-auto">
                <select name="filter_kategori" class="bg-gray-50 border border-[#dcdcde] text-xs font-bold rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#2271b1]">
                    <option value="">Semua Kategori</option>
                    <?php
                    $kat_query = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                    while ($kat = mysqli_fetch_assoc($kat_query)) {
                        $selected = (isset($_GET['filter_kategori']) && $_GET['filter_kategori'] == $kat['nama_kategori']) ? 'selected' : '';
                        echo '<option value="' . $kat['nama_kategori'] . '" ' . $selected . '>' . $kat['nama_kategori'] . '</option>';
                    }
                    ?>
                </select>
                <div class="relative flex-1 md:w-64">
                    <input type="text" name="search" placeholder="Cari produk..." value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>"
                        class="w-full bg-gray-50 border border-[#dcdcde] text-xs font-medium rounded pl-8 pr-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#2271b1]">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]"></i>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-200 text-[#2c3338] rounded font-bold text-[10px] hover:bg-gray-300 transition-all uppercase ">Filter</button>
            </form>
            
            <?php if(isset($_GET['search']) || isset($_GET['filter_kategori'])): ?>
                <a href="index.php" class="text-[10px] font-bold text-[#2271b1] hover:underline uppercase ">Reset Filter</a>
            <?php endif; ?>
        </div>

        <!-- PRODUCT TABLE -->
        <div class="wp-card overflow-hidden">
            <div class="p-5 border-b border-[#dcdcde] bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-[#1d2327] uppercase  flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-[#2271b1]"></i>
                    Daftar Produk
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="wp-table">
                    <thead>
                        <tr>
                            <th class="w-12 text-center">No</th>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Kategori</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $search = isset($_GET['search']) ? $_GET['search'] : '';
                        $filter_kategori = isset($_GET['filter_kategori']) ? $_GET['filter_kategori'] : '';

                        $limit = 10; // jumlah data per halaman
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $page = max($page, 1);
                        $offset = ($page - 1) * $limit;

                        $sql = "SELECT * FROM produk WHERE 1=1";
                        $params = [];
                        $types = "";

                        if ($search) {
                            $sql .= " AND nama_produk LIKE ?";
                            $params[] = "%$search%";
                            $types .= "s";
                        }
                        if ($filter_kategori) {
                            $sql .= " AND kategori = ?";
                            $params[] = $filter_kategori;
                            $types .= "s";
                        }
                        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
                        $params[] = $limit;
                        $params[] = $offset;
                        $types .= "ii";

                        $count_sql = "SELECT COUNT(*) as total FROM produk WHERE 1=1";
                        
                        $count_params = [];
                        $count_types = "";
                        
                        if ($search) {
                            $count_sql .= " AND nama_produk LIKE ?";
                            $count_params[] = "%$search%";
                            $count_types .= "s";
                        }
                        if ($filter_kategori) {
                            $count_sql .= " AND kategori = ?";
                            $count_params[] = $filter_kategori;
                            $count_types .= "s";
                        }
                        
                        $stmt_count = $conn->prepare($count_sql);
                        if (!empty($count_params)) {
                            $stmt_count->bind_param($count_types, ...$count_params);
                        }
                        $stmt_count->execute();
                        $total_data = $stmt_count->get_result()->fetch_assoc()['total'];
                        
                        $total_pages = ceil($total_data / $limit);

                        $stmt_list = $conn->prepare($sql);
                        if (!empty($params)) {
                            $stmt_list->bind_param($types, ...$params);
                        }
                        $stmt_list->execute();
                        $query = $stmt_list->get_result();
                        
                        $no = 1;
                        while ($produk = $query->fetch_assoc()):
                            $harga = number_format($produk['harga'], 0, ',', '.');
                            $gambar = $produk['gambar'] ? '../assets/images/' . $produk['gambar'] : 'https://via.placeholder.com/50';
                            $stok_status = $produk['stok'] <= 5 ? 'text-red-500 font-bold' : 'text-[#50575e]';
                        ?>
                        <tr>
                            <td class="text-center font-bold text-gray-300"><?= $no++ ?></td>
                            <td>
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded border border-gray-100 overflow-hidden bg-gray-50 flex-shrink-0">
                                        <img src="<?= $gambar ?>" alt="Product" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <div class="font-bold text-[#1d2327] leading-tight mb-0.5"><?= $produk['nama_produk'] ?></div>
                                        <div class="text-[10px] text-gray-400 font-medium">ID: #<?= str_pad($produk['id'], 4, '0', STR_PAD_LEFT) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="font-bold text-[#1d2327]">Rp <?= $harga ?></td>
                            <td class="<?= $stok_status ?>">
                                <?= $produk['stok'] > 0 ? $produk['stok'] . ' pcs' : '<span class="px-2 py-0.5 bg-red-100 text-red-600 rounded text-[10px] font-black uppercase ">Habis</span>' ?>
                            </td>
                            <td>
                                <span class="px-2 py-1 bg-blue-50 text-[#2271b1] rounded-md text-[10px] font-bold uppercase ">
                                    <?= $produk['kategori'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-2">
                                    <a href="edit.php?id=<?= $produk['id'] ?>" class="w-8 h-8 flex items-center justify-center bg-gray-100 text-[#1d2327] rounded hover:bg-[#2271b1] hover:text-white transition-all shadow-sm" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <a href="hapus.php?id=<?= $produk['id'] ?>" onclick="return confirm('Yakin hapus produk ini?')" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-500 rounded hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Hapus">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div class="p-4 flex justify-between items-center text-xs font-bold">
                    <div>
                        Halaman <?= $page ?> dari <?= $total_pages ?>
                    </div>
                    <div class="flex gap-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page-1 ?>&search=<?= $search ?>&filter_kategori=<?= $filter_kategori ?>" 
                               class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Prev</a>
                        <?php endif; ?>
                
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?= $i ?>&search=<?= $search ?>&filter_kategori=<?= $filter_kategori ?>" 
                               class="px-3 py-1 rounded <?= $i == $page ? 'bg-[#2271b1] text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
                               <?= $i ?>
                            </a>
                        <?php endfor; ?>
                
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page+1 ?>&search=<?= $search ?>&filter_kategori=<?= $filter_kategori ?>" 
                               class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Next</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: ALERTS & LOGS -->
    <div class="space-y-8">
        <!-- CRITICAL STOCK ALERT -->
        <?php 
        $stok_menipis_query = mysqli_query($conn, "SELECT * FROM produk WHERE stok > 0 AND stok <= 5 ORDER BY stok ASC LIMIT 5");
        $stok_menipis_total = mysqli_num_rows($stok_menipis_query);
        if ($stok_menipis_total > 0): 
        ?>
        <div class="wp-card border-l-4 border-red-500">
            <div class="p-4 border-b border-[#dcdcde] bg-red-50/30 flex items-center justify-between">
                <h3 class="text-xs font-black text-red-600 uppercase  flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation animate-pulse"></i>
                    Stok Menipis
                </h3>
                <span class="bg-red-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full"><?= $stok_menipis_total ?></span>
            </div>
            <div class="p-4 space-y-4">
                <?php while ($item = mysqli_fetch_assoc($stok_menipis_query)): ?>
                <div class="flex items-center justify-between gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg overflow-hidden border border-gray-100">
                            <img src="<?= $item['gambar'] ? '../assets/images/' . $item['gambar'] : 'https://via.placeholder.com/40' ?>" alt="P" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <div class="text-xs font-bold text-[#1d2327] leading-tight"><?= $item['nama_produk'] ?></div>
                            <div class="text-[10px] text-red-500 font-bold mt-1">Sisa <?= $item['stok'] ?> pcs</div>
                        </div>
                    </div>
                    <a href="edit.php?id=<?= $item['id'] ?>" class="text-[10px] font-black text-[#2271b1] hover:underline uppercase ">Restok</a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>

<?php include 'footer.php'; ?>
