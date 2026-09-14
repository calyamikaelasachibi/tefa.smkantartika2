<?php
include 'includes/koneksi.php';
$base_url = '';

$unggulan_query = mysqli_query($conn, "SELECT * FROM produk WHERE stok > 0 ORDER BY terjual DESC LIMIT 4");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpeg" href="assets/images/logo-smk-antartika-2-sidoarjo.jpg">
    <title>Tefa Store - SMKS Antartika 2 Kelompok Keren</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Sembunyikan scrollbar untuk tampilan mobile tab horizontal */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sticky-navbar.php'; ?>

    <!-- 1. SECTION REKOMENDASI SPESIAL (3 KOLOM MOBILE / 5 KOLOM DESKTOP) -->
    <section class="bg-white py-6 border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-3 relative">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <span class="text-[10px] font-bold text-[#FFB606] tracking-widest uppercase block mb-0.5">Pilihan Terbaik</span>
                    <h2 class="text-base md:text-xl font-bold text-[#002147]">Rekomendasi Spesial</h2>
                    <p class="text-gray-400 text-[10px] md:text-xs">Produk pilihan dengan rating dan penjualan terbaik</p>
                </div>
                <a href="katalog.php" class="text-[10px] md:text-xs font-bold text-gray-700 hover:text-[#002147] transition-colors flex items-center gap-1">
                    LIHAT SEMUA <i class="fa-solid fa-arrow-right text-[9px]"></i>
                </a>
            </div>

            <!-- Grid 3 Kolom Mobile & 5 Kolom Desktop -->
            <div class="grid grid-cols-3 md:grid-cols-5 gap-1.5 md:gap-4">
                <?php
                $random_products = mysqli_query($conn, "
                    SELECT p.*, 
                    (SELECT AVG(bintang) FROM ulasan WHERE produk_id = p.id) as rata_rating
                    FROM produk p 
                    WHERE p.stok > 0 
                    ORDER BY RAND() 
                    LIMIT 15
                ");

                while ($rp = mysqli_fetch_assoc($random_products)):
                    $img_rp = $rp['gambar'] ? 'assets/images/' . $rp['gambar'] : 'https://placehold.co/300x300?text=No+Image';
                ?>
                    <a href="detail.php?id=<?= $rp['id'] ?>" class="group block bg-white border border-gray-100 hover:shadow-md transition-all duration-200 rounded-sm overflow-hidden p-1.5 md:p-3 flex flex-col items-center text-center">
                        <div class="w-full aspect-square overflow-hidden mb-1.5 flex items-center justify-center bg-gray-50 rounded-sm">
                            <img src="<?= $img_rp ?>" alt="<?= htmlspecialchars($rp['nama_produk']) ?>" class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <h3 class="text-[10px] md:text-xs font-medium text-gray-800 line-clamp-2 leading-tight">
                            <?= htmlspecialchars($rp['nama_produk']) ?>
                        </h3>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- 2. SECTION PRODUK BARU DITAMBAHKAN (HORIZONTAL DI MOBILE, 3 KOLOM DI DESKTOP) -->
    <section class="bg-slate-100/70 py-8 px-4 border-b border-gray-200">
        <div class="max-w-6xl mx-auto">
            <div class="mb-6">
                <span class="text-xs font-bold text-[#FFB606] uppercase tracking-wider block mb-1">TERBARU</span>
                <h2 class="text-xl md:text-2xl font-bold text-[#002147]">Produk Baru Ditambahkan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php
                $query_terbaru = mysqli_query($conn, "SELECT * FROM produk WHERE stok > 0 ORDER BY id DESC LIMIT 3");
                while ($p_baru = mysqli_fetch_assoc($query_terbaru)):
                    $gambar_baru = !empty($p_baru['gambar']) ? 'assets/images/' . $p_baru['gambar'] : 'https://placehold.co/100x100?text=No+Image';
                    $deskripsi_baru = !empty(trim($p_baru['deskripsi'])) ? strip_tags($p_baru['deskripsi']) : 'Produk unggulan terbaru dari toko kami.';
                ?>
                    <div class="bg-white rounded-lg p-3 md:p-4 shadow-sm flex items-center gap-3 md:gap-4 transition hover:shadow-md border border-gray-100">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 flex-shrink-0 bg-gray-50 rounded-md overflow-hidden flex items-center justify-center border border-gray-100">
                            <img src="<?= $gambar_baru ?>" alt="<?= htmlspecialchars($p_baru['nama_produk']) ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0 flex flex-col justify-between py-0.5">
                            <div>
                                <h3 class="text-sm md:text-base font-bold text-[#002147] truncate mb-0.5 md:mb-1">
                                    <?= htmlspecialchars($p_baru['nama_produk']) ?>
                                </h3>
                                <p class="text-[11px] md:text-xs text-gray-400 line-clamp-2 mb-2 md:mb-3">
                                    <?= htmlspecialchars($deskripsi_baru) ?>
                                </p>
                            </div>
                            <div>
                                <a href="detail.php?id=<?= $p_baru['id'] ?>" class="inline-flex items-center gap-1 bg-[#FFB606] hover:bg-yellow-500 text-[#002147] font-bold text-[10px] md:text-xs py-1.5 px-3 md:py-2 md:px-4 rounded-md transition uppercase shadow-sm">
                                    LIHAT &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- 3. BANNER PROMO BIRU TEFA STORE -->
    <section class="bg-[#002147] text-white py-12 px-4 text-center">
        <div class="max-w-4xl mx-auto">
            <span class="text-[#FFB606] text-xs font-bold tracking-widest uppercase block mb-2">TEFA STORE</span>
            <h2 class="text-xl md:text-3xl font-extrabold mb-3 leading-tight">
                Temukan Produk Terbaik Untuk Kebutuhan Anda
            </h2>
            <p class="text-xs md:text-sm text-gray-300 mb-6">
                Koleksi Terlengkap & Kualitas Terjamin &mdash; Karya Siswa SMKS Antartika 2 Sidoarjo
            </p>
            <a href="katalog.php" class="inline-block bg-[#FFB606] hover:bg-yellow-500 text-[#002147] font-bold text-xs md:text-sm py-3 px-8 rounded-md transition shadow-md uppercase">
                LIHAT SEMUA PRODUK &rarr;
            </a>
        </div>
    </section>

    <!-- 4. PRODUK PILIHAN / PILIHAN EDITOR -->
    <section class="bg-white py-10 border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4">
            <div class="mb-6">
                <span class="text-[10px] font-bold text-[#FFB606] tracking-widest uppercase block mb-1">PILIHAN EDITOR</span>
                <h2 class="text-lg md:text-xl font-bold text-[#002147]">Produk Pilihan</h2>
            </div>

            <div class="grid grid-cols-3 gap-3 md:gap-6 text-center">
                <?php
                $featured_cta = mysqli_query($conn, "SELECT * FROM produk WHERE stok > 0 ORDER BY RAND() LIMIT 3");
                while ($fc = mysqli_fetch_assoc($featured_cta)):
                    $img_fc = $fc['gambar'] ? 'assets/images/' . $fc['gambar'] : 'https://placehold.co/200x300?text=Product';
                ?>
                    <a href="detail.php?id=<?= $fc['id'] ?>" class="group block bg-white border border-gray-100 hover:border-[#FFB606] rounded-sm overflow-hidden p-2 transition">
                        <div class="w-full aspect-square mb-2 flex items-center justify-center overflow-hidden bg-gray-50 rounded-sm">
                            <img src="<?= $img_fc ?>" alt="<?= htmlspecialchars($fc['nama_produk']) ?>" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <h3 class="text-xs font-bold text-[#002147] group-hover:text-[#FFB606] transition-colors truncate"><?= htmlspecialchars($fc['nama_produk']) ?></h3>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- 5. KATEGORI TERPOPULER (4 ITEM DENGAN GRID 4 KOLOM) -->
    <section class="bg-gray-50 py-10">
        <div class="max-w-6xl mx-auto px-4">
            <div class="mb-8 text-left">
                <span class="text-[10px] font-bold text-[#FFB606] tracking-widest uppercase block mb-1">JELAJAHI</span>
                <h2 class="text-lg md:text-xl font-bold text-[#002147]">Kategori Terpopuler</h2>
            </div>

            <div class="grid grid-cols-4 gap-2 md:gap-6 max-w-2xl">
                <?php
                $pop_cats = mysqli_query($conn, "
                    SELECT k.nama_kategori, 
                    (SELECT gambar FROM produk WHERE kategori = k.nama_kategori AND gambar != '' ORDER BY RAND() LIMIT 1) as gambar_produk
                    FROM kategori k 
                    ORDER BY (SELECT COUNT(id) FROM produk WHERE kategori = k.nama_kategori) DESC 
                    LIMIT 3
                ");
                while ($pc = mysqli_fetch_assoc($pop_cats)):
                    $img_pc = $pc['gambar_produk'] ? 'assets/images/' . $pc['gambar_produk'] : 'https://placehold.co/200x200?text=' . urlencode($pc['nama_kategori']);
                ?>
                    <a href="index.php?kategori=<?= urlencode($pc['nama_kategori']) ?>" class="group flex flex-col items-center text-center">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 rounded-full overflow-hidden shadow-sm bg-white p-1 border border-gray-100 flex items-center justify-center">
                            <img src="<?= $img_pc ?>" alt="<?= htmlspecialchars($pc['nama_kategori']) ?>" class="w-full h-full object-cover rounded-full">
                        </div>
                        <span class="mt-2 text-[11px] md:text-xs font-semibold text-gray-700 group-hover:text-[#002147] transition-colors line-clamp-2"><?= htmlspecialchars($pc['nama_kategori']) ?></span>
                    </a>
                <?php endwhile; ?>

                <!-- Item Ke-4: Kategori Lainnya -->
                <a href="katalog.php" class="group flex flex-col items-center text-center">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 rounded-full overflow-hidden shadow-sm bg-gray-200 flex items-center justify-center border border-gray-200 group-hover:bg-gray-300 transition-colors">
                        <span class="text-xs font-bold text-gray-500">Lainnya</span>
                    </div>
                    <span class="mt-2 text-[11px] md:text-xs font-semibold text-gray-700 group-hover:text-[#002147] transition-colors">Lainnya</span>
                </a>
            </div>
        </div>
    </section>

    <!-- 6. SECTION IKLAN HORIZONTAL -->
    <section class="bg-gray-50 border-t border-gray-100 py-4">
        <p class="text-[10px] text-center text-gray-400 tracking-wider mb-2">ADVERTISEMENTS</p>
        <div class="container mx-auto px-4">
            <div class="bg-white border border-gray-200 rounded-sm overflow-hidden shadow-sm">
                <a href="https://smkantartika2-sda.sch.id/post/detail/antartika-fair-2026-event-spektakuler-penuh-talenta-dan-hiburan-di-sidoarjo" target="_blank">
                    <img src="assets/images/iklan-horizontal.png" alt="Iklan Horizontal" class="w-full h-28 md:h-36 object-cover">
                </a>
            </div>
        </div>
    </section>

    <!-- 7. KATALOG UTAMA DENGAN FILTER SIDEBAR -->
    <main class="bg-gray-50 py-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-8">

                <!-- SIDEBAR FILTER (DESKTOP) -->
                <aside class="hidden lg:block w-64 flex-shrink-0">
                    <form action="index.php" method="GET" id="filter-form-sidebar" class="space-y-8">
                        <div>
                            <h3 class="text-sm font-bold text-[#002147] uppercase mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-list text-xs text-gray-400"></i> Kategori
                            </h3>
                            <div class="space-y-3">
                                <?php
                                $all_categories = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                                while ($cat = mysqli_fetch_assoc($all_categories)):
                                    $is_checked = (isset($_GET['kategori']) && $_GET['kategori'] == $cat['nama_kategori']) ? 'checked' : '';
                                ?>
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" name="kategori" value="<?= htmlspecialchars($cat['nama_kategori']) ?>" <?= $is_checked ?>
                                            onchange="this.form.submit()"
                                            class="w-4 h-4 border-gray-300 text-[#FFB606] focus:ring-[#FFB606]">
                                        <span class="text-sm text-gray-600 group-hover:text-[#FFB606] transition-colors"><?= htmlspecialchars($cat['nama_kategori']) ?></span>
                                    </label>
                                <?php endwhile; ?>
                            </div>
                        </div>

                        <hr class="border-gray-200">

                        <div>
                            <h3 class="text-sm font-bold text-[#002147] uppercase mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-tags text-xs text-gray-400"></i> Rentang Harga
                            </h3>
                            <div class="space-y-4">
                                <div class="flex items-center gap-2">
                                    <input type="number" name="min_price" placeholder="Rp MIN" value="<?= isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : '' ?>"
                                        class="w-full text-xs p-2 border border-gray-200 rounded-sm focus:outline-none focus:border-[#FFB606]">
                                    <div class="w-2 h-[1px] bg-gray-300 flex-shrink-0"></div>
                                    <input type="number" name="max_price" placeholder="Rp MAX" value="<?= isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : '' ?>"
                                        class="w-full text-xs p-2 border border-gray-200 rounded-sm focus:outline-none focus:border-[#FFB606]">
                                </div>
                                <button type="submit" class="w-full bg-[#FFB606] text-[#002147] py-1.5 rounded-sm text-xs font-bold hover:bg-yellow-500 transition-colors uppercase">
                                    Terapkan
                                </button>
                            </div>
                        </div>

                        <hr class="border-gray-200">

                        <a href="index.php" class="block w-full text-center bg-gray-100 text-gray-600 py-2 rounded-sm text-sm font-bold hover:bg-gray-200 transition-colors uppercase shadow-sm mt-4">
                            Hapus Semua
                        </a>
                    </form>
                </aside>

                <!-- GRID KATALOG PRODUK -->
                <div class="flex-1">
                    <?php
                    $f_kat = isset($_GET['kategori']) ? $_GET['kategori'] : '';
                    $f_min = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
                    $f_max = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;
                    $f_sort = isset($_GET['sort']) ? $_GET['sort'] : 'terkait';

                    $sql_main = "SELECT p.*, (SELECT AVG(bintang) FROM ulasan WHERE produk_id = p.id) as rata_rating FROM produk p WHERE 1=1";
                    if ($f_kat) $sql_main .= " AND p.kategori = '" . mysqli_real_escape_string($conn, $f_kat) . "'";
                    if ($f_min > 0) $sql_main .= " AND p.harga >= $f_min";
                    if ($f_max > 0) $sql_main .= " AND p.harga <= $f_max";

                    $order_clause = "ORDER BY p.created_at DESC";
                    if ($f_sort == 'terbaru') $order_clause = "ORDER BY p.created_at DESC";
                    elseif ($f_sort == 'terlaris') $order_clause = "ORDER BY p.terjual DESC";
                    elseif ($f_sort == 'harga_asc') $order_clause = "ORDER BY p.harga ASC";
                    elseif ($f_sort == 'harga_desc') $order_clause = "ORDER BY p.harga DESC";

                    $sql_main .= " $order_clause LIMIT 20";
                    $main_query = mysqli_query($conn, $sql_main);
                    $total_filtered = mysqli_num_rows($main_query);

                    if (!function_exists('filterUrl')) {
                        function filterUrl($params)
                        {
                            $current = $_GET;
                            foreach ($params as $key => $val) {
                                if ($val === null) unset($current[$key]);
                                else $current[$key] = $val;
                            }
                            return 'index.php?' . http_build_query($current);
                        }
                    }
                    ?>

                    <!-- Filter Kategori Mobile Horizontal Scroll -->
                    <div class="lg:hidden mb-6 overflow-x-auto no-scrollbar pb-2">
                        <div class="flex gap-2 whitespace-nowrap">
                            <a href="index.php" class="px-4 py-2 <?= !$f_kat ? 'bg-[#FFB606] text-[#002147] border-[#FFB606]' : 'bg-white text-gray-700 border-gray-200' ?> border rounded-full text-xs font-medium transition-all">Semua</a>
                            <?php
                            $kat_mobile = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                            while ($km = mysqli_fetch_assoc($kat_mobile)):
                                $is_active = ($f_kat == $km['nama_kategori']);
                            ?>
                                <a href="<?= filterUrl(['kategori' => $km['nama_kategori']]) ?>" class="px-4 py-2 <?= $is_active ? 'bg-[#FFB606] text-[#002147] border-[#FFB606]' : 'bg-white text-gray-700 border-gray-200' ?> border rounded-full text-xs font-medium transition-all">
                                    <?= htmlspecialchars($km['nama_kategori']) ?>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-bold text-[#002147] uppercase">
                            SEMUA PRODUK <span class="text-gray-400 text-xs font-normal">(<?= $total_filtered ?>)</span>
                        </h2>
                        <button onclick="toggleFilterModal()" class="lg:hidden flex items-center gap-1.5 text-xs font-medium text-gray-600 bg-white px-3 py-1.5 rounded border border-gray-200">
                            <i class="fa-solid fa-sliders"></i> Filter
                        </button>
                    </div>

                    <!-- Items Grid Semua Produk -->
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-2 md:gap-4">
                        <?php
                        if (mysqli_num_rows($main_query) > 0):
                            while ($p = mysqli_fetch_assoc($main_query)):
                                $harga_p = number_format($p['harga'], 0, ',', '.');
                                $img_p = $p['gambar'] ? 'assets/images/' . $p['gambar'] : 'https://placehold.co/200x200?text=No+Image';
                                $rating_p = $p['rata_rating'] ? round($p['rata_rating'], 1) : '0';
                        ?>
                                <a href="detail.php?id=<?= $p['id'] ?>" class="group bg-white border border-gray-100 hover:border-[#FFB606] hover:shadow-lg transition-all duration-150 rounded-sm overflow-hidden flex flex-col h-full">
                                    <div class="relative aspect-square overflow-hidden bg-gray-50">
                                        <img src="<?= $img_p ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>

                                    <div class="p-2 md:p-3 flex flex-col flex-1">
                                        <h3 class="text-xs md:text-sm text-gray-800 line-clamp-2 leading-tight mb-2 h-8 md:h-10">
                                            <?= htmlspecialchars($p['nama_produk']) ?>
                                        </h3>

                                        <div class="flex items-center gap-1 flex-wrap mt-auto">
                                            <span class="text-[10px] md:text-xs text-black font-bold">Rp</span>
                                            <span class="text-sm md:text-lg font-bold text-black"><?= $harga_p ?></span>
                                        </div>

                                        <div class="flex items-center gap-1 mt-2 text-[10px] text-gray-500">
                                            <?php if ($rating_p > 0): ?>
                                                <div class="flex items-center gap-0.5 text-yellow-500">
                                                    <i class="fa-solid fa-star text-[8px]"></i>
                                                    <span class="text-gray-700 font-medium"><?= $rating_p ?></span>
                                                </div>
                                                <div class="w-[1px] h-2 bg-gray-300 mx-0.5"></div>
                                            <?php endif; ?>
                                            <span>Terjual <?= $p['terjual'] ?></span>
                                        </div>

                                        <div class="mt-2 flex items-center gap-1 text-[10px] text-gray-400">
                                            <i class="fa-solid fa-location-dot text-[8px]"></i>
                                            <span>Sidoarjo</span>
                                        </div>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-span-full py-20 text-center rounded-sm">
                                <h3 class="text-lg font-bold text-[#002147] uppercase">Produk Tidak Ditemukan</h3>
                                <p class="text-gray-500 text-sm mt-2">Maaf, tidak ada produk yang sesuai dengan kriteria filter Anda.</p>
                                <a href="index.php" class="inline-block mt-6 px-8 py-2 bg-[#FFB606] text-white text-sm font-semibold rounded-sm hover:bg-yellow-500 transition-all">
                                    Reset Filter
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- SIDEBAR IKLAN VERTIKAL (DESKTOP) -->
                <aside class="hidden xl:block w-48 flex-shrink-0">
                    <p class="text-xs text-center text-gray-500 py-2">ADVERTISEMENTS</p>
                    <div class="bg-white border border-gray-200 rounded-sm overflow-hidden shadow-sm sticky top-24">
                        <a href="https://smkantartika2-sda.sch.id/post/detail/antartika-fair-2026-event-spektakuler-penuh-talenta-dan-hiburan-di-sidoarjo" target="_blank">
                            <img src="assets/images/iklan-vertikal.png" alt="Iklan" class="w-full h-96 object-cover">
                        </a>
                    </div>
                </aside>

            </div>
        </div>
    </main>

    <!-- MODAL FILTER MOBILE -->
    <div id="filterModal" class="fixed inset-0 bg-black/50 z-50 hidden flex justify-end">
        <div class="bg-white w-4/5 max-w-xs h-full p-4 overflow-y-auto flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-200">
                    <h3 class="font-bold text-[#002147] uppercase text-sm">Filter Produk</h3>
                    <button onclick="toggleFilterModal()" class="text-gray-500 hover:text-black">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <form action="index.php" method="GET" class="space-y-6">
                    <div>
                        <h4 class="text-xs font-bold text-[#002147] uppercase mb-3">Rentang Harga</h4>
                        <div class="flex items-center gap-2 mb-3">
                            <input type="number" name="min_price" placeholder="Rp MIN" value="<?= isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : '' ?>" class="w-full text-xs p-2 border border-gray-200 rounded-sm">
                            <span>-</span>
                            <input type="number" name="max_price" placeholder="Rp MAX" value="<?= isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : '' ?>" class="w-full text-xs p-2 border border-gray-200 rounded-sm">
                        </div>
                    </div>
                    <?php if (isset($_GET['kategori'])): ?>
                        <input type="hidden" name="kategori" value="<?= htmlspecialchars($_GET['kategori']) ?>">
                    <?php endif; ?>
                    <button type="submit" class="w-full bg-[#FFB606] text-[#002147] py-2 rounded-sm text-xs font-bold uppercase">Terapkan Filter</button>
                </form>
            </div>
            <a href="index.php" class="block w-full text-center bg-gray-100 text-gray-600 py-2 rounded-sm text-xs font-bold uppercase mt-4">Reset Filter</a>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        function toggleFilterModal() {
            const modal = document.getElementById('filterModal');
            modal.classList.toggle('hidden');
        }
    </script>
</body>

</html>