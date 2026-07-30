<?php 
include 'header.php';

if (isset($_GET['hapus_semua'])) {
    $admin = $_SESSION['admin'];
    mysqli_query($conn, "TRUNCATE TABLE log_aktivitas");
    mysqli_query($conn, "INSERT INTO log_aktivitas (admin, aksi) VALUES ('$admin', 'Membersihkan seluruh log aktivitas')");
    echo "<script>window.location.href='log.php';</script>";
    exit();
}

$log_query = mysqli_query($conn, "SELECT * FROM log_aktivitas ORDER BY waktu DESC");
$log_total = mysqli_num_rows($log_query);
?>

<!-- PAGE HEADER -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-[#1d2327] ">Log Aktivitas</h1>
        <p class="text-sm text-[#646970] font-medium mt-1">Rekam jejak seluruh perubahan dan aktivitas pada panel admin</p>
    </div>
    <?php if ($log_total > 0): ?>
    <a href="log.php?hapus_semua=1" onclick="return confirm('Yakin ingin menghapus seluruh riwayat aktivitas? Tindakan ini tidak dapat dibatalkan.')" 
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 text-red-600 rounded font-bold text-xs hover:bg-red-600 hover:text-white transition-all shadow-sm uppercase  group">
        <i class="fa-solid fa-trash-can group-hover:animate-bounce"></i>
        Bersihkan Log
    </a>
    <?php endif; ?>
</div>

<!-- LOG TABLE -->
<div class="wp-card overflow-hidden">
    <div class="p-5 border-b border-[#dcdcde] bg-gray-50/50 flex items-center justify-between">
        <h3 class="text-xs font-black text-[#1d2327] uppercase  flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-[#2271b1]"></i>
            Riwayat Aktivitas
        </h3>
        <span class="text-[10px] font-black text-[#646970] bg-gray-200 px-2 py-0.5 rounded-full uppercase "><?= number_format($log_total, 0, ',', '.') ?> Baris</span>
    </div>
    <div class="overflow-x-auto">
        <table class="wp-table">
            <thead>
                <tr>
                    <th class="w-16 text-center">No</th>
                    <th class="w-48">Administrator</th>
                    <th>Aktivitas / Aksi</th>
                    <th class="w-48">Waktu Kejadian</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                if ($log_total > 0):
                    while ($log = mysqli_fetch_assoc($log_query)):
                        // Styling icons based on activity type
                        $icon = '<i class="fa-solid fa-circle-dot text-gray-300"></i>';
                        $row_bg = "";
                        
                        if (stripos($log['aksi'], 'Menambah') !== false) {
                            $icon = '<i class="fa-solid fa-circle-plus text-emerald-500"></i>';
                        } elseif (stripos($log['aksi'], 'Menghapus') !== false) {
                            $icon = '<i class="fa-solid fa-circle-minus text-red-500"></i>';
                        } elseif (stripos($log['aksi'], 'Mengedit') !== false || stripos($log['aksi'], 'Memperbarui') !== false || stripos($log['aksi'], 'Mengubah') !== false) {
                            $icon = '<i class="fa-solid fa-pen-to-square text-blue-500"></i>';
                        } elseif (stripos($log['aksi'], 'Membersihkan') !== false) {
                            $icon = '<i class="fa-solid fa-broom text-orange-500"></i>';
                        }
                ?>
                <tr class="<?= $row_bg ?>">
                    <td class="text-center font-bold text-gray-300"><?= $no++ ?></td>
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-[10px] font-black text-[#2271b1]">
                                <?= strtoupper(substr($log['admin'], 0, 1)) ?>
                            </div>
                            <span class="font-bold text-[#1d2327]"><?= $log['admin'] ?></span>
                        </div>
                    </td>
                    <td>
                        <div class="flex items-center gap-3">
                            <span class="text-xs"><?= $icon ?></span>
                            <span class="font-medium"><?= $log['aksi'] ?></span>
                        </div>
                    </td>
                    <td>
                        <div class="flex flex-col">
                            <span class="font-bold text-[#1d2327] text-xs"><?= date('d M Y', strtotime($log['waktu'])) ?></span>
                            <span class="text-[10px] text-gray-400 font-medium "><?= date('H:i:s', strtotime($log['waktu'])) ?> WIB</span>
                        </div>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="4" class="py-20 text-center">
                        <div class="flex flex-col items-center justify-center space-y-3">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-200 text-3xl">
                                <i class="fa-solid fa-list-ul"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-[#1d2327]">Log masih kosong</h3>
                                <p class="text-[11px] text-[#646970]">Belum ada riwayat aktivitas yang tercatat saat ini.</p>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
