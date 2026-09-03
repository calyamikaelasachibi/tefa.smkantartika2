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
    <title>Tefa Store - SMKS Antartika 2 Kelompok keren</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        .swiper-products .swiper-pagination-bullet {
            width: 8px;
            height: 8px;
            background: #d1d5db;
            opacity: 1;
        }

        .swiper-products .swiper-pagination-bullet-active {
            background: #002147;
        }

        .swiper-products .swiper-slide {
            height: auto !important;
            margin-bottom: 20px;
        }

        .swiper-products {
            padding-bottom: 40px !important;
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            display: none !important;
        }
    </style>
</head>

<body>

    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sticky-navbar.php'; ?>

    <section class="bg-gray-50 py-12 border-b border-gray-100">
        <div class="max-w-5xl mx-auto px-4 relative">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-xl font-bold text-[#002147] border-b-4 border-[#FFB606] pb-2 inline-block">Rekomendasi Spesial</h2>
                    <p class="text-gray-500 text-xs mt-2">Produk pilihan dengan rating dan penjualan terbaik</p>
                </div>
            </div>

            <div class="relative group/swiper px-2 md:px-12">
                <div class="swiper swiper-products !pb-12">
                    <div class="swiper-wrapper">
                        <?php
                        $random_products = mysqli_query($conn, "
                            SELECT p.*, 
                            (SELECT AVG(bintang) FROM ulasan WHERE produk_id = p.id) as rata_rating
                            FROM produk p 
                            WHERE p.stok > 0 
                            ORDER BY RAND() 
                            LIMIT 16
                        ");

                        while ($rp = mysqli_fetch_assoc($random_products)):
                            $img_rp = $rp['gambar'] ? 'assets/images/' . $rp['gambar'] : '';
                        ?>
                            <div class="swiper-slide !h-auto">
                                <a href="detail.php?id=<?= $rp['id'] ?>" class="group block bg-white border border-gray-200 hover:border-[#002147] hover:border-2 transition-all duration-150 h-full p-4 flex flex-col items-center">
                                    <div class="w-full aspect-square overflow-hidden mb-4 flex items-center justify-center">
                                        <img src="<?= $img_rp ?>" alt="<?= $rp['nama_produk'] ?>" class="max-w-full max-h-full object-contain">
                                    </div>

                                    <div class="text-center">
                                        <h3 class="text-xs md:text-sm font-medium text-gray-800 line-clamp-2 leading-snug"><?= $rp['nama_produk'] ?></h3>
                                    </div>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="swiper-pagination !bottom-0"></div>
                </div>

                <div class="swiper-button-next !w-10 !h-10 !bg-white !rounded-full !text-[#FFB606] hover:!bg-[#FFB606] hover:!text-white transition-all !-right-4 md:!-right-6 border border-gray-100 flex items-center justify-center">
                    <i class="fa-solid fa-chevron-right text-sm"></i>
                </div>
                <div class="swiper-button-prev !w-10 !h-10 !bg-white !rounded-full !text-[#FFB606] hover:!bg-[#FFB606] hover:!text-white transition-all !-left-4 md:!-left-6 border border-gray-100 flex items-center justify-center">
                    <i class="fa-solid fa-chevron-left text-sm"></i>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-gray-50 py-16">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-20">
                <?php
                $top_cards = mysqli_query($conn, "SELECT * FROM produk WHERE stok > 0 ORDER BY created_at DESC LIMIT 3");
                while ($tc = mysqli_fetch_assoc($top_cards)):
                    $img_tc = $tc['gambar'] ? 'assets/images/' . $tc['gambar'] : 'https://placehold.co/300x200?text=No+Image';
                ?>
                    <a href="detail.php?id=<?= $tc['id'] ?>" class=" p-6 rounded-sm flex items-center gap-4 transition-shadow">
                        <div class="w-1/2 aspect-[3/2] overflow-hidden rounded-sm">
                            <img src="<?= $img_tc ?>" alt="<?= $tc['nama_produk'] ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="w-1/2">
                            <h4 class="text-sm font-bold text-gray-900 mb-1 line-clamp-1"><?= $tc['nama_produk'] ?></h4>
                            <p class="text-[10px] text-gray-500 mb-3 leading-tight line-clamp-2">
                                <?php
                                $desc = !empty(trim($tc['deskripsi'])) ? strip_tags($tc['deskripsi']) : 'Produk unggulan terbaru dari toko kami.';
                                echo $desc;
                                ?>
                            </p>
                            <div class="bg-[#FFB606] p-1 px-4 w-fit text-sm text-white rounded-sm justify-center">
                                Cek sekarang
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>

            <div class="text-center mb-20">
                <h2 class="text-xl md:text-2xl font-bold text-[#002147] mb-1 uppercase">Temukan Produk Terbaik Untuk Kebutuhan Anda</h2>
                <p class="text-[#FFB606] text-md md:text-lg font-medium mb-10">Koleksi Terlengkap & Kualitas Terjamin Hanya disini</p>
                <a href="katalog.php" class="inline-block text-white px-12 py-4 rounded-md font-semibold bg-[#FFB606]">
                    Cari Produkmu!
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-20 text-center">
                <?php
                $featured_cta = mysqli_query($conn, "SELECT * FROM produk WHERE stok > 0 ORDER BY RAND() LIMIT 3");
                while ($fc = mysqli_fetch_assoc($featured_cta)):
                    $img_fc = $fc['gambar'] ? 'assets/images/' . $fc['gambar'] : 'https://placehold.co/200x300?text=Product';
                ?>
                    <div class="flex flex-col items-center group">
                        <a href="detail.php?id=<?= $fc['id'] ?>" class="w-full">
                            <div class="w-full aspect-[4/5] mb-6 flex items-center justify-center rounded-xl overflow-hidden border-gray-100">
                                <img src="<?= $img_fc ?>" alt="<?= $fc['nama_produk'] ?>" class="max-h-full max-w-full object-contain p-6">
                            </div>
                            <h3 class="text-lg font-bold text-[#002147] group-hover:text-[#FFB606]"><?= $fc['nama_produk'] ?></h3>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>

            <!--<div class="flex flex-wrap justify-around gap-16 md:gap-32 bg-gray-50 py-12 rounded-2xl">-->
            <!--    <div class="flex items-center gap-6">-->
            <!--        <div class="w-16 h-16 rounded-full flex items-center justify-center text-[#FFB606]">-->
            <!--            <i class="fa-solid fa-display text-3xl"></i>-->
            <!--        </div>-->
            <!--        <div>-->
            <!--            <h4 class="text-xl font-bold text-[#002147]">Display & Jual</h4>-->
            <!--            <p class="text-sm text-gray-500">Etalase digital produk terbaik</p>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--    <div class="flex items-center gap-6">-->
            <!--        <div class="w-16 h-16 rounded-full flex items-center justify-center text-[#FFB606]">-->
            <!--            <i class="fa-solid fa-comments text-3xl"></i>-->
            <!--        </div>-->
            <!--        <div>-->
            <!--            <h4 class="text-xl font-bold text-[#002147]">Diskusi Produk</h4>-->
            <!--            <p class="text-sm text-gray-500">Tanya jawab seputar produk</p>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->

            <div class="mt-24 text-center">
                <h2 class="text-xl md:text-2xl font-bold text-[#002147] mb-10 uppercase">Kategori terpopuler</h2>
                <div class="flex flex-wrap justify-center gap-8 md:gap-12">
                    <?php
                    $pop_cats = mysqli_query($conn, "
                        SELECT k.nama_kategori, 
                        (SELECT gambar FROM produk WHERE kategori = k.nama_kategori AND gambar != '' ORDER BY RAND() LIMIT 1) as gambar_produk
                        FROM kategori k 
                        ORDER BY (SELECT COUNT(id) FROM produk WHERE kategori = k.nama_kategori) DESC 
                        LIMIT 5
                    ");
                    while ($pc = mysqli_fetch_assoc($pop_cats)):
                        $img_pc = $pc['gambar_produk'] ? 'assets/images/' . $pc['gambar_produk'] : 'https://placehold.co/200x200?text=' . urlencode($pc['nama_kategori']);
                    ?>
                        <a href="index.php?kategori=<?= urlencode($pc['nama_kategori']) ?>" class="group flex flex-col items-center">
                            <div class="w-24 h-24 md:w-32 md:h-32 rounded-full overflow-hidden">
                                <img src="<?= $img_pc ?>" alt="<?= $pc['nama_kategori'] ?>" class="w-full h-full object-cover">
                            </div>
                            <span class="mt-4 text-xs font-semibold text-gray-700 group-hover:text-[#002147]"><?= $pc['nama_kategori'] ?></span>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-gray-50 border-t border-gray-100">
        <p class="text-xs text-center text-gray-500 py-2">ADVERTISEMENTS</p>
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 gap-6">
                <div class="bg-white border border-gray-200 rounded-sm overflow-hidden shadow-sm">
                    <a href="https://smkantartika2-sda.sch.id/post/detail/antartika-fair-2026-event-spektakuler-penuh-talenta-dan-hiburan-di-sidoarjo" target="_blank">
                        <img src="assets/images/iklan-horizontal.png" 
                             alt="Iklan Horizontal" 
                             class="w-full h-32 object-cover">
                    </a>
                </div>
            </div>
        </div>
    </section>

    <main class="bg-gray-50 py-10">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-8">

                <aside class="hidden lg:block w-64 flex-shrink-0">
                    <form action="index.php" method="GET" id="filter-form-sidebar" class="space-y-8">
                        <div>
                            <h3 class="text-sm font-bold text-[#002147] uppercase mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-list text-xs text-gray-400"></i>
                                Kategori
                            </h3>
                            <div class="space-y-3">
                                <?php
                                $all_categories = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                                while ($cat = mysqli_fetch_assoc($all_categories)):
                                    $is_checked = (isset($_GET['kategori']) && $_GET['kategori'] == $cat['nama_kategori']) ? 'checked' : '';
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

                        <hr class="border-gray-200">

                        <div>
                            <h3 class="text-sm font-bold text-[#002147] uppercase mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-tags text-xs text-gray-400"></i>
                                Rentang Harga
                            </h3>
                            <div class="space-y-4">
                                <div class="flex items-center gap-2">
                                    <input type="number" name="min_price" placeholder="Rp MIN" value="<?= isset($_GET['min_price']) ? $_GET['min_price'] : '' ?>"
                                        class="w-full text-xs p-2 border border-gray-200 rounded-sm focus:outline-none focus:border-[#FFB606]">
                                    <div class="w-2 h-[1px] bg-gray-300 flex-shrink-0"></div>
                                    <input type="number" name="max_price" placeholder="Rp MAX" value="<?= isset($_GET['max_price']) ? $_GET['max_price'] : '' ?>"
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

                    <div class="lg:hidden mb-6 overflow-x-auto no-scrollbar pb-2">
                        <div class="flex gap-2 whitespace-nowrap">
                            <a href="index.php" class="px-4 py-2 <?= !$f_kat ? 'bg-[#FFB606] text-[#002147] border-[#FFB606]' : 'bg-white text-gray-700 border-gray-200' ?> border rounded-full text-xs font-medium transition-all">Semua</a>
                            <?php
                            $kat_mobile = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                            while ($km = mysqli_fetch_assoc($kat_mobile)):
                                $is_active = ($f_kat == $km['nama_kategori']);
                            ?>
                                <a href="<?= filterUrl(['kategori' => $km['nama_kategori']]) ?>" class="px-4 py-2 <?= $is_active ? 'bg-[#FFB606] text-[#002147] border-[#FFB606]' : 'bg-white text-gray-700 border-gray-200' ?> border rounded-full text-xs font-medium transition-all">
                                    <?= $km['nama_kategori'] ?>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <div class="lg:hidden mb-4 flex items-center justify-between">
                        <h2 class="text-sm font-bold uppercase">
                            <?= $f_kat ? $f_kat : 'Semua Produk' ?>
                            <span class="text-[10px] font-normal text-gray-400 ml-1">(<?= $total_filtered ?>)</span>
                        </h2>
                        <button onclick="toggleFilterModal()" class="flex items-center gap-2 text-xs font-medium text-gray-500/50 bg-gray-100 px-3 py-1.5 rounded-sm border border-gray-200">
                            <i class="fa-solid fa-sliders"></i>
                            Filter
                        </button>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-2 md:gap-4">
                        <?php
                        if (mysqli_num_rows($main_query) > 0):
                            while ($p = mysqli_fetch_assoc($main_query)):
                                $harga_p = number_format($p['harga'], 0, ',', '.');
                                $img_p = $p['gambar'] ? 'assets/images/' . $p['gambar'] : '';
                                $rating_p = $p['rata_rating'] ? round($p['rata_rating'], 1) : '0';
                        ?>
                                <a href="detail.php?id=<?= $p['id'] ?>" class="group bg-white border border-transparent hover:border-[#FFB606] hover:shadow-lg transition-all duration-150 rounded-sm overflow-hidden flex flex-col h-full">
                                    <div class="relative aspect-square overflow-hidden bg-gray-100">
                                        <img src="<?= $img_p ?>" alt="<?= $p['nama_produk'] ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>

                                    <div class="p-2 md:p-3 flex flex-col flex-1">
                                        <h3 class="text-xs md:text-sm text-gray-800 line-clamp-2 leading-tight mb-2 h-8 md:h-10">
                                            <?= $p['nama_produk'] ?>
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

    <?php include 'includes/footer.php'; ?>

</body>

</html>
