<?php include 'includes/koneksi.php'; ?>
<?php
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: 404.php');
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$data_query = mysqli_query($conn, "SELECT * FROM produk WHERE id = '$id'");
$data = mysqli_fetch_assoc($data_query);

if (!$data) {
    header('Location: 404.php');
    exit();
}

$harga = number_format($data['harga'], 0, ',', '.');
$gambar_utama = $data['gambar'] ? 'assets/images/' . $data['gambar'] : 'https://via.placeholder.com/600x600?text=No+Image';

$foto_query = mysqli_query($conn, "SELECT * FROM produk_foto WHERE produk_id='$id' ORDER BY urutan ASC");
$semua_foto = [];
while ($f = mysqli_fetch_assoc($foto_query)) {
    $semua_foto[] = 'assets/images/' . $f['foto'];
}
if (empty($semua_foto)) {
    $semua_foto[] = $gambar_utama;
}

$related = mysqli_query($conn, "SELECT * FROM produk WHERE kategori='{$data['kategori']}' AND id != '$id' LIMIT 4");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kirim_ulasan'])) {
    $nama = $_POST['nama'];
    $komentar = $_POST['komentar'];
    $bintang = (int)$_POST['bintang'];
    
    $stmt_ulasan = $conn->prepare("INSERT INTO ulasan (produk_id, nama, komentar, bintang) VALUES (?, ?, ?, ?)");
    $stmt_ulasan->bind_param("issi", $id, $nama, $komentar, $bintang);
    
    if ($stmt_ulasan->execute()) {
        header("Location: detail.php?id=$id#ulasan");
        exit();
    }
}

