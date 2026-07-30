<?php
$halaman = basename($_SERVER['PHP_SELF']);
$search_action = (isset($base_url) ? $base_url : '') . 'katalog.php';
include_once 'koneksi.php';
?>

<header class="w-full bg-white font-sans sticky top-0 lg:relative z-[100] shadow-sm">
    <!-- Top Bar (Hidden on Mobile/Tablet) -->
    <div class="bg-gray-100 py-2 border-b border-gray-200">
        <div class="container mx-auto px-4 flex justify-end items-center text-sm text-gray-600">
            <a href="https://wa.me/6289524309299" target="_blank"
               class="fixed bottom-6 right-6 z-50 bg-green-500 hover:bg-green-600 text-white 
                      w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition">
                
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
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#002147]">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <input type="text" name="search" placeholder="Cari produk..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                        class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-[#002147] bg-gray-50 focus:bg-white">
                </form>
            </div>

            <!-- Filter / Elements Desktop -->
        <div class="flex items-center gap-6">
            <button onclick="toggleFilterModal()" class="flex items-center gap-2 text-gray-700 hover:text-[#002147] font-semibold transition-colors">
                <i class="fa-solid fa-sliders text-xl"></i>
                <span>Filter</span>
            </button>
        </div>
    </div>

    <!-- Filter Modal -->
    <div id="filter-modal" class="fixed inset-0 z-[150] hidden">
        <div class="absolute inset-0 bg-[#002147]/60 backdrop-blur-sm" onclick="toggleFilterModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-xl font-bold text-[#002147]">Filter Produk</h3>
                <button onclick="toggleFilterModal()" class="text-gray-400 hover:text-[#002147]">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            
            <form action="<?= $search_action ?>" method="GET" class="p-6 space-y-6">
                <!-- Keep existing search query if any -->
                <?php if(isset($_GET['search'])): ?>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search']) ?>">
                <?php endif; ?>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 uppercase ">Kategori</label>
                    <select name="kategori" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FFB606]">
                        <option value="">Semua Kategori</option>
                        <?php
                        $kat_filter = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                        while($kf = mysqli_fetch_assoc($kat_filter)):
                            $selected = (isset($_GET['kategori']) && $_GET['kategori'] == $kf['nama_kategori']) ? 'selected' : '';
                        ?>
                        <option value="<?= $kf['nama_kategori'] ?>" <?= $selected ?>><?= $kf['nama_kategori'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Rentang Harga -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 uppercase ">Rentang Harga (Rp)</label>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="number" name="min_price" placeholder="Min" value="<?= isset($_GET['min_price']) ? $_GET['min_price'] : '' ?>" 
                            class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FFB606]">
                        <input type="number" name="max_price" placeholder="Max" value="<?= isset($_GET['max_price']) ? $_GET['max_price'] : '' ?>" 
                            class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FFB606]">
                    </div>
                </div>

                <!-- Urutan -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 uppercase ">Urutkan Berdasarkan</label>
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
                    <button type="reset" onclick="window.location.href='<?= $search_action ?>'" class="flex-1 py-3 px-6 border border-gray-200 rounded-xl font-bold text-gray-500 hover:bg-gray-50 transition-colors">Reset</button>
                    <button type="submit" class="flex-1 py-3 px-6 bg-[#FFB606] text-[#002147] rounded-xl font-bold hover:bg-yellow-500 transition-colors shadow-lg shadow-yellow-400/20">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

        <!-- Layout Mobile/Tablet (Tokopedia/Shopee Style) -->
        <div class="lg:hidden flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <form method="GET" action="<?= $search_action ?>" class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </div>
                    <input type="text" name="search" placeholder="Cari produk di Tefa Store..." value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>"
                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none bg-gray-50 text-sm shadow-inner">
                </form>
                <!-- Burger Menu Icon -->
                <button onclick="toggleMobileMenu()" class="text-[#002147] p-2 focus:outline-none">
                    <i class="fa-solid fa-bars text-3xl"></i>
                </button>
            </div>

            <!-- Search Bar Mobile/Tablet -->
            <div class="w-full">

            </div>
        </div>
    </div>

    <!-- Desktop Navigation Bar (Hidden on Mobile/Tablet) -->
    <div class="border-gray-100 hidden lg:block">
        <div class="container mx-auto px-4">
            <nav class="flex justify-start">
                <a href="<?= (isset($base_url) ? $base_url : '') ?>index.php"
                    class="flex-1 max-w-[250px] border-x border-gray-100 py-4 px-6 relative
                          <?= $halaman == 'index.php' ? 'bg-[#002147] text-white border-t-4 border-t-[#FFB606]' : 'bg-white text-gray-700 hover:bg-gray-50' ?>">
                    <div class="flex items-center gap-4">
                        <div class="text-2xl <?= $halaman == 'index.php' ? 'text-[#FFB606]' : 'text-gray-400' ?>">
                            <i class="fa-solid fa-gear text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-xs uppercase  leading-none mb-1">Beranda</div>
                            <div class="text-[10px] opacity-70">Halaman Utama</div>
                        </div>
                    </div>
                </a>

                <a href="<?= (isset($base_url) ? $base_url : '') ?>katalog.php"
                    class="flex-1 max-w-[250px] border-r border-gray-100 py-4 px-6 relative
                          <?= $halaman == 'katalog.php' ? 'bg-[#002147] text-white border-t-4 border-t-[#FFB606]' : 'bg-white text-gray-700 hover:bg-gray-50' ?>">
                    <div class="flex items-center gap-4">
                        <div class="text-2xl <?= $halaman == 'katalog.php' ? 'text-[#FFB606]' : 'text-gray-400' ?>">
                            <i class="fa-solid fa-box text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-xs uppercase  leading-none mb-1">Katalog</div>
                            <div class="text-[10px] opacity-70">Produk Tefa</div>
                        </div>
                    </div>
                </a>

                <a href="<?= (isset($base_url) ? $base_url : '') ?>tentang.php"
                    class="flex-1 max-w-[250px] border-r border-gray-100 py-4 px-6 relative
                          <?= $halaman == 'tentang.php' ? 'bg-[#002147] text-white border-t-4 border-t-[#FFB606]' : 'bg-white text-gray-700 hover:bg-gray-50' ?>">
                    <div class="flex items-center gap-4">
                        <div class="text-2xl <?= $halaman == 'tentang.php' ? 'text-[#FFB606]' : 'text-gray-400' ?>">
                            <i class="fa-solid fa-circle-info text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-xs uppercase  leading-none mb-1">Tentang</div>
                            <div class="text-[10px] opacity-70">Mengenal Tefa</div>
                        </div>
                    </div>
                </a>

                <a href="<?= (isset($base_url) ? $base_url : '') ?>kontak.php"
                    class="flex-1 max-w-[250px] border-r border-gray-100 py-4 px-6 relative
                          <?= $halaman == 'kontak.php' ? 'bg-[#002147] text-white border-t-4 border-t-[#FFB606]' : 'bg-white text-gray-700 hover:bg-gray-50' ?>">
                    <div class="flex items-center gap-4">
                        <div class="text-2xl <?= $halaman == 'kontak.php' ? 'text-[#FFB606]' : 'text-gray-400' ?>">
                            <i class="fa-solid fa-phone text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-xs uppercase  leading-none mb-1">Kontak</div>
                            <div class="text-[10px] opacity-70">Hubungi Kami</div>
                        </div>
                    </div>
                </a>
            </nav>
        </div>
    </div>

    <!-- Mega Menu Section (Visible only on Large Desktop) -->
    <div class="bg-[#002147] text-white border-b border-blue-900 min-h-[200px] hidden lg:flex items-center">
        <div class="container mx-auto">

            <?php if ($halaman == 'index.php'): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 h-full">
                    <div class="relative group border-r border-blue-900 overflow-hidden min-h-[200px] flex flex-col justify-center p-4">
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-30 bg-cover bg-center"></div>
                        <div class="relative z-10">
                            <h3 class="text-xl font-bold mb-4">Tefa Store</h3>
                            <p class="text-gray-400 text-xs mb-4 leading-relaxed">Pusat produk inovatif hasil karya siswa/siswi SMKS Antartika 2 Sidoarjo.</p>
                            <a href="katalog.php" class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147]">
                                Lihat Produk <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                    <div class="relative group border-r border-blue-900 overflow-hidden min-h-[200px] flex flex-col justify-center p-4">
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-30 bg-cover bg-center"></div>
                        <div class="relative z-10">

                            <h3 class="text-xl font-bold mb-4">Produk Unggulan</h3>
                            <p class="text-gray-400 text-xs mb-4 leading-relaxed">Cek produk terlaris dan paling direkomendasikan bulan ini.</p>
                            <a href="katalog.php?sort=terlaris" class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147]">
                                Cek Rekomendasi <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                    <div class="relative group border-r border-blue-900 overflow-hidden min-h-[200px] flex flex-col justify-center p-4">
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-30 bg-cover bg-center"></div>
                        <div class="relative z-10">
                            <h3 class="text-xl font-bold mb-4">Promo Spesial</h3>
                            <p class="text-gray-400 text-xs mb-4 leading-relaxed">Dapatkan penawaran harga terbaik untuk berbagai kategori produk.</p>
                            <a href="katalog.php?sort=termurah" class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147]">
                                Lihat Promo <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>

            <?php elseif ($halaman == 'katalog.php' || $halaman == 'detail.php'): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 h-full">
                    <?php
                    $kat_q = mysqli_query($conn, "SELECT k.*, (SELECT gambar FROM produk WHERE kategori = k.nama_kategori LIMIT 1) as contoh_gambar FROM kategori k LIMIT 8");
                    while ($k = mysqli_fetch_assoc($kat_q)):
                        $bg_img = $k['contoh_gambar'] ? 'assets/images/' . $k['contoh_gambar'] : '';
                    ?>
                        <div class="relative group border-r border-b border-blue-900 overflow-hidden min-h-[200px] flex flex-col justify-center p-8">
                            <?php if ($bg_img): ?>
                                <div class="absolute inset-0 opacity-0 group-hover:opacity-40 bg-cover bg-center" style="background-image: url('<?= $bg_img ?>')"></div>
                            <?php endif; ?>

                            <div class="relative z-10">
                                <h3 class="text-lg font-bold mb-6"><?= $k['nama_kategori'] ?></h3>
                                <a href="katalog.php?kategori=<?= urlencode($k['nama_kategori']) ?>"
                                    class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147] group/btn">
                                    Semua produk kategori
                                    <i class="fa-solid fa-chevron-right text-[10px] group-hover/btn:translate-x-1"></i>
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

            <?php elseif ($halaman == 'tentang.php'): ?>
                <div class="grid grid-cols-1 md:grid-cols-3 h-full">
                    <div class="relative group border-r border-blue-900 overflow-hidden min-h-[200px] flex flex-col justify-center p-8">
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-30 bg-cover bg-center"></div>
                        <div class="relative z-10">
                            <h3 class="text-xl font-bold mb-4">Tentang Kami</h3>
                            <p class="text-gray-400 text-xs mb-6 leading-relaxed">Menjadi pusat inovasi dan kreativitas siswa SMKS Antartika 2 Sidoarjo yang berdaya saing global.</p>
                            <a href="tentang.php#visi" class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147]">
                                Selengkapnya <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>

            <?php elseif ($halaman == 'kontak.php'): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 h-full">
                    <div class="relative group border-r border-blue-900 overflow-hidden min-h-[200px] flex flex-col justify-center p-8">
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-30 bg-cover bg-center"></div>
                        <div class="relative z-10">
                            <h3 class="text-xl font-bold mb-4">WhatsApp Kami</h3>
                            <p class="text-gray-400 text-xs mb-6 leading-relaxed">Fast response untuk pertanyaan produk, stok, dan pemesanan custom.</p>
                            <a href="https://wa.me/6289524309299" target="_blank" class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147]">
                                Chat Sekarang <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                    <div class="relative group overflow-hidden min-h-[200px] flex flex-col justify-center p-8">
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-30 bg-cover bg-center"></div>
                        <div class="relative z-10">
                            <h3 class="text-xl font-bold mb-4">Kirim Pesan</h3>
                            <p class="text-gray-400 text-xs mb-6 leading-relaxed">Tinggalkan pesan Anda melalui form kontak kami untuk kerjasama lebih lanjut.</p>
                            <a href="kontak.php#form" class="inline-flex items-center gap-3 px-4 py-2 border border-blue-800 rounded-md text-xs font-medium hover:bg-[#FFB606] hover:border-[#FFB606] hover:text-[#002147]">
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
        <div class="p-8">
            <div class="flex items-center justify-between mb-10">
                <div class="flex items-center gap-3">
                    <span class="font-semibold text-[#002147]">Menu</span>
                </div>
                <button onclick="toggleMobileMenu()" class="text-gray-400 hover:text-[#002147]">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
            </div>

            <nav class="flex flex-col text-gray-800">
                <a href="index.php" class="py-4 border-b border-gray-100 text-lg font-semibold <?= $halaman == 'index.php' ? 'text-[#FFB606]' : '' ?> hover:text-[#FFB606]">
                    Beranda
                </a>

                <div class="border-b border-gray-100">
                    <button onclick="toggleMobileSubmenu('sub-katalog')" class="flex items-center justify-between w-full py-4 text-lg font-semibold text-left outline-none <?= ($halaman == 'katalog.php' || $halaman == 'detail.php') ? 'text-[#FFB606]' : '' ?> hover:text-[#FFB606]">
                        <span>Katalog Produk</span>
                        <i id="icon-katalog" class="fa-solid fa-chevron-down text-xs text-gray-400"></i>
                    </button>
                    <div id="sub-katalog" class="hidden pl-4 pb-4 flex flex-col gap-4">
                        <a href="katalog.php" class="text-gray-600 hover:text-[#FFB606] italic">Semua Produk</a>
                        <?php
                        $kat_mobile = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                        while ($km = mysqli_fetch_assoc($kat_mobile)):
                        ?>
                            <a href="katalog.php?kategori=<?= urlencode($km['nama_kategori']) ?>" class="text-gray-600 hover:text-[#FFB606]">
                                <?= $km['nama_kategori'] ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div class="border-b border-gray-100">
                    <button onclick="toggleMobileSubmenu('sub-tentang')" class="flex items-center justify-between w-full py-4 text-lg font-semibold text-left outline-none <?= $halaman == 'tentang.php' ? 'text-[#FFB606]' : '' ?> hover:text-[#FFB606]">
                        <span>Tentang Tefa</span>
                        <i id="icon-tentang" class="fa-solid fa-chevron-down text-xs text-gray-400"></i>
                    </button>
                    <div id="sub-tentang" class="hidden pl-4 pb-4 flex flex-col gap-4 text-gray-600">
                        <a href="tentang.php#tefa" class="hover:text-[#FFB606]">Apa itu Tefa?</a>
                        <a href="tentang.php#visi" class="hover:text-[#FFB606]">Visi & Misi</a>
                        <a href="tentang.php#jurusan" class="hover:text-[#FFB606]">Program Keahlian (Jurusan)</a>
                    </div>
                </div>

                <a href="kontak.php" class="py-4 border-b border-gray-100 text-lg font-semibold <?= $halaman == 'kontak.php' ? 'text-[#FFB606]' : '' ?> hover:text-[#FFB606]">
                    Kontak Kami
                </a>
            </nav>
        </div>
    </div>
</header>