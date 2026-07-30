<?php 
include 'header.php';

$pesan = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi = $_POST['konfirmasi'];
    $admin = $_SESSION['admin'];

    // Use Prepared Statements to fetch current admin
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $admin);
    $stmt->execute();
    $result = $stmt->get_result();
    $cek = $result->fetch_assoc();

    // Verify current password (supports both password_verify and old MD5)
    $password_verified = false;
    if ($cek) {
        if (password_verify($password_lama, $cek['password'])) {
            $password_verified = true;
        } elseif (MD5($password_lama) === $cek['password']) {
            $password_verified = true;
        }
    }

    if (!$password_verified) {
        $error = "Password lama yang Anda masukkan salah.";
    } elseif ($password_baru != $konfirmasi) {
        $error = "Konfirmasi password baru tidak cocok.";
    } elseif (strlen($password_baru) < 6) {
        $error = "Password baru minimal harus 6 karakter.";
    } else {
        // Use secure hashing for new password
        $password_baru_hashed = password_hash($password_baru, PASSWORD_DEFAULT);
        
        $update_stmt = $conn->prepare("UPDATE admin SET password = ? WHERE username = ?");
        $update_stmt->bind_param("ss", $password_baru_hashed, $admin);
        
        if ($update_stmt->execute()) {
            mysqli_query($conn, "INSERT INTO log_aktivitas (admin, aksi) VALUES ('$admin', 'Mengubah password akun')");
            $pesan = "Password Anda berhasil diperbarui!";
        } else {
            $error = "Terjadi kesalahan saat memperbarui password.";
        }
    }
}
?>

<!-- PAGE HEADER -->
<div class="mb-8">
    <h1 class="text-2xl font-extrabold text-[#1d2327] ">Keamanan Akun</h1>
    <p class="text-sm text-[#646970] font-medium mt-1">Perbarui password Anda secara berkala untuk menjaga keamanan panel admin</p>
</div>

<div class="max-w-2xl">
    <!-- MESSAGES -->
    <?php if ($pesan): ?>
    <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl flex items-center gap-3 animate-bounce">
        <i class="fa-solid fa-circle-check text-emerald-500"></i>
        <span class="text-emerald-700 text-sm font-bold"><?= $pesan ?></span>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl flex items-center gap-3">
        <i class="fa-solid fa-circle-exclamation text-red-500"></i>
        <span class="text-red-700 text-sm font-bold"><?= $error ?></span>
    </div>
    <?php endif; ?>

    <div class="wp-card">
        <div class="p-5 border-b border-[#dcdcde] bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-xs font-black text-[#1d2327] uppercase  flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-[#2271b1]"></i>
                Ganti Password
            </h3>
        </div>
        <div class="p-8">
            <form method="POST" class="space-y-6">
                <!-- Current Password -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Password Saat Ini</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#2271b1]">
                            <i class="fa-solid fa-key text-xs"></i>
                        </div>
                        <input type="password" name="password_lama" required placeholder="Masukkan password lama Anda"
                            class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all">
                    </div>
                </div>

                <hr class="border-gray-100 my-8">

                <!-- New Password -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Password Baru</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#2271b1]">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </div>
                        <input type="password" name="password_baru" required placeholder="Minimal 6 karakter"
                            class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all">
                    </div>
                </div>

                <!-- Confirm New Password -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Konfirmasi Password Baru</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#2271b1]">
                            <i class="fa-solid fa-circle-check text-xs"></i>
                        </div>
                        <input type="password" name="konfirmasi" required placeholder="Ulangi password baru Anda"
                            class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all">
                    </div>
                </div>

                <div class="pt-6 flex items-center justify-end gap-4">
                    <button type="reset" class="px-6 py-3 text-xs font-black text-gray-400 hover:text-red-500 uppercase  transition-colors">Reset</button>
                    <button type="submit" class="px-8 py-3 bg-[#2271b1] text-white rounded-lg font-bold text-xs hover:bg-[#135e96] transition-all shadow-md shadow-blue-500/10 uppercase  flex items-center gap-2">
                        <i class="fa-solid fa-shield-check"></i>
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>
    </div>
    
</div>

<?php include 'footer.php'; ?>
