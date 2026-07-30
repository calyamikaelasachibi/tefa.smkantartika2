<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpeg" href="assets/images/logo-smk-antartika-2-sidoarjo.jpg">
    <title>404 - Halaman Tidak Ditemukan | Tefa Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

    <?php 
    $base_url = ''; 
    include 'includes/sticky-navbar.php';
    ?>

    <!-- 404 CONTENT -->
    <main class="flex-grow flex items-center justify-center py-20 px-4">
        <div class="max-w-2xl w-full text-center">
            <!-- Animated Icon -->
            <div class="relative mb-12 inline-block">
                <div class="text-[150px] md:text-[200px] font-black text-gray-500/50 leading-none select-none">404</div>
            </div>

            <!-- Text Content -->
            <div class="space-y-6">
                <h1 class="text-3xl md:text-4xl font-extrabold text-[#002147] ">Waduh! Halaman Hilang...</h1>
                <p class="text-gray-500 text-lg max-w-md mx-auto leading-relaxed">
                    Sepertinya halaman yang kamu cari tidak ada
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-8">
                    <a href="index.php" class="w-full sm:w-auto px-8 py-4 bg-[#002147] text-white rounded-md font-bold text-sm hover:bg-blue-900 transition-all  uppercase  flex items-center justify-center gap-3">
                        <i class="fa-solid fa-house"></i>
                        Ke Beranda
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="mt-16 pt-8 border-t border-gray-200">
                <p class="text-xs font-bold text-gray-400 uppercase  mb-6">Mungkin kamu mencari ini?</p>
                <div class="flex flex-wrap justify-center gap-x-8 gap-y-4">
                    <a href="tentang.php" class="text-sm font-bold text-gray-600 hover:text-[#002147] transition-colors">Tentang Tefa</a>
                    <a href="kontak.php" class="text-sm font-bold text-gray-600 hover:text-[#002147] transition-colors">Hubungi Kami</a>
                    <a href="katalog.php?sort=terlaris" class="text-sm font-bold text-gray-600 hover:text-[#002147] transition-colors">Produk Terlaris</a>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/script.js"></script>

</body>
</html>
