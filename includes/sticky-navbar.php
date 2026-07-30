<?php
// includes/sticky-navbar.php
$halaman_sticky = basename($_SERVER['PHP_SELF']);
$search_action_sticky = (isset($base_url) ? $base_url : '') . 'katalog.php';
include_once 'koneksi.php';
?>

<!-- Sticky Navbar Container -->
<div id="sticky-navbar" class="fixed top-0 left-0 w-full bg-white shadow-lg z-[1000] transform -translate-y-full opacity-0 invisible pointer-events-none hidden lg:block border-b border-gray-100">
    <div class="bg-gray-100 py-2 hidden lg:block border-b border-gray-200">
        <div class="container mx-auto px-4 flex justify-end items-center text-sm text-gray-600">
            <!--<a href="https://wa.me/6289524309299" target="_blank" class="hover:text-[#002147] flex items-center gap-2 ">-->
            <!--    <i class="fa-brands fa-whatsapp text-lg"></i>-->
            <!--    <span>Whatsapp</span>-->
            <!--</a>-->
        </div>
    </div>
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between gap-8 h-20">
            <!-- Navigation Links with Mega Panels -->
            <nav class="flex items-center h-full">
                <!-- Beranda -->
                <?php $is_index = ($halaman_sticky == 'index.php'); ?>
                <div class="group h-full flex items-center">
                    <a href="index.php" class="px-6 h-full flex items-center font-bold text-xs uppercase <?= $is_index ? 'text-[#002147]' : 'text-gray-700' ?> hover:text-[#002147]  relative">
                        Beranda
                    </a>

                    <!-- Mega Panel Beranda -->
                    <div class="absolute top-full left-0 w-full bg-[#002147] text-white opacity-0 invisible group-hover:opacity-100 group-hover:visible   shadow-2xl border-t border-blue-900 overflow-hidden">
                        <div class="container mx-auto">
                            <div class="grid grid-cols-3 h-full divide-x divide-blue-900/50">
                                <div class="relative group/panel overflow-hidden min-h-[200px] flex flex-col justify-center p-8 hover:bg-blue-950/30 ">
                                    <div class="relative z-10">
                                        <h3 class="text-xl font-bold mb-4">Tefa Store</h3>
                                        <p class="text-gray-400 text-xs mb-6 leading-relaxed">Pusat produk inovatif hasil karya siswa/siswi SMKS Antartika 2 Sidoarjo.</p>
                                        <a href="katalog.php" class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147] ">
                                            Lihat Produk <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="relative group/panel overflow-hidden min-h-[200px] flex flex-col justify-center p-8 hover:bg-blue-950/30 ">
                                    <div class="relative z-10">
                                        <h3 class="text-xl font-bold mb-4">Produk Unggulan</h3>
                                        <p class="text-gray-400 text-xs mb-6 leading-relaxed">Cek produk terlaris dan paling direkomendasikan bulan ini.</p>
                                        <a href="katalog.php?sort=terlaris" class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147] ">
                                            Cek Rekomendasi <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="relative group/panel overflow-hidden min-h-[200px] flex flex-col justify-center p-8 hover:bg-blue-950/30 ">
                                    <div class="relative z-10">
                                        <h3 class="text-xl font-bold mb-4">Promo Spesial</h3>
                                        <p class="text-gray-400 text-xs mb-6 leading-relaxed">Dapatkan penawaran harga terbaik untuk berbagai kategori produk.</p>
                                        <a href="katalog.php?sort=termurah" class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147] ">
                                            Lihat Promo <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Katalog -->
                <?php $is_katalog = ($halaman_sticky == 'katalog.php' || $halaman_sticky == 'detail.php'); ?>
                <div class="group h-full flex items-center">
                    <a href="katalog.php" class="px-6 h-full flex items-center font-bold text-xs uppercase <?= $is_katalog ? 'text-[#002147]' : 'text-gray-700' ?> hover:text-[#002147]  relative">
                        Katalog
                    </a>

                    <!-- Mega Panel Katalog -->
                    <div class="absolute top-full left-0 w-full bg-[#002147] text-white opacity-0 invisible group-hover:opacity-100 group-hover:visible   shadow-2xl border-t border-blue-900 overflow-hidden">
                        <div class="container mx-auto">
                            <div class="grid grid-cols-4 h-full divide-x divide-blue-900/50">
                                <?php
                                $kat_q_sticky = mysqli_query($conn, "SELECT k.*, (SELECT gambar FROM produk WHERE kategori = k.nama_kategori LIMIT 1) as contoh_gambar FROM kategori k LIMIT 8");
                                while ($k_sticky = mysqli_fetch_assoc($kat_q_sticky)):
                                    $bg_img_sticky = $k_sticky['contoh_gambar'] ? 'assets/images/' . $k_sticky['contoh_gambar'] : '';
                                ?>
                                    <div class="relative group/item overflow-hidden min-h-[200px] flex flex-col justify-center p-4 hover:bg-blue-950/30  border-b border-blue-900/50">
                                        <?php if ($bg_img_sticky): ?>
                                            <div class="absolute inset-0 opacity-0 group-hover/item:opacity-20 bg-cover bg-center" style="background-image: url('<?= $bg_img_sticky ?>')"></div>
                                        <?php endif; ?>
                                        <div class="relative z-10">
                                            <h3 class="text-sm font-bold mb-4"><?= $k_sticky['nama_kategori'] ?></h3>
                                            <a href="katalog.php?kategori=<?= urlencode($k_sticky['nama_kategori']) ?>"
                                                class="inline-flex items-center gap-2 text-[10px] font-bold uppercase text-[#FFB606] hover:text-white  group/btn">
                                                Lihat Produk
                                                <i class="fa-solid fa-arrow-right group-hover/btn:translate-x-1 "></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tentang -->
                <?php $is_tentang = ($halaman_sticky == 'tentang.php'); ?>
                <div class="group h-full flex items-center">
                    <a href="tentang.php" class="px-6 h-full flex items-center font-bold text-xs uppercase <?= $is_tentang ? 'text-[#002147]' : 'text-gray-700' ?> hover:text-[#002147]  relative">
                        Tentang
                    </a>

                    <!-- Mega Panel Tentang -->
                    <div class="absolute top-full left-0 w-full bg-[#002147] text-white opacity-0 invisible group-hover:opacity-100 group-hover:visible   shadow-2xl border-t border-blue-900 overflow-hidden">
                        <div class="container mx-auto">
                            <div class="grid grid-cols-1 md:grid-cols-3 h-full divide-x divide-blue-900/50">
                                <div class="relative overflow-hidden min-h-[200px] flex flex-col justify-center p-4 hover:bg-blue-950/30 ">
                                    <div class="relative z-10">
                                        <h3 class="text-xl font-bold mb-4">Tentang Kami</h3>
                                        <p class="text-gray-400 text-xs mb-6 leading-relaxed">Menjadi pusat inovasi dan kreativitas siswa SMKS Antartika 2 Sidoarjo yang berdaya saing global.</p>
                                        <a href="tentang.php#visi" class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147] ">
                                            Selengkapnya <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kontak -->
                <?php $is_kontak = ($halaman_sticky == 'kontak.php'); ?>
                <div class="group h-full flex items-center">
                    <a href="kontak.php" class="px-6 h-full flex items-center font-bold text-xs uppercase <?= $is_kontak ? 'text-[#002147]' : 'text-gray-700' ?> hover:text-[#002147]  relative">
                        Kontak
                    </a>

                    <!-- Mega Panel Kontak -->
                    <div class="absolute top-full left-0 w-full bg-[#002147] text-white opacity-0 invisible group-hover:opacity-100 group-hover:visible   shadow-2xl border-t border-blue-900 overflow-hidden">
                        <div class="container mx-auto">
                            <div class="grid grid-cols-2 h-full divide-x divide-blue-900/50">
                                <div class="relative overflow-hidden min-h-[200px] flex flex-col justify-center p-8 hover:bg-blue-950/30 ">
                                    <div class="relative z-10">
                                        <h3 class="text-xl font-bold mb-4">WhatsApp Kami</h3>
                                        <p class="text-gray-400 text-xs mb-6 leading-relaxed">Fast response untuk pertanyaan produk, stok, dan pemesanan custom.</p>
                                        <a href="https://wa.me/6289524309299" target="_blank" class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147] ">
                                            Chat Sekarang <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="relative overflow-hidden min-h-[200px] flex flex-col justify-center p-8 hover:bg-blue-950/30 ">
                                    <div class="relative z-10">
                                        <h3 class="text-xl font-bold mb-4">Kirim Pesan</h3>
                                        <p class="text-gray-400 text-xs mb-6 leading-relaxed">Tinggalkan pesan Anda melalui form kontak kami untuk kerjasama lebih lanjut.</p>
                                        <a href="kontak.php#form" class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147] ">
                                            Isi Form <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Search Bar Compact -->
            <div class="flex-1 max-w-sm">
                <form method="GET" action="<?= $search_action_sticky ?>" class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#002147]">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </div>
                    <input type="text" name="search" placeholder="Cari produk..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                        class="w-full pl-11 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-[#002147] bg-gray-50 focus:bg-white text-sm ">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let lastScrollTop = 0;
        const stickyNavbar = document.getElementById('sticky-navbar');
        const scrollThreshold = 400; // Threshold before showing sticky navbar

        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            // Ensure scrollTop is not negative (can happen on some browsers)
            if (scrollTop < 0) scrollTop = 0;

            if (scrollTop > scrollThreshold) {
                if (scrollTop > lastScrollTop) {
                    // Scrolling down - SHOW navbar
                    stickyNavbar.classList.remove('-translate-y-full', 'opacity-0', 'invisible', 'pointer-events-none');
                } else {
                    // Scrolling up - HIDE navbar
                    stickyNavbar.classList.add('-translate-y-full', 'opacity-0', 'invisible', 'pointer-events-none');
                }
            } else {
                // Above threshold - ALWAYS hide
                stickyNavbar.classList.add('-translate-y-full', 'opacity-0', 'invisible', 'pointer-events-none');
            }

            lastScrollTop = scrollTop;
        }, {
            passive: true
        });
    });
</script>