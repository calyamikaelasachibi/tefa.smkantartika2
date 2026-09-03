<?php
include 'includes/koneksi.php';
$base_url = '';

$f_kat = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$f_search = isset($_GET['search']) ? $_GET['search'] : '';
$f_min = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$f_max = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;
$f_sort = isset($_GET['sort']) ? $_GET['sort'] : 'terbaru';

$per_halaman = 20;
$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset = ($halaman_aktif - 1) * $per_halaman;

$sql_base = "SELECT p.*, (SELECT AVG(bintang) FROM ulasan WHERE produk_id = p.id) as rata_rating FROM produk p WHERE 1=1";
$params_kat = [];
$types_kat = "";

if ($f_kat) {
    $sql_base .= " AND p.kategori = ?";
    $params_kat[] = $f_kat;
    $types_kat .= "s";
}
if ($f_search) {
    $sql_base .= " AND p.nama_produk LIKE ?";
    $params_kat[] = "%$f_search%";
    $types_kat .= "s";
}
if ($f_min > 0) {
    $sql_base .= " AND p.harga >= ?";
    $params_kat[] = $f_min;
    $types_kat .= "i";
}
if ($f_max > 0) {
    $sql_base .= " AND p.harga <= ?";
    $params_kat[] = $f_max;
    $types_kat .= "i";
}

$order_clause = "ORDER BY p.created_at DESC";
if ($f_sort == 'terbaru') $order_clause = "ORDER BY p.created_at DESC";
elseif ($f_sort == 'terlaris') $order_clause = "ORDER BY p.terjual DESC";
elseif ($f_sort == 'harga_asc') $order_clause = "ORDER BY p.harga ASC";
elseif ($f_sort == 'harga_desc') $order_clause = "ORDER BY p.harga DESC";

$stmt_total = $conn->prepare($sql_base);
if (!empty($params_kat)) {
    $stmt_total->bind_param($types_kat, ...$params_kat);
}
$stmt_total->execute();
$total_items = $stmt_total->get_result()->num_rows;
$total_halaman = ceil($total_items / $per_halaman);

$sql_main = $sql_base . " $order_clause LIMIT ? OFFSET ?";
$params_kat[] = $per_halaman;
$params_kat[] = $offset;
$types_kat .= "ii";

$stmt_main = $conn->prepare($sql_main);
$stmt_main->bind_param($types_kat, ...$params_kat);
$stmt_main->execute();
$main_query = $stmt_main->get_result();

