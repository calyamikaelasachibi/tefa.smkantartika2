<?php include 'includes/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpeg" href="assets/images/logo-smk-antartika-2-sidoarjo.jpg">
    <title>Kontak - Tefa Store dibuat oleh arsy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-white">

    <?php $base_url = ''; include 'includes/header.php'; include 'includes/sticky-navbar.php'; ?>

    <main class="py-20">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
                
                <!-- CONTACT INFO -->
                <div class="space-y-12">
                    <div>
                        <h2 class="text-2xl font-bold text-[#002147] mb-8 uppercase relative inline-block">
                            Informasi Kontak
                        </h2>
                        <p class="text-gray-500 mb-10 leading-relaxed">Kami siap membantu Anda. Silakan hubungi kami melalui saluran komunikasi di bawah ini atau kunjungi lokasi kami langsung.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Address -->
                            <div class="flex gap-5 group">
                                <div class="w-12 h-12 rounded-sm flex items-center justify-center ">
                                    <i class="fa-solid fa-location-dot text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#002147] uppercase mb-1">Alamat</h4>
                                    <p class="text-sm text-gray-500 leading-relaxed">Jl. Siwalanpanji, Buduran, Sidoarjo, Jawa Timur</p>
                                </div>
                            </div>

                            <!-- WhatsApp -->
                            <div class="flex gap-5 group">
                                <div class="w-12 h-12 rounded-sm flex items-center justify-center ">
                                    <i class="fa-brands fa-whatsapp text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#002147] uppercase mb-1">WhatsApp</h4>
                                    <p class="text-sm text-gray-500 leading-relaxed">+62 895-2430-9299</p>
                                </div>
                            </div>

                            <!-- Jam Operasional -->
                            <div class="flex gap-5 group">
                                <div class="w-12 h-12 rounded-sm flex items-center justify-center ">
                                    <i class="fa-solid fa-clock text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#002147] uppercase mb-1">Jam Operasional</h4>
                                    <p class="text-sm text-gray-500 leading-relaxed">Senin - Jumat: 08.00 - 15.00 WIB</p>
                                </div>
                            </div>

                            <!-- Email/Web -->
                            <div class="flex gap-5 group">
                                <div class="w-12 h-12 rounded-sm flex items-center justify-center ">
                                    <i class="fa-solid fa-school text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#002147] uppercase mb-1">Lembaga</h4>
                                    <p class="text-sm text-gray-500 leading-relaxed">SMKS Antartika 2 Sidoarjo</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-gray-100">
                        <a href="https://wa.me/6289524309299" target="_blank" class="inline-flex items-center justify-center gap-3 bg-[#FFB606] text-white px-10 py-4 rounded-sm font-bold hover:bg-yellow-500 transition-all uppercase">
                            Chat WhatsApp
                        </a>
                    </div>
                </div>

                <!-- MAPS -->
                <div class="h-full min-h-[450px] relative group">
                    <div class="absolute -inset-4 "></div>
                    <div class="w-full h-full bg-white rounded-sm overflow-hidden shadow-sm border border-gray-100">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3956.286436076065!2d112.72316517532182!3d-7.433522892577244!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7e6a886bb12af%3A0xfd09f08967a2d26f!2sSMK%20ANTARTIKA%202%20SIDOARJO!5e0!3m2!1sid!2sid!4v1771905919976!5m2!1sid!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

</body>
</html>