$ulasan_query = mysqli_query($conn, "SELECT * FROM ulasan WHERE produk_id='$id' ORDER BY waktu DESC");
$jumlah_ulasan = mysqli_num_rows($ulasan_query);
$rata_bintang_query = mysqli_query($conn, "SELECT AVG(bintang) as rata FROM ulasan WHERE produk_id='$id'");
$rata_bintang = mysqli_fetch_assoc($rata_bintang_query)['rata'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpeg" href="assets/images/logo-smk-antartika-2-sidoarjo.jpg">
    <title><?= $data['nama_produk'] ?> - Tefa Store</title>
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

        #slider-track {
            transition: transform 0.5s ease-in-out;
        }

        .slider-dot.active {
            border-color: #FFB606;
            background-color: #FFB606;
        }

        #lightbox {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.9);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        #lightbox.active {
            display: flex;
        }

        /* Star Rating Style */
        .rating-stars {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .rating-stars input:checked ~ label,
        .rating-stars label:hover ~ label,
        .rating-stars label:hover {
            color: #FFB606 !important;
        }
    </style>
</head>

<body class="bg-white">

    <?php $base_url = ''; include 'includes/header_detail.php'; ?>

    <nav class="bg-gray-50 py-4 border-b border-gray-100">
        <div class="container mx-auto px-4">
            <div class="flex items-center gap-2 text-xs text-gray-400 uppercase ">
                <a href="index.php" class="hover:text-[#FFB606]">Home</a>
                <i class="fa-solid fa-chevron-right text-[8px]"></i>
                <a href="katalog.php" class="hover:text-[#FFB606]">Katalog</a>
                <i class="fa-solid fa-chevron-right text-[8px]"></i>
                <a href="katalog.php?kategori=<?= urlencode($data['kategori']) ?>" class="hover:text-[#FFB606]"><?= $data['kategori'] ?></a>
                <i class="fa-solid fa-chevron-right text-[8px]"></i>
                <span class="text-[#002147] font-bold truncate max-w-[150px] md:max-w-none"><?= $data['nama_produk'] ?></span>
            </div>
        </div>
    </nav>

    <main class="py-12 md:py-20">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-12 lg:gap-20 mb-20">

                <div class="lg:w-1/2">
                    <div class="relative group">
                        <div class="aspect-square overflow-hidden bg-gray-50 border border-gray-100 relative rounded-sm">
                            <div id="slider-track" class="flex h-full">
                                <?php foreach ($semua_foto as $foto): ?>
                                    <div class="min-w-full h-full flex items-center justify-center p-4">
                                        <img src="<?= $foto ?>" alt="<?= $data['nama_produk'] ?>" class="max-w-full max-h-full object-contain cursor-zoom-in" onclick="openLightbox('<?= $foto ?>')">
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if (count($semua_foto) > 1): ?>
                                <button onclick="slideMove(-1)" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/90 shadow-sm rounded-full flex items-center justify-center text-[#002147] hover:bg-[#FFB606] hover:text-white transition-all z-10">
                                    <i class="fa-solid fa-chevron-left text-sm"></i>
                                </button>
                                <button onclick="slideMove(1)" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/90 shadow-sm rounded-full flex items-center justify-center text-[#002147] hover:bg-[#FFB606] hover:text-white transition-all z-10">
                                    <i class="fa-solid fa-chevron-right text-sm"></i>
                                </button>
                            <?php endif; ?>
                        </div>

                        <?php if (count($semua_foto) > 1): ?>
                            <div class="flex gap-3 mt-4 overflow-x-auto no-scrollbar pb-2">
                                <?php foreach ($semua_foto as $i => $foto): ?>
                                    <button onclick="slideTo(<?= $i ?>)" class="slider-dot-btn flex-shrink-0 w-20 aspect-square border-2 border-gray-100 hover:border-[#FFB606] transition-all overflow-hidden rounded-sm <?= $i === 0 ? 'border-[#FFB606]' : '' ?>">
                                        <img src="<?= $foto ?>" class="w-full h-full object-cover">
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="lg:w-1/2">
                    <div class="mb-6">
                        <span class="text-[10px] font-bold mb-3 block"><?= $data['kategori'] ?></span>
                        <h1 class="text-2xl md:text-4xl font-semibold text-[#002147] mb-4"><?= $data['nama_produk'] ?></h1>

                        <div class="flex items-center gap-6">
                            <?php if ($jumlah_ulasan > 0): ?>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-0.5 text-yellow-400">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fa-solid fa-star text-xs <?= $i <= round($rata_bintang) ? '' : 'text-gray-200' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700"><?= number_format($rata_bintang, 1) ?></span>
                                    <span class="text-xs text-gray-400 font-medium">(<?= $jumlah_ulasan ?> Ulasan)</span>
                                </div>
                            <?php endif; ?>
                            <div class="text-xs text-gray-400 font-medium">Terjual <span class="text-gray-700 font-bold"><?= $data['terjual'] ?></span></div>
                        </div>
                    </div>

                    <div class="rounded-sm mb-5">
                        <div class="flex items-baseline gap-2 text-[#002147] mb-2">
                            <span class="text-lg font-bold">Rp</span>
                            <span class="text-4xl font-semibold "><?= $harga ?></span>
                        </div>
                    </div>

                    <div class="block gap-8 mb-10">
                        <div class="flex items-center space-x-1">
                            <h4 class="text-sm ext-gray-400 ">Stok :</h4>
                            <p class="text-sm text-[#002147]"><?= $data['stok'] > 0 ? $data['stok'] : '<span class="text-red-500">Habis</span>' ?></p>
                        </div>
                        <div class="flex items-center space-x-1">
                            <h4 class="text-sm extext-gray-400 ">Lokasi :</h4>
                            <p class="text-sm text-[#002147]">Sidoarjo</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 mb-12">
                        <a href="https://wa.me/6289524309299?text=Halo%2C%20saya%20tertarik%20memesan%20produk%20*<?= urlencode($data['nama_produk']) ?>*%20di%20Tefa%20Store." target="_blank" class="flex-1 bg-[#002147] text-white text-center py-5 rounded-sm font-bold uppercase  text-xs hover:bg-[#FFB606] hover:text-[#002147] transition-all shadow-lg flex items-center justify-center gap-3">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                            Pesan Sekarang
                        </a>
                        <button onclick="copyLink(this)" class="px-8 py-5 border border-gray-200 rounded-sm text-gray-400 hover:text-[#002147] hover:border-[#002147] transition-all">
                            <i class="fa-solid fa-share-nodes"></i>
                        </button>
                    </div>

                    <div class="mt-12 pt-12 border-t border-gray-100">
                        <h3 class="text-lg font-semibold text-[#002147] mb-8 flex items-center gap-3">
                            Form pemesanan
                        </h3>
                        <div class="p-2">
                            <div class="space-y-4">
                                <div>
                                    <label class="text-[10px] font-bold text-gray-400 mb-2 block">Nama Lengkap</label>
                                    <input type="text" id="order_name" placeholder="Masukkan nama Anda..." class="w-full p-4 border border-gray-200 rounded-sm text-sm focus:outline-none focus:border-[#FFB606] transition-all bg-white">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 mb-2 block">Jumlah Pesanan</label>
                                        <input type="number" id="order_qty" value="1" min="1" class="w-full p-4 border border-gray-200 rounded-sm text-sm focus:outline-none focus:border-[#FFB606] transition-all bg-white">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 mb-2 block">Metode Pembayaran</label>
                                        <select id="order_payment" class="w-full p-4 border border-gray-200 rounded-sm text-sm focus:outline-none focus:border-[#FFB606] transition-all bg-white appearance-none cursor-pointer">
                                            <option value="Tunai / COD">Tunai / COD</option>
                                            <option value="Transfer Bank">Transfer Bank</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-gray-400 mb-2 block">Catatan Tambahan</label>
                                    <textarea id="order_notes" placeholder="Contoh: Ukuran L, Warna Biru, dll..." class="w-full p-4 border border-gray-200 rounded-sm text-sm focus:outline-none focus:border-[#FFB606] transition-all bg-white h-24"></textarea>
                                </div>
                                <button onclick="sendOrder()" class="w-full bg-[#FFB606] text-[#002147] py-5 rounded-sm font-bold uppercase text-xs hover:bg-[#002147] hover:text-white transition-all shadow-md flex items-center justify-center gap-3">
                                    <i class="fa-solid fa-paper-plane"></i>
                                    Kirim Form Pesanan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section id="ulasan" class="py-20 border-t border-gray-100">
                <div class="flex flex-col lg:flex-row gap-16">
                    <div class="lg:w-1/3">
                        <h2 class="text-2xl font-semibold text-[#002147] mb-8 ">Ulasan Pembeli</h2>
                        <div class=" p-10 text-center">
                            <h3 class="text-6xl font-semibold text-[#002147] mb-4"><?= number_format($rata_bintang, 1) ?></h3>
                            <div class="flex justify-center gap-1 text-yellow-400 mb-4">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fa-solid fa-star text-lg <?= $i <= round($rata_bintang) ? '' : 'text-gray-200' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="text-xs text-gray-400 font-bold ">Berdasarkan <?= $jumlah_ulasan ?> ulasan</p>
                        </div>

                        <div class="mt-12">
                            <h4 class="text-sm font-bold text-[#002147] uppercase  mb-6 border-b border-gray-100 pb-3">Tulis Ulasan Kamu</h4>
                            <form method="POST" class="space-y-4">
                                <input type="text" name="nama" placeholder="Nama lengkap..." required class="w-full p-4 border border-gray-100 rounded-sm text-sm focus:outline-none focus:border-[#FFB606] bg-gray-50 transition-all">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase  mb-3">Rating Bintang</p>
                                    <div class="rating-stars text-2xl gap-2">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <input type="radio" name="bintang" id="b<?= $i ?>" value="<?= $i ?>" required class="hidden">
                                            <label for="b<?= $i ?>" class="cursor-pointer text-gray-200 transition-colors">★</label>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <textarea name="komentar" placeholder="Bagaimana pengalaman belanja kamu?..." required class="w-full p-4 border border-gray-100 rounded-sm text-sm focus:outline-none focus:border-[#FFB606] bg-gray-50 h-32 transition-all"></textarea>
                                <button type="submit" name="kirim_ulasan" class="w-full bg-[#002147] text-white py-4 rounded-sm font-bold uppercase  text-xs hover:bg-[#FFB606] hover:text-[#002147] transition-all shadow-md">Kirim Ulasan</button>
                            </form>
                        </div>
                    </div>

                    <div class="lg:w-2/3">
                        <div class="space-y-8">
                            <?php if ($jumlah_ulasan > 0):
                                while ($ul = mysqli_fetch_assoc($ulasan_query)):
                            ?>
                                    <div class="border-b border-gray-50 pb-8">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-[#002147] font-bold text-xs">
                                                    <?= strtoupper(substr($ul['nama'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-bold text-[#002147] leading-none mb-1"><?= htmlspecialchars($ul['nama']) ?></h4>
                                                    <p class="text-[10px] text-gray-400"><?= date('d F Y', strtotime($ul['waktu'])) ?></p>
                                                </div>
                                            </div>
                                            <div class="flex gap-0.5 text-[#FFB606]">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fa-solid fa-star text-[10px] <?= $i <= $ul['bintang'] ? '' : 'text-gray-100' ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <p class="text-gray-600 text-sm leading-relaxed italic">"<?= htmlspecialchars($ul['komentar']) ?>"</p>
                                    </div>
                                <?php endwhile;
                            else: ?>
                                <div class="py-20 text-center">
                                    <i class="fa-regular fa-comment-dots text-4xl text-gray-200 mb-4 block"></i>
                                    <p class="text-gray-400 text-sm">Belum ada ulasan. Jadi yang pertama!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>

            <?php if (mysqli_num_rows($related) > 0): ?>
                <section class="py-20 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-12">
                        <h2 class="text-2xl font-semibold text-[#002147] ">Produk Serupa</h2>
                        <a href="katalog.php?kategori=<?= urlencode($data['kategori']) ?>" class="text-xs font-semibold hover:underline uppercase ">Lihat Semua</a>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
                        <?php while ($rel = mysqli_fetch_assoc($related)):
                            $rel_harga = number_format($rel['harga'], 0, ',', '.');
                            $rel_gambar = $rel['gambar'] ? 'assets/images/' . $rel['gambar'] : 'https://placehold.co/400x400?text=No+Image';
                        ?>
                            <a href="detail.php?id=<?= $rel['id'] ?>" class="group flex flex-col h-full bg-white border border-gray-100">
                                <div class="relative aspect-square overflow-hidden bg-gray-50">
                                    <img src="<?= $rel_gambar ?>" alt="<?= $rel['nama_produk'] ?>" class="w-full h-full object-cover">
                                </div>
                                <div class="p-4 flex flex-col flex-1">
                                    <h3 class="text-xs md:text-sm text-gray-800 line-clamp-2 leading-snug font-medium h-10"><?= $rel['nama_produk'] ?></h3>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </main>

    <div id="lightbox" onclick="closeLightbox()">
        <div class="absolute top-6 right-6 text-white cursor-pointer hover:text-[#FFB606] transition-colors">
            <i class="fa-solid fa-xmark text-3xl"></i>
        </div>
        <img src="" alt="" id="lightbox-img" class="max-w-[90%] max-h-[90%] object-contain shadow-2xl">
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        let currentSlide = 0;
        const totalSlides = <?= count($semua_foto) ?>;
        const track = document.getElementById('slider-track');
        const dots = document.querySelectorAll('.slider-dot-btn');

        function updateSlider() {
            if (track) track.style.transform = `translateX(-${currentSlide * 100}%)`;
            dots.forEach((d, i) => {
                if (i === currentSlide) {
                    d.classList.add('border-[#FFB606]');
                } else {
                    d.classList.remove('border-[#FFB606]');
                }
            });
        }

        function slideMove(dir) {
            currentSlide = (currentSlide + dir + totalSlides) % totalSlides;
            updateSlider();
        }

        function slideTo(index) {
            currentSlide = index;
            updateSlider();
        }

        function openLightbox(src) {
            const lb = document.getElementById('lightbox');
            const lbImg = document.getElementById('lightbox-img');
            lbImg.src = src;
            lb.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            const lb = document.getElementById('lightbox');
            lb.classList.remove('active');
            document.body.style.overflow = '';
        }

        function copyLink(btn) {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                const icon = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check text-green-500"></i>';
                setTimeout(() => {
                    btn.innerHTML = icon;
                }, 2000);
            });
        }

        function sendOrder() {
            const name = document.getElementById('order_name').value;
            const qty = document.getElementById('order_qty').value;
            const payment = document.getElementById('order_payment').value;
            const notes = document.getElementById('order_notes').value;
            const product = "<?= addslashes($data['nama_produk']) ?>";
            const price = "<?= $harga ?>";

            if (!name) {
                alert('Mohon isi nama lengkap Anda.');
                document.getElementById('order_name').focus();
                return;
            }

            const text = `*FORM PESANAN TEFA STORE*\n\n` +
                `*Produk:* ${product}\n` +
                `*Harga:* Rp ${price}\n` +
                `--------------------------\n` +
                `*Nama Pembeli:* ${name}\n` +
                `*Jumlah:* ${qty} Pcs\n` +
                `*Pembayaran:* ${payment}\n` +
                `*Catatan:* ${notes || '-'}\n\n` +
                `Mohon segera dikonfirmasi ya, terima kasih!`;

            const waUrl = `https://wa.me/6289524309299?text=${encodeURIComponent(text)}`;
            window.open(waUrl, '_blank');
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') slideMove(-1);
            if (e.key === 'ArrowRight') slideMove(1);
        });
    </script>

</body>

</html>
