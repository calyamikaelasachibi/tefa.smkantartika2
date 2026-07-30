<?php
$halaman = basename($_SERVER['PHP_SELF']);
$search_action = (isset($base_url) ? $base_url : '') . 'katalog.php';
include_once 'koneksi.php';
?>

<header class="w-full bg-white font-sans sticky top-0 z-[100] shadow-sm">
    <!-- Top Bar -->
    <div class="bg-gray-100 py-2 hidden lg:block border-b border-gray-200">
        <div class="container mx-auto px-4 flex justify-end items-center text-sm text-gray-600">
            <a href="https://wa.me/6289524309299" target="_blank" class="hover:text-[#002147] flex items-center gap-2 transition-colors">
                <i class="fa-brands fa-whatsapp text-lg"></i>
                <span>Whatsapp</span>
            </a>
        </div>
    </div>

    <!-- Main Header -->
    <div class="container mx-auto px-4 py-4 lg:py-6">
        <div class="flex items-center justify-between gap-6 md:gap-12">

            <!-- Search Bar -->
            <div class="flex-1 max-w-2xl">
                <form method="GET" action="<?= $search_action ?>" class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#002147]">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </div>
                    <input type="text" name="search" placeholder="Cari produk..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                        class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-sm focus:outline-none focus:border-[#FFB606] bg-gray-50 focus:bg-white transition-all text-sm">
                </form>
            </div>

            <!-- Filter Desktop -->
            <div class="hidden lg:block">
                <button onclick="toggleFilterModal()" class="flex items-center gap-2 font-semibold text-sm transition-colors">
                    <i class="fa-solid fa-sliders text-lg"></i>
                    <span>Filter</span>
                </button>
            </div>

            <!-- Mobile Menu Toggle -->
            <button onclick="toggleMobileMenu()" class="lg:hidden text-[#002147] p-2">
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>
        </div>
    </div>

</header>

<!-- Standalone Filter Modal -->
<div id="filter-modal" class="fixed inset-0 z-[150] hidden">
    <div class="absolute inset-0 bg-[#002147]/60 backdrop-blur-sm" onclick="toggleFilterModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-lg shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-xl font-semibold text-[#002147] uppercase">Filter Produk</h3>
            <button onclick="toggleFilterModal()" class="text-gray-400 hover:text-[#002147]">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <form action="<?= $search_action ?>" method="GET" class="p-6 space-y-6">
            <div>
                <label class="block text-[10px] font-semibold text-gray-400 mb-3 uppercase">Kategori</label>
                <select name="kategori" class="w-full p-4 border border-gray-100 rounded-sm focus:outline-none focus:border-[#FFB606] text-sm bg-gray-50">
                    <option value="">Semua Kategori</option>
                    <?php
                    $kat_filter = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                    while ($kf = mysqli_fetch_assoc($kat_filter)):
                    ?>
                        <option value="<?= $kf['nama_kategori'] ?>"><?= $kf['nama_kategori'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="pt-4">
                <button type="submit" class="w-full py-4 bg-[#002147] text-white rounded-sm font-semibold uppercase text-xs hover:bg-[#FFB606] hover:text-[#002147] transition-all shadow-lg">Terapkan Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Mobile Sidebar -->
<div id="mobile-nav-sidebar" class="fixed inset-0 z-[200] lg:hidden transform translate-x-full transition-transform duration-300">
    <div class="absolute inset-0 bg-[#002147]/60 backdrop-blur-sm" onclick="toggleMobileMenu()"></div>
    <div class="absolute right-0 top-0 bottom-0 w-80 bg-white shadow-2xl p-6">
        <div class="flex items-center justify-between mb-10 border-b border-gray-100 pb-4">
            <div class="font-black text-[#002147] text-xl uppercase">Menu</div>
            <button onclick="toggleMobileMenu()" class="text-gray-400 hover:text-[#002147]">
                <i class="fa-solid fa-xmark text-2xl"></i>
            </button>
        </div>
        <nav class="space-y-6">
            <a href="index.php" class="flex items-center gap-4 text-gray-700 hover:text-[#FFB606] font-semibold uppercase text-xs transition-colors">
                <i class="fa-solid fa-house text-lg"></i>
                <span>Beranda</span>
            </a>
            <a href="katalog.php" class="flex items-center gap-4 text-gray-700 hover:text-[#FFB606] font-semibold uppercase text-xs transition-colors">
                <i class="fa-solid fa-box text-lg"></i>
                <span>Katalog Produk</span>
            </a>
            <a href="tentang.php" class="flex items-center gap-4 text-gray-700 hover:text-[#FFB606] font-semibold uppercase text-xs transition-colors">
                <i class="fa-solid fa-info-circle text-lg"></i>
                <span>Tentang Kami</span>
            </a>
            <a href="kontak.php" class="flex items-center gap-4 text-gray-700 hover:text-[#FFB606] font-semibold uppercase text-xs transition-colors">
                <i class="fa-solid fa-phone text-lg"></i>
                <span>Hubungi Kami</span>
            </a>
        </nav>
    </div>
</div>