if (!function_exists('filterUrl')) {
    function filterUrl($params)
    {
        $current = $_GET;
        foreach ($params as $key => $val) {
            if ($val === null) unset($current[$key]);
            else $current[$key] = $val;
        }
        return 'katalog.php?' . http_build_query($current);
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpeg" href="assets/images/logo-smk-antartika-2-sidoarjo.jpg">
    <title>Katalog Produk - Tefa Store Kelompok keren</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-white">

    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sticky-navbar.php'; ?>

    <main class="py-16">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-12">

                <aside class="hidden lg:block w-64 flex-shrink-0">
                    <form action="katalog.php" method="GET" class="space-y-10 sticky top-24">
                        <div>
                            <h3 class="text-sm font-bold text-[#002147] uppercase mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-magnifying-glass text-xs text-gray-400"></i>
                                Cari Produk
                            </h3>
                            <div class="relative">
                                <input type="text" name="search" placeholder="Cari..." value="<?= htmlspecialchars($f_search) ?>"
                                    class="w-full text-sm p-3 border border-gray-200 rounded-sm focus:outline-none focus:border-[#FFB606] transition-all">
                                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#FFB606]">
                                    <i class="fa-solid fa-search text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-[#002147] uppercase mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-list text-xs text-gray-400"></i>
                                Kategori
                            </h3>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="kategori" value="" <?= !$f_kat ? 'checked' : '' ?>
                                        onchange="this.form.submit()"
                                        class="w-4 h-4 border-gray-300 text-[#FFB606] focus:ring-[#FFB606]">
                                    <span class="text-sm text-gray-600 group-hover:text-[#FFB606] transition-colors">Semua Kategori</span>
                                </label>
                                <?php
                                $all_categories = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                                while ($cat = mysqli_fetch_assoc($all_categories)):
                                    $is_checked = ($f_kat == $cat['nama_kategori']) ? 'checked' : '';
                                ?>
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" name="kategori" value="<?= $cat['nama_kategori'] ?>" <?= $is_checked ?>
                                            onchange="this.form.submit()"
                                            class="w-4 h-4 border-gray-300 text-[#FFB606] focus:ring-[#FFB606]">
                                        <span class="text-sm text-gray-600 group-hover:text-[#FFB606] transition-colors"><?= $cat['nama_kategori'] ?></span>
                                    </label>
                                <?php endwhile; ?>
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        <div>
                            <h3 class="text-sm font-bold text-[#002147] uppercase mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-arrow-down-wide-short text-xs text-gray-400"></i>
                                Urutkan
                            </h3>
                            <select name="sort" onchange="this.form.submit()" class="w-full text-sm p-3 border border-gray-200 rounded-sm focus:outline-none focus:border-[#FFB606] appearance-none bg-white">
                                <option value="terbaru" <?= $f_sort == 'terbaru' ? 'selected' : '' ?>>Terbaru</option>
                                <option value="terlaris" <?= $f_sort == 'terlaris' ? 'selected' : '' ?>>Terlaris</option>
                                <option value="harga_asc" <?= $f_sort == 'harga_asc' ? 'selected' : '' ?>>Harga: Rendah ke Tinggi</option>
                                <option value="harga_desc" <?= $f_sort == 'harga_desc' ? 'selected' : '' ?>>Harga: Tinggi ke Rendah</option>
                            </select>
                        </div>

                        <hr class="border-gray-100">

                        <div>
                            <h3 class="text-sm font-bold text-[#002147] uppercase mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-tags text-xs text-gray-400"></i>
                                Rentang Harga
                            </h3>
                            <div class="space-y-4">
                                <div class="flex items-center gap-2">
                                    <input type="number" name="min_price" placeholder="Min" value="<?= $f_min > 0 ? $f_min : '' ?>"
                                        class="w-full text-xs p-3 border border-gray-200 rounded-sm focus:outline-none focus:border-[#FFB606]">
                                    <div class="w-2 h-[1px] bg-gray-300 flex-shrink-0"></div>
                                    <input type="number" name="max_price" placeholder="Max" value="<?= $f_max > 0 ? $f_max : '' ?>"
                                        class="w-full text-xs p-3 border border-gray-200 rounded-sm focus:outline-none focus:border-[#FFB606]">
                                </div>
                                <button type="submit" class="w-full bg-[#002147] text-white py-3 rounded-sm text-xs font-bold hover:bg-[#FFB606] hover:text-[#002147] transition-all uppercase shadow-sm">
                                    Terapkan Filter
                                </button>
                            </div>
                        </div>

                        <a href="katalog.php" class="block w-full text-center text-gray-400 py-2 text-xs font-bold hover:text-[#002147] transition-colors uppercase mt-4 underline decoration-gray-200 underline-offset-4">
                            Hapus Semua Filter
                        </a>
                    </form>
                </aside>

                <div class="flex-1">
                    <div class="lg:hidden mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-sm font-bold text-[#002147] uppercase">
                                <?= $f_kat ? $f_kat : 'Semua Produk' ?>
                                <span class="text-[10px] font-normal text-gray-400 ml-1">(<?= $total_items ?>)</span>
                            </h2>
                            <button onclick="document.getElementById('mobile-filter').classList.remove('translate-x-full')" class="text-xs font-bold text-gray-500/50 flex items-center gap-2 bg-gray-100 px-3 py-1.5 rounded-sm border border-gray-200">
                                <i class="fa-solid fa-sliders"></i>
                                Filter
                            </button>
                        </div>
                        <div class="overflow-x-auto no-scrollbar pb-2">
                            <div class="flex gap-2 whitespace-nowrap">
                                <a href="katalog.php" class="px-5 py-2 <?= !$f_kat ? 'bg-[#FFB606] text-white' : 'bg-gray-50 text-gray-500 border border-gray-100' ?> rounded-full text-xs font-medium transition-all">Semua</a>
                                <?php
                                $km_q = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                                while ($km = mysqli_fetch_assoc($km_q)):
                                    $is_active = ($f_kat == $km['nama_kategori']);
                                ?>
                                    <a href="katalog.php?kategori=<?= urlencode($km['nama_kategori']) ?>" class="px-5 py-2 <?= $is_active ? 'bg-[#FFB606] text-white' : 'bg-gray-50 text-gray-500 border border-gray-100' ?> rounded-full text-xs font-medium transition-all">
                                        <?= $km['nama_kategori'] ?>
                                    </a>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>

                    <div class="hidden lg:flex items-center justify-between mb-10 border-b border-gray-100 pb-6">
                        <h2 class="text-lg font-bold text-[#002147] uppercase">
                            <?= $f_kat ? $f_kat : 'Semua Produk' ?>
                            <span class="text-xs font-normal text-gray-400 ml-2">(<?= $total_items ?> produk ditemukan)</span>
                        </h2>
                        <div class="flex items-center gap-4">
                            <span class="text-xs text-gray-400">Tampilkan per halaman: 20</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-8">
                        <?php
                        if ($total_items > 0):
                            while ($p = mysqli_fetch_assoc($main_query)):
                                $harga_p = number_format($p['harga'], 0, ',', '.');
                                $img_p = $p['gambar'] ? 'assets/images/' . $p['gambar'] : 'https://placehold.co/400x400?text=No+Image';
                                $rating_p = $p['rata_rating'] ? round($p['rata_rating'], 1) : '0';
                        ?>
                                <a href="detail.php?id=<?= $p['id'] ?>" class="group flex flex-col h-full bg-white border border-gray-100 hover:border-[#FFB606] transition-all duration-300 rounded-sm overflow-hidden hover:shadow-xl">
                                    <div class="relative aspect-square overflow-hidden bg-gray-50">
                                        <img src="<?= $img_p ?>" alt="<?= $p['nama_produk'] ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        <?php if ($p['stok'] <= 0): ?>
                                            <div class="absolute inset-0 bg-white/60 flex items-center justify-center">
                                                <span class="bg-gray-900 text-white text-[10px] font-bold px-3 py-1 rounded-sm uppercase">Stok Habis</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="p-4 flex flex-col flex-1">
                                        <h3 class="text-xs md:text-sm text-gray-800 line-clamp-2 mb-3 font-medium h-10">
                                            <?= $p['nama_produk'] ?>
                                        </h3>

                                        <div class="mt-auto">
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-[10px] font-bold text-black uppercase">Rp</span>
                                                <span class="text-lg font-semibold text-black"><?= $harga_p ?></span>
                                            </div>

                                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
                                                <div class="flex items-center gap-1">
                                                    <i class="fa-solid fa-star text-[10px] text-yellow-400"></i>
                                                    <span class="text-[10px] font-bold text-gray-700"><?= $rating_p ?></span>
                                                    <span class="text-[10px] text-gray-400 mx-1">|</span>
                                                    <span class="text-[10px] text-gray-500"><?= $p['terjual'] ?> terjual</span>
                                                </div>
                                                <span class="text-[10px] text-gray-400 flex items-center gap-1">
                                                    <i class="fa-solid fa-location-dot text-[8px]"></i>
                                                    Sidoarjo
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-span-full py-32 text-center">
                                <h3 class="text-xl font-bold text-[#002147] uppercase">Produk Tidak Ditemukan</h3>
                                <p class="text-gray-500 text-sm mt-3 max-w-xs mx-auto">Maaf, kami tidak dapat menemukan produk yang sesuai dengan kriteria Anda.</p>
                                <a href="katalog.php" class="inline-block mt-8 px-10 py-3 bg-[#002147] text-white text-xs font-bold rounded-sm hover:bg-[#FFB606] hover:text-[#002147] transition-all uppercase shadow-md">
                                    Reset Filter
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($total_halaman > 1): ?>
                        <div class="mt-20 flex justify-center items-center gap-3">
                            <?php if ($halaman_aktif > 1): ?>
                                <a href="<?= filterUrl(['halaman' => $halaman_aktif - 1]) ?>" class="w-10 h-10 flex items-center justify-center rounded-sm border border-gray-200 text-gray-400 hover:border-[#FFB606] hover:text-[#FFB606] transition-all">
                                    <i class="fa-solid fa-chevron-left text-xs"></i>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                                <a href="<?= filterUrl(['halaman' => $i]) ?>" class="w-10 h-10 flex items-center justify-center rounded-sm border <?= $halaman_aktif == $i ? 'bg-[#002147] border-[#002147] text-white font-bold' : 'border-gray-200 text-gray-500 hover:border-[#FFB606] hover:text-[#FFB606]' ?> transition-all text-sm">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($halaman_aktif < $total_halaman): ?>
                                <a href="<?= filterUrl(['halaman' => $halaman_aktif + 1]) ?>" class="w-10 h-10 flex items-center justify-center rounded-sm border border-gray-200 text-gray-400 hover:border-[#FFB606] hover:text-[#FFB606] transition-all">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </main>

    <div id="mobile-filter" class="fixed inset-0 z-50 transform translate-x-full  lg:hidden">
        <div class="absolute inset-0 bg-black/50" onclick="document.getElementById('mobile-filter').classList.add('translate-x-full')"></div>
        <div class="absolute right-0 top-0 bottom-0 w-80 bg-white shadow-2xl p-6 overflow-y-auto">
            <div class="flex items-center justify-between mb-8 border-b border-gray-100 pb-4">
                <h3 class="font-bold text-[#002147] uppercase">Filter Produk</h3>
                <button onclick="document.getElementById('mobile-filter').classList.add('translate-x-full')" class="text-gray-400 hover:text-black">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form action="katalog.php" method="GET" class="space-y-10">
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase mb-4">Urutkan</h4>
                    <select name="sort" class="w-full p-3 border border-gray-100 rounded-sm text-sm focus:outline-none">
                        <option value="terbaru" <?= $f_sort == 'terbaru' ? 'selected' : '' ?>>Terbaru</option>
                        <option value="terlaris" <?= $f_sort == 'terlaris' ? 'selected' : '' ?>>Terlaris</option>
                        <option value="harga_asc" <?= $f_sort == 'harga_asc' ? 'selected' : '' ?>>Harga Terendah</option>
                        <option value="harga_desc" <?= $f_sort == 'harga_desc' ? 'selected' : '' ?>>Harga Tertinggi</option>
                    </select>
                </div>

                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase mb-4">Rentang Harga</h4>
                    <div class="flex items-center gap-3">
                        <input type="number" name="min_price" placeholder="Min" value="<?= $f_min > 0 ? $f_min : '' ?>" class="w-full p-3 border border-gray-100 rounded-sm text-sm">
                        <span class="text-gray-300">-</span>
                        <input type="number" name="max_price" placeholder="Max" value="<?= $f_max > 0 ? $f_max : '' ?>" class="w-full p-3 border border-gray-100 rounded-sm text-sm">
                    </div>
                </div>

                <div class="pt-10">
                    <button type="submit" class="w-full bg-[#002147] text-white py-4 rounded-sm font-bold uppercase text-xs shadow-lg">Terapkan</button>
                    <a href="katalog.php" class="block w-full text-center text-gray-400 mt-6 text-[10px] font-bold uppercase">Reset Semua</a>
                </div>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

</body>

</html>
