<?php
require 'header.php'; // otomatis: session_start, cek login, koneksi $conn, buka <html>+sidebar+topbar

// ============ PROSES TAMBAH PEMBELIAN ============
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_pembelian'])) {
    $produk_id    = (int) $_POST['produk_id'];
    $nama_pembeli = trim($_POST['nama_pembeli']);
    $jumlah       = (int) $_POST['jumlah'];
    $admin        = $_SESSION['admin'];

    if ($produk_id > 0 && $nama_pembeli !== '' && $jumlah > 0) {
        // Ambil harga, stok & nama produk saat ini
        $stmt = mysqli_prepare($conn, "SELECT nama_produk, harga, stok FROM produk WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $produk_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $produk = mysqli_fetch_assoc($result);

        if (!$produk) {
            $pesan = "Produk tidak ditemukan.";
            $pesan_tipe = "gagal";
        } elseif ($produk['stok'] < $jumlah) {
            $pesan = "Stok tidak mencukupi. Sisa stok: " . $produk['stok'];
            $pesan_tipe = "gagal";
        } else {
            $harga_satuan = $produk['harga'];
            $total_harga  = $harga_satuan * $jumlah;

            // Mulai transaction supaya insert pembelian + update stok tetap konsisten
            mysqli_begin_transaction($conn);
            $sukses = true;
            $error_pesan = "";

            // Simpan transaksi pembelian
            $stmt2 = mysqli_prepare($conn, "INSERT INTO pembelian (produk_id, nama_pembeli, jumlah, harga_satuan, total_harga) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt2, "isiii", $produk_id, $nama_pembeli, $jumlah, $harga_satuan, $total_harga);
            if (!mysqli_stmt_execute($stmt2)) {
                $sukses = false;
                $error_pesan = "Gagal menyimpan transaksi.";
            }

            // Update stok & terjual, dengan pengaman WHERE stok >= ? agar stok tidak minus
            // (mencegah race condition jika ada 2 transaksi bersamaan untuk produk yang sama)
            if ($sukses) {
                $stmt3 = mysqli_prepare($conn, "UPDATE produk SET stok = stok - ?, terjual = terjual + ? WHERE id = ? AND stok >= ?");
                mysqli_stmt_bind_param($stmt3, "iiii", $jumlah, $jumlah, $produk_id, $jumlah);
                mysqli_stmt_execute($stmt3);

                if (mysqli_stmt_affected_rows($stmt3) === 0) {
                    // Stok berubah/habis di antara pengecekan awal dan proses update
                    $sukses = false;
                    $error_pesan = "Stok berubah, transaksi dibatalkan. Silakan coba lagi.";
                }
            }

            // Catat log aktivitas
            if ($sukses) {
                $log_teks = "Mencatat pembelian: {$jumlah}x {$produk['nama_produk']} oleh {$nama_pembeli}";
                $stmt4 = mysqli_prepare($conn, "INSERT INTO log_aktivitas (admin, aksi) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmt4, "ss", $admin, $log_teks);
                mysqli_stmt_execute($stmt4);
            }

            if ($sukses) {
                mysqli_commit($conn);
                $pesan = "Pembelian berhasil dicatat.";
                $pesan_tipe = "sukses";
            } else {
                mysqli_rollback($conn);
                $pesan = $error_pesan;
                $pesan_tipe = "gagal";
            }
        }
    } else {
        $pesan = "Data belum lengkap.";
        $pesan_tipe = "gagal";
    }
}

// ============ AMBIL DAFTAR PRODUK (dipakai untuk dropdown filter & dropdown modal) ============
$produk_list_data = array();
$produk_list_result = mysqli_query($conn, "SELECT id, nama_produk, harga, stok FROM produk ORDER BY nama_produk ASC");
while ($p = mysqli_fetch_assoc($produk_list_result)) {
    $produk_list_data[] = $p;
}

// ============ FILTER (tanggal + produk, opsional & bisa digabung) ============
$dari        = isset($_GET['dari']) ? $_GET['dari'] : '';
$sampai      = isset($_GET['sampai']) ? $_GET['sampai'] : '';
$filter_produk_id = isset($_GET['produk_id']) ? (int) $_GET['produk_id'] : 0;

// Cari nama produk yang sedang difilter (untuk ditampilkan sebagai badge)
$nama_produk_filter = '';
if ($filter_produk_id > 0) {
    foreach ($produk_list_data as $p) {
        if ((int) $p['id'] === $filter_produk_id) {
            $nama_produk_filter = $p['nama_produk'];
            break;
        }
    }
}

// Bangun kondisi WHERE secara dinamis, supaya tanggal & produk bisa dipakai sendiri-sendiri atau bersamaan
function bangun_kondisi_filter($dari, $sampai, $filter_produk_id) {
    $kondisi = array();
    $params  = array();
    $types   = "";

    if ($dari !== '' && $sampai !== '') {
        $kondisi[] = "DATE(p.tanggal) BETWEEN ? AND ?";
        $types .= "ss";
        $params[] = $dari;
        $params[] = $sampai;
    }
    if ($filter_produk_id > 0) {
        $kondisi[] = "p.produk_id = ?";
        $types .= "i";
        $params[] = $filter_produk_id;
    }

    return array('kondisi' => $kondisi, 'params' => $params, 'types' => $types);
}

$filter = bangun_kondisi_filter($dari, $sampai, $filter_produk_id);

// ============ QUERY RIWAYAT PEMBELIAN ============
$sql = "SELECT p.id, p.nama_pembeli, p.jumlah, p.harga_satuan, p.total_harga, p.tanggal,
               pr.nama_produk
        FROM pembelian p
        JOIN produk pr ON pr.id = p.produk_id";

if (!empty($filter['kondisi'])) {
    $sql .= " WHERE " . implode(" AND ", $filter['kondisi']);
}
$sql .= " ORDER BY p.tanggal DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($filter['types'] !== "") {
    // bind_param butuh referensi, jadi kita rakit manual (kompatibel PHP lama)
    $bind_names = array();
    $bind_names[] = $filter['types'];
    foreach ($filter['params'] as $i => $val) {
        $bind_name = 'bind' . $i;
        $$bind_name = $val;
        $bind_names[] = &$$bind_name;
    }
    call_user_func_array('mysqli_stmt_bind_param', array_merge(array($stmt), $bind_names));
}
mysqli_stmt_execute($stmt);
$data_pembelian = mysqli_stmt_get_result($stmt);

$total_keseluruhan = 0;
$total_item = 0;
$rows = array();
while ($row = mysqli_fetch_assoc($data_pembelian)) {
    $rows[] = $row;
    $total_keseluruhan += $row['total_harga'];
    $total_item += $row['jumlah'];
}

// ============ REKAP PENJUALAN PER PRODUK (mengikuti filter yang sama) ============
$sql_rekap = "SELECT pr.id, pr.nama_produk,
                     COUNT(p.id) AS total_transaksi,
                     SUM(p.jumlah) AS total_terjual,
                     SUM(p.total_harga) AS total_pendapatan
              FROM pembelian p
              JOIN produk pr ON pr.id = p.produk_id";

if (!empty($filter['kondisi'])) {
    $sql_rekap .= " WHERE " . implode(" AND ", $filter['kondisi']);
}
$sql_rekap .= " GROUP BY pr.id, pr.nama_produk ORDER BY total_terjual DESC";

$stmt_rekap = mysqli_prepare($conn, $sql_rekap);
if ($filter['types'] !== "") {
    $bind_names_rekap = array();
    $bind_names_rekap[] = $filter['types'];
    foreach ($filter['params'] as $i => $val) {
        $bind_name = 'bindrekap' . $i;
        $$bind_name = $val;
        $bind_names_rekap[] = &$$bind_name;
    }
    call_user_func_array('mysqli_stmt_bind_param', array_merge(array($stmt_rekap), $bind_names_rekap));
}
mysqli_stmt_execute($stmt_rekap);
$data_rekap = mysqli_stmt_get_result($stmt_rekap);

$rekap_produk = array();
while ($rp = mysqli_fetch_assoc($data_rekap)) {
    $rekap_produk[] = $rp;
}

$ada_filter_aktif = ($dari !== '' || $sampai !== '' || $filter_produk_id > 0);
?>

<!-- PAGE HEADER -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-[#1d2327]">Laporan Pembelian</h1>
        <p class="text-sm text-[#646970] font-medium mt-1">Catat transaksi pembelian dan pantau riwayat penjualan produk</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="toggleModalPembelian()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#2271b1] text-white rounded font-bold text-xs hover:bg-[#135e96] transition-all shadow-sm uppercase">
            <i class="fa-solid fa-plus"></i>
            Tambah Transaksi
        </button>
    </div>
</div>

<!-- STAT CARDS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
    <div class="wp-card p-6 flex items-center gap-5 group hover:border-[#2271b1] transition-colors">
        <div class="w-14 h-14 bg-blue-50 text-[#2271b1] rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-receipt"></i>
        </div>
        <div>
            <div class="text-2xl font-black text-[#1d2327] leading-none mb-1"><?php echo number_format(count($rows), 0, ',', '.'); ?></div>
            <div class="text-[10px] font-bold text-[#646970] uppercase">Total Transaksi</div>
        </div>
    </div>

    <div class="wp-card p-6 flex items-center gap-5 group hover:border-[#2271b1] transition-colors">
        <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-cart-shopping"></i>
        </div>
        <div>
            <div class="text-2xl font-black text-[#1d2327] leading-none mb-1"><?php echo number_format($total_item, 0, ',', '.'); ?></div>
            <div class="text-[10px] font-bold text-[#646970] uppercase">Total Item Terjual</div>
        </div>
    </div>

    <div class="wp-card p-6 flex items-center gap-5 group hover:border-[#2271b1] transition-colors">
        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-sack-dollar"></i>
        </div>
        <div>
            <div class="text-xl font-black text-[#1d2327] leading-none mb-1">Rp <?php echo number_format($total_keseluruhan, 0, ',', '.'); ?></div>
            <div class="text-[10px] font-bold text-[#646970] uppercase">Total Pendapatan</div>
        </div>
    </div>
</div>

<?php if (isset($pesan)): ?>
    <div class="mb-6 px-4 py-3 rounded-lg text-sm font-bold border <?php echo $pesan_tipe === 'sukses' ? 'bg-green-50 border-green-300 text-green-700' : 'bg-red-50 border-red-300 text-red-700'; ?>">
        <i class="fa-solid <?php echo $pesan_tipe === 'sukses' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?> mr-1"></i>
        <?php echo htmlspecialchars($pesan); ?>
    </div>
<?php endif; ?>

<!-- FILTER & TABEL RIWAYAT -->
<div class="space-y-8">

    <!-- FILTER BAR -->
    <div class="wp-card p-4 flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <span class="text-[10px] font-black text-[#646970] uppercase mr-1">Periode:</span>
            <input type="date" name="dari" value="<?php echo htmlspecialchars($dari); ?>"
                class="bg-gray-50 border border-[#dcdcde] text-xs font-bold rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#2271b1]">
            <span class="text-xs text-gray-400">s/d</span>
            <input type="date" name="sampai" value="<?php echo htmlspecialchars($sampai); ?>"
                class="bg-gray-50 border border-[#dcdcde] text-xs font-bold rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#2271b1]">

            <span class="text-[10px] font-black text-[#646970] uppercase ml-3 mr-1">Produk:</span>
            <select name="produk_id" class="bg-gray-50 border border-[#dcdcde] text-xs font-bold rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#2271b1]">
                <option value="0">Semua Produk</option>
                <?php foreach ($produk_list_data as $p): ?>
                    <option value="<?php echo $p['id']; ?>" <?php echo $filter_produk_id === (int) $p['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($p['nama_produk']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="px-4 py-2 bg-gray-200 text-[#2c3338] rounded font-bold text-[10px] hover:bg-gray-300 transition-all uppercase">Filter</button>
        </form>

        <?php if ($ada_filter_aktif): ?>
            <a href="laporan_pembelian.php" class="text-[10px] font-bold text-[#2271b1] hover:underline uppercase">Reset Filter</a>
        <?php endif; ?>
    </div>

    <!-- TABEL RIWAYAT -->
    <div class="wp-card overflow-hidden">
        <div class="p-5 border-b border-[#dcdcde] bg-gray-50/50 flex flex-wrap items-center justify-between gap-2">
            <h3 class="text-sm font-bold text-[#1d2327] uppercase flex items-center gap-2">
                <i class="fa-solid fa-list-check text-[#2271b1]"></i>
                Riwayat Pembelian
            </h3>
            <div class="flex items-center gap-2">
                <?php if ($nama_produk_filter !== ''): ?>
                    <span class="text-[10px] font-bold text-[#2271b1] bg-blue-50 px-2 py-0.5 rounded-full">
                        Difilter: <?php echo htmlspecialchars($nama_produk_filter); ?>
                    </span>
                <?php endif; ?>
                <span class="text-[10px] font-black text-[#646970] bg-gray-200 px-2 py-0.5 rounded-full uppercase"><?php echo count($rows); ?> Transaksi</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="wp-table">
                <thead>
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Pembeli</th>
                        <th class="text-center">Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr><td colspan="7" class="text-center text-[#646970] py-12 italic">Belum ada data pembelian.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($rows as $r): ?>
                            <tr>
                                <td class="text-center font-bold text-gray-300"><?php echo $no++; ?></td>
                                <td class="text-xs text-[#646970]"><?php echo date('d M Y, H:i', strtotime($r['tanggal'])); ?></td>
                                <td class="font-bold text-[#1d2327]"><?php echo htmlspecialchars($r['nama_produk']); ?></td>
                                <td><?php echo htmlspecialchars($r['nama_pembeli']); ?></td>
                                <td class="text-center">
                                    <span class="px-2.5 py-1 bg-gray-100 text-[#1d2327] rounded-full text-[10px] font-bold"><?php echo $r['jumlah']; ?></span>
                                </td>
                                <td>Rp<?php echo number_format($r['harga_satuan'], 0, ',', '.'); ?></td>
                                <td class="font-bold text-[#2271b1]">Rp<?php echo number_format($r['total_harga'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="text-right px-5 py-4 bg-gray-50/50 font-extrabold text-[#1d2327] text-sm border-t border-[#dcdcde]">
            Total Keseluruhan: <span class="text-[#2271b1]">Rp<?php echo number_format($total_keseluruhan, 0, ',', '.'); ?></span>
        </div>
    </div>

    <!-- REKAP PENJUALAN PER PRODUK -->
    <div class="wp-card overflow-hidden">
        <div class="p-5 border-b border-[#dcdcde] bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#1d2327] uppercase flex items-center gap-2">
                <i class="fa-solid fa-chart-column text-[#2271b1]"></i>
                Rekap Penjualan per Produk
                <?php if ($ada_filter_aktif): ?>
                    <span class="text-[10px] font-semibold text-[#646970] normal-case">(sesuai filter aktif)</span>
                <?php endif; ?>
            </h3>
            <span class="text-[10px] font-black text-[#646970] bg-gray-200 px-2 py-0.5 rounded-full uppercase"><?php echo count($rekap_produk); ?> Produk</span>
        </div>
        <div class="overflow-x-auto">
            <table class="wp-table">
                <thead>
                    <tr>
                        <th class="w-12 text-center">Rank</th>
                        <th>Produk</th>
                        <th class="text-center">Jumlah Transaksi</th>
                        <th class="text-center">Total Terjual</th>
                        <th>Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rekap_produk)): ?>
                        <tr><td colspan="5" class="text-center text-[#646970] py-12 italic">Belum ada data penjualan.</td></tr>
                    <?php else: ?>
                        <?php $rank = 1; foreach ($rekap_produk as $rp): ?>
                            <tr>
                                <td class="text-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[10px] font-black <?php echo $rank === 1 ? 'bg-amber-100 text-amber-700' : ($rank === 2 ? 'bg-gray-200 text-gray-600' : ($rank === 3 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-400')); ?>">
                                        <?php echo $rank++; ?>
                                    </span>
                                </td>
                                <td class="font-bold text-[#1d2327]"><?php echo htmlspecialchars($rp['nama_produk']); ?></td>
                                <td class="text-center text-xs text-[#646970] font-semibold"><?php echo number_format($rp['total_transaksi'], 0, ',', '.'); ?>x</td>
                                <td class="text-center">
                                    <span class="px-2.5 py-1 bg-blue-50 text-[#2271b1] rounded-full text-[10px] font-bold"><?php echo number_format($rp['total_terjual'], 0, ',', '.'); ?> unit</span>
                                </td>
                                <td class="font-bold text-emerald-600">Rp<?php echo number_format($rp['total_pendapatan'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- MODAL TAMBAH TRANSAKSI -->
<div id="modal-pembelian-overlay" class="fixed inset-0 bg-[#1d2327]/70 backdrop-blur-sm z-[200] hidden items-center justify-center p-4" onclick="if(event.target === this) toggleModalPembelian()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden border border-[#dcdcde]">

        <!-- HEADER -->
        <div class="relative px-7 py-6 bg-gradient-to-r from-[#1d2327] to-[#2c3338]">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-[#2271b1] flex items-center justify-center text-white text-lg shadow-lg shadow-black/20 shrink-0">
                        <i class="fa-solid fa-cash-register"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-white uppercase tracking-wide">Transaksi Pembelian Baru</h3>
                        <p class="text-[11px] text-gray-400 font-semibold mt-0.5">Lengkapi detail transaksi di bawah ini</p>
                    </div>
                </div>
                <button onclick="toggleModalPembelian()" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 rounded-full transition-all shrink-0">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- BODY -->
        <form method="POST" id="form-pembelian" class="p-7 space-y-5">

            <div class="space-y-2">
                <label class="flex items-center gap-1.5 text-[11px] font-black text-[#1d2327] uppercase tracking-wide ml-0.5">
                    <i class="fa-solid fa-box text-[#2271b1] text-[10px]"></i> Produk
                </label>
                <select name="produk_id" id="input-produk" required onchange="updateRingkasan()"
                    class="w-full bg-white border-2 border-[#dcdcde] text-sm font-semibold text-[#1d2327] rounded-lg px-4 py-3 focus:outline-none focus:ring-4 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all cursor-pointer">
                    <option value="">— Pilih Produk —</option>
                    <?php foreach ($produk_list_data as $p): ?>
                        <option value="<?php echo $p['id']; ?>"
                            data-harga="<?php echo (int) $p['harga']; ?>"
                            data-stok="<?php echo (int) $p['stok']; ?>">
                            <?php echo htmlspecialchars($p['nama_produk']); ?> · Stok <?php echo $p['stok']; ?> · Rp<?php echo number_format($p['harga'], 0, ',', '.'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="flex items-center gap-1.5 text-[11px] font-black text-[#1d2327] uppercase tracking-wide ml-0.5">
                        <i class="fa-solid fa-user text-[#2271b1] text-[10px]"></i> Nama Pembeli
                    </label>
                    <input type="text" name="nama_pembeli" required placeholder="Contoh: Budi Santoso"
                        class="w-full bg-white border-2 border-[#dcdcde] text-sm font-semibold text-[#1d2327] rounded-lg px-4 py-3 focus:outline-none focus:ring-4 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all placeholder:font-normal placeholder:text-gray-400">
                </div>

                <div class="space-y-2">
                    <label class="flex items-center gap-1.5 text-[11px] font-black text-[#1d2327] uppercase tracking-wide ml-0.5">
                        <i class="fa-solid fa-hashtag text-[#2271b1] text-[10px]"></i> Jumlah
                    </label>
                    <div class="flex items-stretch border-2 border-[#dcdcde] rounded-lg overflow-hidden focus-within:ring-4 focus-within:ring-[#2271b1]/10 focus-within:border-[#2271b1] transition-all">
                        <button type="button" onclick="stepJumlah(-1)" tabindex="-1" class="w-10 bg-gray-50 hover:bg-gray-100 text-[#646970] font-bold text-lg transition-colors">−</button>
                        <input type="number" name="jumlah" id="input-jumlah" min="1" value="1" required oninput="updateRingkasan()"
                            class="w-full text-center bg-white text-sm font-bold text-[#1d2327] px-2 py-3 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        <button type="button" onclick="stepJumlah(1)" tabindex="-1" class="w-10 bg-gray-50 hover:bg-gray-100 text-[#646970] font-bold text-lg transition-colors">+</button>
                    </div>
                </div>
            </div>

            <!-- RINGKASAN TRANSAKSI (muncul otomatis setelah produk dipilih) -->
            <div id="box-ringkasan" class="rounded-xl border border-dashed border-[#c3c4c7] bg-gray-50/70 px-5 py-4 space-y-2.5 hidden">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-[#646970] font-semibold">Sisa Stok Tersedia</span>
                    <span id="ringkasan-stok" class="font-bold text-[#1d2327]">—</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-[#646970] font-semibold">Harga Satuan</span>
                    <span id="ringkasan-harga" class="font-bold text-[#1d2327]">—</span>
                </div>
                <div class="h-px bg-[#dcdcde]"></div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black text-[#1d2327] uppercase tracking-wide">Total Bayar</span>
                    <span id="ringkasan-total" class="text-xl font-black text-[#2271b1]">Rp0</span>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="toggleModalPembelian()" class="flex-1 bg-gray-100 text-[#1d2327] py-3 rounded-lg font-bold text-xs hover:bg-gray-200 transition-all uppercase">
                    Batal
                </button>
                <button type="submit" name="tambah_pembelian" class="flex-1 bg-[#2271b1] text-white py-3 rounded-lg font-bold text-xs hover:bg-[#135e96] transition-all shadow-md shadow-blue-500/20 uppercase flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check"></i> Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModalPembelian() {
        const overlay = document.getElementById('modal-pembelian-overlay');
        if (overlay.classList.contains('hidden')) {
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
        } else {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
        }
    }

    function stepJumlah(delta) {
        const input = document.getElementById('input-jumlah');
        let val = parseInt(input.value || '1', 10) + delta;
        if (val < 1) val = 1;
        input.value = val;
        updateRingkasan();
    }

    function updateRingkasan() {
        const select = document.getElementById('input-produk');
        const jumlahInput = document.getElementById('input-jumlah');
        const box = document.getElementById('box-ringkasan');
        const opt = select.options[select.selectedIndex];

        if (!opt || !opt.value) {
            box.classList.add('hidden');
            return;
        }

        const harga = parseInt(opt.dataset.harga || '0', 10);
        const stok = parseInt(opt.dataset.stok || '0', 10);
        let jumlah = parseInt(jumlahInput.value || '0', 10);
        if (jumlah < 1) jumlah = 1;

        const total = harga * jumlah;
        const stokEl = document.getElementById('ringkasan-stok');

        stokEl.textContent = stok + ' unit';
        stokEl.classList.toggle('text-red-600', jumlah > stok);
        stokEl.classList.toggle('text-[#1d2327]', jumlah <= stok);

        document.getElementById('ringkasan-harga').textContent = 'Rp' + harga.toLocaleString('id-ID');
        document.getElementById('ringkasan-total').textContent = 'Rp' + total.toLocaleString('id-ID');

        box.classList.remove('hidden');
    }

    // Kalau ada pesan hasil submit dan form gagal (validasi server), otomatis buka modal lagi
    <?php if (isset($pesan) && $pesan_tipe === 'gagal' && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    document.addEventListener('DOMContentLoaded', function() {
        toggleModalPembelian();
    });
    <?php endif; ?>
</script>

<?php
require 'footer.php';
?>