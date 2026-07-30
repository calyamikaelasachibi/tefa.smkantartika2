<?php 
include 'header.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$data_query = mysqli_query($conn, "SELECT * FROM produk WHERE id = '$id'");
$data = mysqli_fetch_assoc($data_query);

if (!$data) {
    header('Location: index.php');
    exit();
}

// SUCCESS MESSAGES
$pesan = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'hapus_foto') $pesan = "Foto produk berhasil dihapus!";
}

// ACTION: HAPUS FOTO
if (isset($_GET['hapus_foto'])) {
    $foto_id = (int)$_GET['hapus_foto'];
    $foto_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM produk_foto WHERE id='$foto_id' AND produk_id='$id'"));
    
    if ($foto_data) {
        $file_path = '../assets/images/' . $foto_data['foto'];
        if (file_exists($file_path)) unlink($file_path);
        mysqli_query($conn, "DELETE FROM produk_foto WHERE id='$foto_id'");

        if ($foto_data['foto'] == $data['gambar']) {
            $sisa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM produk_foto WHERE produk_id='$id' ORDER BY urutan ASC LIMIT 1"));
            $gambar_baru = $sisa ? $sisa['foto'] : '';
            mysqli_query($conn, "UPDATE produk SET gambar='$gambar_baru' WHERE id='$id'");
        }
        
        mysqli_query($conn, "INSERT INTO log_aktivitas (admin, aksi) VALUES ('{$_SESSION['admin']}', 'Menghapus salah satu foto produk: {$data['nama_produk']}')");
    }
    echo "<script>window.location.href='edit.php?id=$id&success=hapus_foto';</script>";
    exit();
}

// ACTION: CATAT PENJUALAN
if (isset($_POST['catat_penjualan'])) {
    $jumlah_jual = (int)$_POST['jumlah_jual'];
    if ($jumlah_jual > 0 && $jumlah_jual <= $data['stok']) {
        $stok_baru = $data['stok'] - $jumlah_jual;
        $terjual_baru = $data['terjual'] + $jumlah_jual;
        mysqli_query($conn, "UPDATE produk SET stok='$stok_baru', terjual='$terjual_baru' WHERE id='$id'");
        mysqli_query($conn, "INSERT INTO log_aktivitas (admin, aksi) VALUES ('{$_SESSION['admin']}', 'Mencatat $jumlah_jual penjualan produk: {$data['nama_produk']}')");
        $pesan_jual = "✅ Berhasil mencatat $jumlah_jual penjualan!";
        // Refresh data
        $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM produk WHERE id = '$id'"));
    } else {
        $error_jual = "❌ Jumlah tidak valid atau melebihi stok!";
    }
}

