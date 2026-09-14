<?php
$halaman = basename($_SERVER['PHP_SELF']);
$base_url_path = isset($base_url) ? $base_url : '';
$search_action = $base_url_path . 'katalog.php';
include_once 'koneksi.php';
?>

<header class="w-full bg-[#FFFFFF] font-sans sticky top-0 lg:relative z-[100] shadow-sm">
    <!-- Top Bar / Floating WhatsApp Button -->
    <div class="bg-gray-100 py-2 border-b border-gray-200 hidden lg:block">
        <div class="container mx-auto px-4 flex justify-end items-center text-sm text-gray-600">
            <a href="https://wa.me/6289524309299" target="_blank" rel="noopener noreferrer"
               class="fixed bottom-6 right-6 z-50 bg-green-500 hover:bg-green-600 text-white 
                      w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition-transform hover:scale-105">
                <i class="fa-brands fa-whatsapp text-2xl"></i>
            </a>
        </div>
    </div>

    <!-- Main Header (Desktop & Mobile/Tablet) -->
    <div class="container mx-auto px-4 py-3 lg:py-6">
        <!-- Layout Desktop (Large Screens 1024px+) -->
        <div class="hidden lg:flex items-center justify-between gap-8">
            <!-- Search Bar Desktop -->
            <div class="flex-1 max-w-2xl">
                <form method="GET" action="<?= $search_action ?>" class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#FFB606]">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <input type="text" name="search" placeholder="Cari produk..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                        class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-[#FFB606] bg-gray-50 focus:bg-white text-gray-800">
                </form>
            </div>

            <!-- Filter Button Desktop -->
            <div class="flex items-center gap-6">
                <button type="button" onclick="toggleFilterModal()" class="flex items-center gap-2 text-white hover:text-[#FFB606] font-semibold transition-colors">
                    <i class="fa-solid fa-sliders text-xl"></i>
                    <span>Filter</span>
                </button>
            </div>
        </div>

        <!-- Layout Mobile/Tablet -->
        <div class="lg:hidden flex items-center justify-between gap-3">
            <form method="GET" action="<?= $search_action ?>" class="flex-1">
                <div class="relative flex items-center w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-300">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </div>
                    <input type="text" name="search" placeholder="Cari produk di Tefa Store..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                        class="w-full pl-9 pr-4 py-2 bg-[#001529] border border-[#003366] rounded-md focus:outline-none focus:border-[#FFB606] text-white placeholder-gray-400 text-sm">
                </div>
            </form>

            <!-- Tombol Filter Mobile -->
            <button type="button" onclick="toggleFilterModal()" class="text-white p-2 hover:text-[#FFB606] focus:outline-none flex-shrink-0">
                <i class="fa-solid fa-sliders text-xl"></i>
            </button>

            <!-- Burger Menu Icon -->
            <button type="button" onclick="toggleMobileMenu()" class="text-white p-1 focus:outline-none flex-shrink-0">
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>
        </div>
    </div>

    <!-- Filter Modal -->
    <div id="filter-modal" class="fixed inset-0 z-[150] hidden">
        <div class="absolute inset-0 bg-[#002147]/60 backdrop-blur-sm" onclick="toggleFilterModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[90%] max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-[#002147]">Filter Produk</h3>
                <button type="button" onclick="toggleFilterModal()" class="text-gray-400 hover:text-[#002147]">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            
            <form action="<?= $search_action ?>" method="GET" class="p-6 space-y-6">
                <?php if(isset($_GET['search'])): ?>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search']) ?>">
                <?php endif; ?>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 uppercase">Kategori</label>
                    <select name="kategori" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FFB606]">
                        <option value="">Semua Kategori</option>
                        <?php
                        $kat_filter = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                        while($kf = mysqli_fetch_assoc($kat_filter)):
                            $selected = (isset($_GET['kategori']) && $_GET['kategori'] == $kf['nama_kategori']) ? 'selected' : '';
                        ?>
                        <option value="<?= htmlspecialchars($kf['nama_kategori']) ?>" <?= $selected ?>><?= htmlspecialchars($kf['nama_kategori']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Rentang Harga -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 uppercase">Rentang Harga (Rp)</label>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="number" name="min_price" placeholder="Min" value="<?= isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : '' ?>" 
                            class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FFB606]">
                        <input type="number" name="max_price" placeholder="Max" value="<?= isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : '' ?>" 
                            class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FFB606]">
                    </div>
                </div>

                <!-- Urutan -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 uppercase">Urutkan Berdasarkan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <?php 
                        $sort_options = [
                            'terbaru' => 'Terbaru',
                            'termurah' => 'Harga Terendah',
                            'termahal' => 'Harga Tertinggi',
                            'terlaris' => 'Terlaris'
                        ];
                        $current_sort = isset($_GET['sort']) ? $_GET['sort'] : 'terbaru';
                        foreach($sort_options as $val => $label):
                        ?>
                        <label class="relative flex items-center gap-2 p-3 border border-gray-100 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="sort" value="<?= $val ?>" <?= $current_sort == $val ? 'checked' : '' ?> class="accent-[#FFB606]">
                            <span class="text-sm text-gray-600 font-medium"><?= $label ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="button" onclick="window.location.href='<?= $search_action ?>'" class="flex-1 py-3 px-6 border border-gray-200 rounded-xl font-bold text-gray-500 hover:bg-gray-50 transition-colors">Reset</button>
                    <button type="submit" class="flex-1 py-3 px-6 bg-[#FFB606] text-[#002147] rounded-xl font-bold hover:bg-yellow-500 transition-colors shadow-lg shadow-yellow-400/20">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Desktop Navigation Bar -->
    <div class="border-gray-100 hidden lg:block bg-white">
        <div class="container mx-auto px-4">
            <nav class="flex justify-start">
                <a href="<?= $base_url_path ?>index.php"
                    class="flex-1 max-w-[250px] <?= $halaman == 'index.php' ? 'border-x border-gray-100 py-4 px-6 relative bg-#002147 text-gray-700 hover:bg-gray-50' : 'border-x border-gray-100 py-4 px-6 relative bg-white text-gray-700 hover:bg-gray-50' ?>
                          <?= $halaman == 'index.php' ? 'border-t-4 border-t-[#FFB606]' : '' ?>">
                    <div class="flex items-center gap-4">
                        <div class="text-2xl <?= $halaman == 'index.php' ? 'text-[#FFB606]' : 'text-gray-400' ?>">
                            <i class="fa-solid fa-house text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-xs uppercase leading-none mb-1 <?= $halaman == 'index.php' ? 'text-[#002147]' : '' ?>">Beranda</div>
                            <div class="text-[10px] text-gray-400">Halaman Utama</div>
                        </div>
                    </div>
                </a>

                <a href="<?= $base_url_path ?>katalog.php"
                    class="flex-1 max-w-[250px] border-r border-gray-100 py-4 px-6 relative bg-white text-gray-700 hover:bg-gray-50
                          <?= ($halaman == 'katalog.php' || $halaman == 'detail.php') ? 'border-t-4 border-t-[#FFB606]' : '' ?>">
                    <div class="flex items-center gap-4">
                        <div class="text-2xl <?= ($halaman == 'katalog.php' || $halaman == 'detail.php') ? 'text-[#FFB606]' : 'text-gray-400' ?>">
                            <i class="fa-solid fa-box text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-xs uppercase leading-none mb-1 <?= ($halaman == 'katalog.php' || $halaman == 'detail.php') ? 'text-[#002147]' : '' ?>">Katalog</div>
                            <div class="text-[10px] text-gray-400">Produk Tefa</div>
                        </div>
                    </div>
                </a>

                <a href="<?= $base_url_path ?>tentang.php"
                    class="flex-1 max-w-[250px] border-r border-gray-100 py-4 px-6 relative bg-white text-gray-700 hover:bg-gray-50
                          <?= $halaman == 'tentang.php' ? 'border-t-4 border-t-[#FFB606]' : '' ?>">
                    <div class="flex items-center gap-4">
                        <div class="text-2xl <?= $halaman == 'tentang.php' ? 'text-[#FFB606]' : 'text-gray-400' ?>">
                            <i class="fa-solid fa-circle-info text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-xs uppercase leading-none mb-1 <?= $halaman == 'tentang.php' ? 'text-[#002147]' : '' ?>">Tentang</div>
                            <div class="text-[10px] text-gray-400">Mengenal Tefa</div>
                        </div>
                    </div>
                </a>

                <a href="<?= $base_url_path ?>kontak.php"
                    class="flex-1 max-w-[250px] border-r border-gray-100 py-4 px-6 relative bg-white text-gray-700 hover:bg-gray-50
                          <?= $halaman == 'kontak.php' ? 'border-t-4 border-t-[#FFB606]' : '' ?>">
                    <div class="flex items-center gap-4">
                        <div class="text-2xl <?= $halaman == 'kontak.php' ? 'text-[#FFB606]' : 'text-gray-400' ?>">
                            <i class="fa-solid fa-phone text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-xs uppercase leading-none mb-1 <?= $halaman == 'kontak.php' ? 'text-[#002147]' : '' ?>">Kontak</div>
                            <div class="text-[10px] text-gray-400">Hubungi Kami</div>
                        </div>
                    </div>
                </a>
            </nav>
        </div>
    </div>

    <!-- Mega Menu Section (Visible only on Desktop) -->
    <div class="bg-[#002147] text-white border-b border-blue-900 min-h-[200px] hidden lg:flex items-center">
        <div class="container mx-auto">
            <?php if ($halaman == 'index.php'): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 h-full">
                    <div class="relative group border-r border-blue-900 overflow-hidden min-h-[200px] flex flex-col justify-center p-6">
                        <div class="relative z-10">
                            <h3 class="text-xl font-bold mb-3">Tefa Store</h3>
                            <p class="text-gray-400 text-xs mb-4 leading-relaxed">Pusat produk inovatif hasil karya siswa/siswi SMKS Antartika 2 Sidoarjo.</p>
                            <a href="katalog.php" class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147] transition">
                                Lihat Produk <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                    <div class="relative group border-r border-blue-900 overflow-hidden min-h-[200px] flex flex-col justify-center p-6">
                        <div class="relative z-10">
                            <h3 class="text-xl font-bold mb-3">Produk Unggulan</h3>
                            <p class="text-gray-400 text-xs mb-4 leading-relaxed">Cek produk terlaris dan paling direkomendasikan bulan ini.</p>
                            <a href="katalog.php?sort=terlaris" class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147] transition">
                                Cek Rekomendasi <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                    <div class="relative group border-r border-blue-900 overflow-hidden min-h-[200px] flex flex-col justify-center p-6">
                        <div class="relative z-10">
                            <h3 class="text-xl font-bold mb-3">Promo Spesial</h3>
                            <p class="text-gray-400 text-xs mb-4 leading-relaxed">Dapatkan penawaran harga terbaik untuk berbagai kategori produk.</p>
                            <a href="katalog.php?sort=termurah" class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147] transition">
                                Lihat Promo <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>

            <?php elseif ($halaman == 'katalog.php' || $halaman == 'detail.php'): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 h-full">
                    <?php
                    $kat_q = mysqli_query($conn, "SELECT k.*, (SELECT gambar FROM produk WHERE kategori = k.nama_kategori LIMIT 1) as contoh_gambar FROM kategori k LIMIT 4");
                    while ($k = mysqli_fetch_assoc($kat_q)):
                        $bg_img = $k['contoh_gambar'] ? 'assets/images/' . $k['contoh_gambar'] : '';
                    ?>
                        <div class="relative group border-r border-blue-900 overflow-hidden min-h-[200px] flex flex-col justify-center p-6">
                            <?php if ($bg_img): ?>
                                <div class="absolute inset-0 opacity-0 group-hover:opacity-20 bg-cover bg-center transition-opacity" style="background-image: url('<?= $bg_img ?>')"></div>
                            <?php endif; ?>
                            <div class="relative z-10">
                                <h3 class="text-lg font-bold mb-4"><?= htmlspecialchars($k['nama_kategori']) ?></h3>
                                <a href="katalog.php?kategori=<?= urlencode($k['nama_kategori']) ?>"
                                    class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147] group/btn transition">
                                    Semua produk kategori
                                    <i class="fa-solid fa-chevron-right text-[10px] group-hover/btn:translate-x-1 transition-transform"></i>
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

            <?php elseif ($halaman == 'tentang.php'): ?>
                <div class="grid grid-cols-1 md:grid-cols-3 h-full">
                    <div class="relative group border-r border-blue-900 overflow-hidden min-h-[200px] flex flex-col justify-center p-6">
                        <div class="relative z-10">
                            <h3 class="text-xl font-bold mb-3">Tentang Kami</h3>
                            <p class="text-gray-400 text-xs mb-4 leading-relaxed">Menjadi pusat inovasi dan kreativitas siswa SMKS Antartika 2 Sidoarjo yang berdaya saing global.</p>
                            <a href="tentang.php#visi" class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147] transition">
                                Selengkapnya <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>

            <?php elseif ($halaman == 'kontak.php'): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 h-full">
                    <div class="relative group border-r border-blue-900 overflow-hidden min-h-[200px] flex flex-col justify-center p-6">
                        <div class="relative z-10">
                            <h3 class="text-xl font-bold mb-3">WhatsApp Kami</h3>
                            <p class="text-gray-400 text-xs mb-4 leading-relaxed">Fast response untuk pertanyaan produk, stok, dan pemesanan custom.</p>
                            <a href="https://wa.me/6289524309299" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147] transition">
                                Chat Sekarang <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                    <div class="relative group overflow-hidden min-h-[200px] flex flex-col justify-center p-6">
                        <div class="relative z-10">
                            <h3 class="text-xl font-bold mb-3">Kirim Pesan</h3>
                            <p class="text-gray-400 text-xs mb-4 leading-relaxed">Tinggalkan pesan Anda melalui form kontak kami untuk kerjasama lebih lanjut.</p>
                            <a href="kontak.php#form" class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147] transition">
                                Isi Form <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mobile Side Drawer (Overlay) -->
    <div id="mobile-overlay" onclick="toggleMobileMenu()" class="fixed inset-0 bg-black/60 z-[110] hidden backdrop-blur-sm"></div>

    <!-- Mobile Side Drawer (Content) -->
    <div id="mobile-drawer" class="fixed top-0 right-0 h-full w-[85%] max-w-[320px] bg-white z-[120] translate-x-full transition-transform duration-300 ease-in-out shadow-2xl overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-8">
                <span class="font-semibold text-[#002147] text-lg">Menu Navigation</span>
                <button type="button" onclick="toggleMobileMenu()" class="text-gray-400 hover:text-[#002147]">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
            </div>

            <nav class="flex flex-col text-gray-800">
                <a href="index.php" class="py-3.5 border-b border-gray-100 font-semibold <?= $halaman == 'index.php' ? 'text-[#FFB606]' : '' ?> hover:text-[#FFB606]">
                    Beranda
                </a>

                <div class="border-b border-gray-100">
                    <button type="button" onclick="toggleMobileSubmenu('sub-katalog')" class="flex items-center justify-between w-full py-3.5 font-semibold text-left outline-none <?= ($halaman == 'katalog.php' || $halaman == 'detail.php') ? 'text-[#FFB606]' : '' ?> hover:text-[#FFB606]">
                        <span>Katalog Produk</span>
                        <i id="icon-sub-katalog" class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform"></i>
                    </button>
                    <div id="sub-katalog" class="hidden pl-4 pb-4 flex flex-col gap-3 text-sm">
                        <a href="katalog.php" class="text-gray-600 hover:text-[#FFB606] italic">Semua Produk</a>
                        <?php
                        $kat_mobile = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                        while ($km = mysqli_fetch_assoc($kat_mobile)):
                        ?>
                            <a href="katalog.php?kategori=<?= urlencode($km['nama_kategori']) ?>" class="text-gray-600 hover:text-[#FFB606]">
                                <?= htmlspecialchars($km['nama_kategori']) ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div class="border-b border-gray-100">
                    <button type="button" onclick="toggleMobileSubmenu('sub-tentang')" class="flex items-center justify-between w-full py-3.5 font-semibold text-left outline-none <?= $halaman == 'tentang.php' ? 'text-[#FFB606]' : '' ?> hover:text-[#FFB606]">
                        <span>Tentang Tefa</span>
                        <i id="icon-sub-tentang" class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform"></i>
                    </button>
                    <div id="sub-tentang" class="hidden pl-4 pb-4 flex flex-col gap-3 text-sm text-gray-600">
                        <a href="tentang.php#tefa" class="hover:text-[#FFB606]">Apa itu Tefa?</a>
                        <a href="tentang.php#visi" class="hover:text-[#FFB606]">Visi & Misi</a>
                        <a href="tentang.php#jurusan" class="hover:text-[#FFB606]">Program Keahlian</a>
                    </div>
                </div>

                <a href="kontak.php" class="py-3.5 border-b border-gray-100 font-semibold <?= $halaman == 'kontak.php' ? 'text-[#FFB606]' : '' ?> hover:text-[#FFB606]">
                    Kontak Kami
                </a>
            </nav>
        </div>
    </div>
</header>

<!-- Script Interaktif UI Header -->
<script>
    function toggleFilterModal() {
        const modal = document.getElementById('filter-modal');
        modal.classList.toggle('hidden');
    }

    function toggleMobileMenu() {
        const drawer = document.getElementById('mobile-drawer');
        const overlay = document.getElementById('mobile-overlay');
        
        if (drawer.classList.contains('translate-x-full')) {
            drawer.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            drawer.classList.add('translate-x-full');
            overlay.classList.add('hidden');
        }
    }

    function toggleMobileSubmenu(id) {
        const submenu = document.getElementById(id);
        const icon = document.getElementById('icon-' + id);
        
        if (submenu) {
            submenu.classList.toggle('hidden');
        }
        if (icon) {
            icon.classList.toggle('rotate-180');
        }
    }
</script>