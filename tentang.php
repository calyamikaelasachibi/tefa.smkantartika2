<?php include 'includes/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpeg" href="assets/images/logo-smk-antartika-2-sidoarjo.jpg">
    <title>Tentang Tefa - Tefa Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-white">

    <?php $base_url = '';
    include 'includes/header.php';
    include 'includes/sticky-navbar.php'; ?>

    <main class="py-20">
        <div class="container mx-auto px-4 max-w-5xl">
            <!-- HERO SECTION -->
            <section class="grid grid-cols-1 md:grid-cols-1 gap-12 items-center mb-32">
                <div>
                    <h2 class="text-3xl font-bold text-[#002147] mb-6 leading-tight">Kenal Kami Lebih Dekat </h2>
                    <p class="text-gray-600 mb-6">
                        Teaching Factory (Tefa) adalah unit produksi <a href="https://smkantartika2-sda.sch.id" class="underline text-blue-800">SMKS Antartika 2 Sidoarjo</a> tempat guru dan siswa berkolaborasi menghasilkan produk berkualitas. Dengan Tefa, siswa belajar langsung bagaimana dunia industri bekerja sambil menghasilkan produk nyata yang bermanfaat.
                    </p>
                    <p class="text-gray-600">
                        Kami berkomitmen untuk memberikan pengalaman praktis terbaik bagi siswa sekaligus menyediakan produk-produk inovatif bagi masyarakat luas.
                    </p>
                </div>
                <div class="mt-10">
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100 text-[#002147]">
                                <tr>
                                    <th class="py-3 px-4 text-left">NAMA FILE</th>
                                    <th class="py-3 px-4 text-left">KETERANGAN</th>
                                    <th class="py-3 px-4 text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-t">
                                    <td class="py-3 px-4">Company Profile TEFA</td>
                                    <td class="py-3 px-4">Profil lengkap Teaching Factory SMK Antartika 2 Sidoarjo</td>
                                    <td class="py-3 px-4 text-center">
                                        <a href="https://drive.google.com/file/d/1gbjG_FyBqvSKqBFaN5LU54cCtElb_0iB/view?usp=sharing" 
                                           target="_blank"
                                           class="bg-[#002147] text-white px-4 py-2 rounded-md hover:bg-blue-900 transition
                                                  text-xs sm:text-sm text-center inline-flex items-center justify-center gap-2
                                                  w-full sm:w-auto leading-tight">
                                            
                                            <span>⬇</span>
                                            <span>View & Download</span>
                                        
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- VISI MISI -->
            <section class="grid grid-cols-1 md:grid-cols-2 gap-16 mb-32">
                <div class="bg-white p-8 md:p-12 relative overflow-hidden group">
                    <h2 class="text-2xl font-bold text-[#002147] mb-6 uppercase">Visi Kami</h2>
                    <p class="text-gray-600 text-lg italic">
                        "Menjadi unit bisnis sekolah yang kreatif, inovatif, dan berstandar industri dalam menghasilkan produk dan layanan berkualitas."
                    </p>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-[#002147] mb-8 uppercase flex items-center gap-3">
                        Misi Kami
                    </h2>
                    <ul class="space-y-6">
                        <li class="flex items-start gap-4">
                            <p class="text-gray-600">Mengembangkan kreativitas dan inovasi siswa melalui pembelajaran berbasis produksi.</p>
                        </li>
                        <li class="flex items-start gap-4">
                            <p class="text-gray-600">Meningkatkan kompetensi siswa agar mampu menghasilkan produk dan layanan sesuai standar industri.</p>
                        </li>
                        <li class="flex items-start gap-4">
                            <p class="text-gray-600">Menghasilkan produk dan layanan berkualitas yang memiliki nilai guna bagi masyarakat.</p>
                        </li>
                        <li class="flex items-start gap-4">
                            <p class="text-gray-600">Menjalin kerja sama yang kuat dengan dunia usaha dan industri (DUDI).</p>
                        </li>
                        <li class="flex items-start gap-4">
                            <p class="text-gray-600">Menumbuhkan jiwa profesionalisme dan kewirausahaan pada siswa melalui kegiatan Teaching Factory.</p>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- JURUSAN -->
            <section class="mb-32">
                <h2 class="text-2xl font-bold text-[#002147] mb-12 uppercase relative inline-block">
                    Jurusan di SMKS Antartika 2
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
                    <?php
                    $jurusan = [
                        ['name' => 'Teknik Rekayasa Perangkat Lunak', 'icon' => 'fa-code'],
                        ['name' => 'Teknik Mekatronika', 'icon' => 'fa-robot'],
                        ['name' => 'Teknik Komputer dan Jaringan', 'icon' => 'fa-network-wired'],
                        ['name' => 'Perbankan dan Keuangan Mikro', 'icon' => 'fa-piggy-bank'],
                        ['name' => 'Akuntansi dan Keuangan Lembaga', 'icon' => 'fa-calculator'],
                        ['name' => 'Desain Komunikasi Visual', 'icon' => 'fa-palette']
                    ];
                    foreach ($jurusan as $j):
                    ?>
                        <div class="p-8 border border-gray-100 rounded-sm bg-white ">
                            <i class="fa-solid <?= $j['icon'] ?> text-2xl text-gray-300 "></i>
                            <h4 class="text-sm font-semibold text-gray-700 mt-3"><?= $j['name'] ?></h4>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

</body>

</html>