// ACTION: EDIT PRODUK
if (isset($_POST['edit_produk'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    $stok = mysqli_real_escape_string($conn, $_POST['stok']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    
    $gambar_utama = $data['gambar'];

    // Upload foto baru
    $foto_files = $_FILES['foto'];
    $uploaded_fotos = [];
    $error_upload = '';
    if (isset($foto_files['name'][0]) && $foto_files['name'][0] != '') {
        for ($i = 0; $i < count($foto_files['name']); $i++) {
            $upload_res = upload_file($foto_files['name'][$i], $foto_files['tmp_name'][$i]);
            if ($upload_res['status']) {
                $uploaded_fotos[] = $upload_res['filename'];
            } else {
                $error_upload = $upload_res['message'];
                break;
            }
        }
    }

    if (empty($error_upload)) {
        // Simpan ke produk_foto
        $urutan_max = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(urutan) as max FROM produk_foto WHERE produk_id='$id'"))['max'];
        $urutan_max = $urutan_max ?? -1;
        foreach ($uploaded_fotos as $i => $foto) {
            $urutan = $urutan_max + $i + 1;
            mysqli_query($conn, "INSERT INTO produk_foto (produk_id, foto, urutan) VALUES ('$id', '$foto', '$urutan')");
        }

        if (empty($gambar_utama) && !empty($uploaded_fotos)) {
            $gambar_utama = $uploaded_fotos[0];
        }

        mysqli_query($conn, "UPDATE produk SET nama_produk='$nama', harga='$harga', stok='$stok', deskripsi='$deskripsi', kategori='$kategori', gambar='$gambar_utama' WHERE id='$id'");
        mysqli_query($conn, "INSERT INTO log_aktivitas (admin, aksi) VALUES ('{$_SESSION['admin']}', 'Memperbarui data produk: $nama')");
        
        echo "<script>window.location.href='index.php?success=edit';</script>";
        exit();
     }
 }
 
 $foto_list = mysqli_query($conn, "SELECT * FROM produk_foto WHERE produk_id='$id' ORDER BY urutan ASC");
 ?>

<!-- PAGE HEADER -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-[#1d2327] ">Edit Produk</h1>
        <p class="text-sm text-[#646970] font-medium mt-1">ID Produk: #<?= str_pad($data['id'], 4, '0', STR_PAD_LEFT) ?> • Terakhir diupdate: <?= date('d M Y', strtotime($data['created_at'])) ?></p>
    </div>
    <a href="index.php" class="text-xs font-bold text-[#2271b1] hover:underline uppercase  flex items-center gap-2">
        <i class="fa-solid fa-arrow-left-long"></i>
        Kembali ke Daftar
    </a>
</div>

<?php if ($pesan): ?>
<div class="mb-8 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl flex items-center gap-3">
    <i class="fa-solid fa-circle-check text-emerald-500"></i>
    <span class="text-emerald-700 text-sm font-bold"><?= $pesan ?></span>
</div>
<?php endif; ?>

<?php if (isset($error_upload) && !empty($error_upload)): ?>
<div class="mb-8 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl flex items-center gap-3">
    <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
    <span class="text-red-700 text-sm font-bold"><?= $error_upload ?></span>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    
    <!-- LEFT COLUMN: MAIN FORM -->
    <div class="xl:col-span-2 space-y-8">
        
        <!-- FORM EDIT -->
        <div class="wp-card">
            <div class="p-5 border-b border-[#dcdcde] bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-xs font-black text-[#1d2327] uppercase  flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-[#2271b1]"></i>
                    Informasi Utama Produk
                </h3>
            </div>
            <div class="p-8">
                <form id="form-edit" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Nama Produk</label>
                        <input type="text" name="nama_produk" required value="<?= htmlspecialchars($data['nama_produk']) ?>"
                            class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Deskripsi Produk</label>
                        <textarea name="deskripsi" rows="10"
                            class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all resize-none"><?= htmlspecialchars($data['deskripsi']) ?></textarea>
                    </div>

                    <!-- PHOTO MANAGEMENT -->
                    <div class="pt-6 border-t border-gray-100">
                        <label class="block text-xs font-bold text-[#1d2327] uppercase  mb-4 ml-1">Manajemen Foto</label>
                        
                        <!-- CURRENT PHOTOS -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-8">
                            <?php while ($f = mysqli_fetch_assoc($foto_list)): ?>
                            <div class="relative aspect-square rounded-xl overflow-hidden border border-gray-100 group shadow-sm">
                                <img src="../assets/images/<?= $f['foto'] ?>" class="w-full h-full object-cover">
                                <?php if ($f['foto'] == $data['gambar']): ?>
                                    <span class="absolute top-2 left-2 px-2 py-0.5 bg-[#2271b1] text-white text-[8px] font-black uppercase rounded shadow-sm">Utama</span>
                                <?php endif; ?>
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                    <a href="edit.php?id=<?= $id ?>&hapus_foto=<?= $f['id'] ?>" onclick="return confirm('Hapus foto ini?')" 
                                        class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors shadow-lg">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                            <?php endwhile; ?>
                            
                            <!-- UPLOAD TRIGGER -->
                            <div onclick="document.getElementById('input-foto-baru').click()" 
                                class="aspect-square rounded-xl border-2 border-dashed border-[#dcdcde] hover:border-[#2271b1] hover:bg-blue-50/30 transition-all flex flex-col items-center justify-center gap-2 cursor-pointer text-gray-400 hover:text-[#2271b1]">
                                <i class="fa-solid fa-plus text-lg"></i>
                                <span class="text-[10px] font-black uppercase ">Tambah</span>
                            </div>
                        </div>
                        <input type="file" id="input-foto-baru" name="foto[]" accept="image/*" multiple class="hidden" onchange="previewNewImages(this)">
                        
                        <!-- NEW PHOTO PREVIEW -->
                        <div id="new-preview-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 hidden mb-4"></div>
                    </div>
                    
                    <input type="hidden" name="edit_produk" value="1">
                </form>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: SIDEBAR TOOLS -->
    <div class="space-y-8">
        <!-- SAVE CHANGES -->
        <div class="wp-card">
            <div class="p-4 border-b border-[#dcdcde] bg-gray-50/50">
                <h3 class="text-xs font-black text-[#1d2327] uppercase ">Tindakan</h3>
            </div>
            <div class="p-5 space-y-4">
                <button type="submit" form="form-edit" class="w-full bg-[#2271b1] text-white py-3 rounded-lg font-bold text-xs hover:bg-[#135e96] transition-all shadow-md shadow-blue-500/10 uppercase  flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Perubahan
                </button>
                <a href="hapus.php?id=<?= $id ?>" onclick="return confirm('Yakin ingin menghapus produk ini selamanya?')" 
                    class="block w-full text-center py-2.5 text-[10px] font-black text-red-400 hover:text-red-600 uppercase  transition-colors">
                    Hapus Produk
                </a>
            </div>
        </div>

        <!-- SALES TRACKER -->
        <div class="wp-card">
            <div class="p-5 border-b border-[#dcdcde] bg-gray-50/50">
                <h3 class="text-xs font-black text-[#1d2327] uppercase  flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-[#2271b1]"></i>
                    Catat Penjualan
                </h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <div class="text-lg font-black text-[#002147]"><?= $data['stok'] ?></div>
                        <div class="text-[9px] font-black text-[#2271b1] uppercase ">Stok Tersisa</div>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-xl border border-purple-100">
                        <div class="text-lg font-black text-purple-900"><?= $data['terjual'] ?></div>
                        <div class="text-[9px] font-black text-purple-600 uppercase ">Total Terjual</div>
                    </div>
                </div>

                <form method="POST" class="space-y-3">
                    <div class="relative">
                        <input type="number" name="jumlah_jual" min="1" max="<?= $data['stok'] ?>" placeholder="Jumlah terjual..." required
                            class="w-full bg-gray-50 border border-[#dcdcde] text-xs font-bold rounded-lg pl-4 pr-12 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-400 uppercase">Pcs</span>
                    </div>
                    <button type="submit" name="catat_penjualan" class="w-full py-2.5 bg-gray-800 text-white rounded-lg text-[10px] font-black uppercase  hover:bg-black transition-all">
                        Update Penjualan
                    </button>
                </form>
                
                <?php if (isset($pesan_jual)): ?>
                    <p class="text-[10px] font-bold text-emerald-600 italic text-center"><?= $pesan_jual ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- CATEGORY & PRICE -->
        <div class="wp-card p-6 space-y-6">
            <h3 class="text-xs font-black text-[#1d2327] uppercase  flex items-center gap-2">
                <i class="fa-solid fa-tags text-[#2271b1]"></i>
                Kategori & Harga
            </h3>
            
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-[#646970] uppercase  ml-1">Kategori</label>
                    <select name="kategori" form="form-edit" class="w-full bg-gray-50 border border-[#dcdcde] text-xs font-bold rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10">
                        <?php
                        $kat_query = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                        while ($kat = mysqli_fetch_assoc($kat_query)) {
                            $selected = $data['kategori'] == $kat['nama_kategori'] ? 'selected' : '';
                            echo '<option value="' . $kat['nama_kategori'] . '" ' . $selected . '>' . $kat['nama_kategori'] . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-[#646970] uppercase  ml-1">Harga (Rp)</label>
                    <input type="number" name="harga" form="form-edit" required value="<?= $data['harga'] ?>"
                        class="w-full bg-gray-50 border border-[#dcdcde] text-xs font-bold rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10">
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-[#646970] uppercase  ml-1">Total Stok</label>
                    <input type="number" name="stok" form="form-edit" required value="<?= $data['stok'] ?>"
                        class="w-full bg-gray-50 border border-[#dcdcde] text-xs font-bold rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10">
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function previewNewImages(input) {
    const container = document.getElementById('new-preview-container');
    container.innerHTML = '';
    
    if (input.files && input.files[0]) {
        container.classList.remove('hidden');
        Array.from(input.files).forEach((file) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = "relative aspect-square rounded-xl overflow-hidden border border-emerald-200 bg-emerald-50 group shadow-sm ring-2 ring-emerald-500/20";
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover">
                    <span class="absolute top-2 right-2 px-2 py-0.5 bg-emerald-500 text-white text-[8px] font-black uppercase rounded shadow-sm italic">Baru</span>
                `;
                container.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    } else {
        container.classList.add('hidden');
    }
}
</script>

<?php include 'footer.php'; ?>
