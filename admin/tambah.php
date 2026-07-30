<?php 
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];
    $kategori = $_POST['kategori'];
    $admin = $_SESSION['admin'];
    
    // Upload multi foto
    $gambar_utama = '';
    $uploaded_fotos = [];
    $foto_files = $_FILES['foto'];
    $error_upload = '';

    if (isset($foto_files['name'][0]) && $foto_files['name'][0] != '') {
        for ($i = 0; $i < count($foto_files['name']); $i++) {
            $upload_res = upload_file($foto_files['name'][$i], $foto_files['tmp_name'][$i]);
            
            if ($upload_res['status']) {
                $nama_file = $upload_res['filename'];
                $uploaded_fotos[] = $nama_file;
                if ($i == 0) $gambar_utama = $nama_file;
            } else {
                $error_upload = $upload_res['message'];
                break;
            }
        }
    }

    if (empty($error_upload)) {
        // Use Prepared Statements to prevent SQL Injection
        $stmt_insert = $conn->prepare("INSERT INTO produk (nama_produk, harga, stok, deskripsi, kategori, gambar) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("siisss", $nama, $harga, $stok, $deskripsi, $kategori, $gambar_utama);
        
        if ($stmt_insert->execute()) {
            $produk_id = $stmt_insert->insert_id;
            
            // Simpan ke produk_foto
            $stmt_foto = $conn->prepare("INSERT INTO produk_foto (produk_id, foto, urutan) VALUES (?, ?, ?)");
            foreach ($uploaded_fotos as $urutan => $foto) {
                $stmt_foto->bind_param("isi", $produk_id, $foto, $urutan);
                $stmt_foto->execute();
            }

            mysqli_query($conn, "INSERT INTO log_aktivitas (admin, aksi) VALUES ('$admin', 'Menambah produk baru: $nama')");
            echo "<script>window.location.href='index.php?success=tambah';</script>";
            exit();
        }
    }
}
?>

<!-- PAGE HEADER -->
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-extrabold text-[#1d2327] ">Tambah Produk</h1>
        <p class="text-sm text-[#646970] font-medium mt-1">Tambahkan item baru ke dalam katalog Tefa Store</p>
    </div>
    <a href="index.php" class="text-xs font-bold text-[#2271b1] hover:underline uppercase  flex items-center gap-2">
        <i class="fa-solid fa-arrow-left-long"></i>
        Kembali ke Daftar
    </a>
</div>

<?php if (isset($error_upload) && !empty($error_upload)): ?>
<div class="mb-8 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl flex items-center gap-3">
    <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
    <span class="text-red-700 text-sm font-bold"><?= $error_upload ?></span>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    
    <!-- LEFT: MAIN PRODUCT INFO -->
    <div class="xl:col-span-2 space-y-8">
        <div class="wp-card p-8">
            <div class="space-y-6">
                <!-- Nama Produk -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-[#1d2327] uppercase ">Nama Produk</label>
                    <input type="text" name="nama_produk" required placeholder="Contoh: Kopi Robusta 250gr"
                        class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all">
                </div>

                <!-- Deskripsi -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-[#1d2327] uppercase ">Deskripsi Lengkap</label>
                    <textarea name="deskripsi" rows="8" placeholder="Tuliskan deskripsi detail produk di sini..."
                        class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all resize-none"></textarea>
                </div>
            </div>
        </div>

        <!-- MULTI PHOTO UPLOAD -->
        <div class="wp-card p-8">
            <h3 class="text-xs font-black text-[#1d2327] uppercase  mb-6 flex items-center gap-2">
                <i class="fa-solid fa-images text-[#2271b1]"></i>
                Galeri Foto Produk
            </h3>
            
            <div class="space-y-6">
                <div class="relative group cursor-pointer" onclick="document.getElementById('input-foto').click()">
                    <div class="border-2 border-dashed border-[#dcdcde] group-hover:border-[#2271b1] rounded-2xl p-10 flex flex-col items-center justify-center transition-all bg-gray-50/50 group-hover:bg-blue-50/30">
                        <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-gray-400 group-hover:text-[#2271b1] mb-4 transition-all group-hover:scale-110">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                        </div>
                        <div class="text-sm font-bold text-[#1d2327]">Pilih Foto Produk</div>
                        <p class="text-[10px] text-gray-400 font-medium mt-1">Bisa pilih lebih dari satu • JPG, PNG, WEBP</p>
                    </div>
                    <input type="file" id="input-foto" name="foto[]" accept="image/*" multiple class="hidden" onchange="previewImages(this)">
                </div>

                <!-- PREVIEW GRID -->
                <div id="preview-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 hidden">
                    <!-- Previews will be injected here -->
                </div>
                <div id="preview-hint" class="text-[10px] text-blue-600 font-bold italic hidden">
                    <i class="fa-solid fa-circle-info"></i> Foto pertama yang Anda pilih akan menjadi foto utama produk.
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: SIDEBAR INFO -->
    <div class="space-y-8">
        <!-- PUBLISH CARD -->
        <div class="wp-card">
            <div class="p-4 border-b border-[#dcdcde] bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-xs font-black text-[#1d2327] uppercase ">Publikasi</h3>
            </div>
            <div class="p-5 space-y-4">
                <button type="submit" class="w-full bg-[#2271b1] text-white py-3 rounded-lg font-bold text-xs hover:bg-[#135e96] transition-all shadow-md shadow-blue-500/10 uppercase  flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Produk
                </button>
                <a href="index.php" class="block w-full text-center py-2.5 text-[10px] font-black text-gray-400 hover:text-red-500 uppercase  transition-colors">Batalkan</a>
            </div>
        </div>

        <!-- INVENTORY CARD -->
        <div class="wp-card p-6 space-y-6">
            <h3 class="text-xs font-black text-[#1d2327] uppercase  flex items-center gap-2">
                <i class="fa-solid fa-warehouse text-[#2271b1]"></i>
                Inventaris
            </h3>
            
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-[#646970] uppercase  ml-1">Kategori</label>
                    <select name="kategori" class="w-full bg-gray-50 border border-[#dcdcde] text-xs font-bold rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10">
                        <?php
                        $kat_query = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                        while ($kat = mysqli_fetch_assoc($kat_query)) {
                            echo '<option value="' . $kat['nama_kategori'] . '">' . $kat['nama_kategori'] . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-[#646970] uppercase  ml-1">Harga (Rp)</label>
                        <input type="number" name="harga" required placeholder="0"
                            class="w-full bg-gray-50 border border-[#dcdcde] text-xs font-bold rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-[#646970] uppercase  ml-1">Stok Awal</label>
                        <input type="number" name="stok" required placeholder="0"
                            class="w-full bg-gray-50 border border-[#dcdcde] text-xs font-bold rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10">
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>

<script>
function previewImages(input) {
    const container = document.getElementById('preview-container');
    const hint = document.getElementById('preview-hint');
    container.innerHTML = '';
    
    if (input.files && input.files[0]) {
        container.classList.remove('hidden');
        hint.classList.remove('hidden');
        
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = "relative aspect-square rounded-xl overflow-hidden border border-gray-100 bg-gray-50 group";
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover">
                    ${index === 0 ? '<span class="absolute top-2 left-2 px-2 py-0.5 bg-[#2271b1] text-white text-[8px] font-black uppercase rounded shadow-sm">Utama</span>' : ''}
                `;
                container.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    } else {
        container.classList.add('hidden');
        hint.classList.add('hidden');
    }
}
</script>

<?php include 'footer.php'; ?>
