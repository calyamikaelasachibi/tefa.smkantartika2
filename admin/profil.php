<?php 
include 'header.php';

$stmt_profile = $conn->prepare("SELECT * FROM admin WHERE username = ?");
$stmt_profile->bind_param("s", $_SESSION['admin']);
$stmt_profile->execute();
$admin = $stmt_profile->get_result()->fetch_assoc();

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $username_post = $_POST['username'];
    $current_admin = $_SESSION['admin'];
    
    $foto = $admin['foto'];
    $error_upload = '';
    if ($_FILES['foto']['name'] != '') {
        $upload_res = upload_file($_FILES['foto']['name'], $_FILES['foto']['tmp_name']);
        if ($upload_res['status']) {
            $foto = $upload_res['filename'];
        } else {
            $error_upload = $upload_res['message'];
        }
    }
    
    if (empty($error_upload)) {
        // Use Prepared Statements to prevent SQL Injection
        $stmt_update_profile = $conn->prepare("UPDATE admin SET nama_lengkap = ?, email = ?, username = ?, foto = ? WHERE username = ?");
        $stmt_update_profile->bind_param("sssss", $nama_lengkap, $email, $username_post, $foto, $current_admin);
        $update = $stmt_update_profile->execute();
        
        if ($update) {
            $_SESSION['admin'] = $username_post;
            mysqli_query($conn, "INSERT INTO log_aktivitas (admin, aksi) VALUES ('$username_post', 'Memperbarui profil admin')");
            $pesan = "Profil berhasil diperbarui!";
            
            // Refresh data
            $stmt_refresh = $conn->prepare("SELECT * FROM admin WHERE username = ?");
            $stmt_refresh->bind_param("s", $username_post);
            $stmt_refresh->execute();
            $admin = $stmt_refresh->get_result()->fetch_assoc();
        }
    }
}
?>

<!-- PAGE HEADER -->
<div class="mb-8">
    <h1 class="text-2xl font-extrabold text-[#1d2327] ">Profil Saya</h1>
    <p class="text-sm text-[#646970] font-medium mt-1">Kelola informasi pribadi dan pengaturan akun administrator</p>
</div>

<?php if ($pesan): ?>
<div class="mb-8 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl flex items-center gap-3 animate-bounce">
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
    
    <!-- LEFT: PROFILE CARD -->
    <div class="lg:col-span-1">
        <div class="wp-card overflow-hidden">
            <div class="h-24 bg-[#002147] relative">
                <div class="absolute -bottom-12 left-1/2 -translate-x-1/2">
                    <div class="w-24 h-24 rounded-full border-4 border-white shadow-lg overflow-hidden bg-white">
                        <img src="<?= $admin['foto'] ? '../assets/images/' . $admin['foto'] : 'https://ui-avatars.com/api/?name=' . urlencode($admin['nama_lengkap']) . '&background=002147&color=fff' ?>" 
                            alt="Avatar" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
            <div class="pt-16 pb-8 px-6 text-center">
                <h3 class="text-lg font-black text-[#1d2327]"><?= $admin['nama_lengkap'] ?></h3>
                <p class="text-xs font-bold text-[#2271b1] uppercase  mt-1">@<?= $admin['username'] ?></p>
                <div class="mt-6 pt-6 border-t border-gray-100 flex items-center justify-center gap-6">
                    <div class="text-center">
                        <div class="text-sm font-black text-[#1d2327]"><?= mysqli_num_rows(mysqli_query($conn, "SELECT id FROM log_aktivitas WHERE admin='{$admin['username']}'")) ?></div>
                        <div class="text-[9px] font-black text-[#646970] uppercase er">Aktivitas</div>
                    </div>
                    <div class="w-px h-8 bg-gray-100"></div>
                    <div class="text-center">
                        <div class="text-sm font-black text-[#1d2327]">Admin</div>
                        <div class="text-[9px] font-black text-[#646970] uppercase er">Role</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: EDIT FORM -->
    <div class="lg:col-span-2">
        <div class="wp-card">
            <div class="p-5 border-b border-[#dcdcde] bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-xs font-black text-[#1d2327] uppercase  flex items-center gap-2">
                    <i class="fa-solid fa-user-pen text-[#2271b1]"></i>
                    Edit Informasi Profil
                </h3>
            </div>
            <div class="p-8">
                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" value="<?= $admin['nama_lengkap'] ?>" required
                                class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Username</label>
                            <input type="text" name="username" value="<?= $admin['username'] ?>" required
                                class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Alamat Email</label>
                        <input type="email" name="email" value="<?= $admin['email'] ?>" required
                            class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all">
                    </div>

                    <div class="space-y-2 pt-4">
                        <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Ganti Foto Profil</label>
                        <div class="flex items-center gap-6 p-4 bg-gray-50 rounded-xl border border-dashed border-[#dcdcde]">
                            <div class="w-16 h-16 rounded-lg overflow-hidden border border-white shadow-sm flex-shrink-0">
                                <img src="<?= $admin['foto'] ? '../assets/images/' . $admin['foto'] : 'https://ui-avatars.com/api/?name=' . urlencode($admin['nama_lengkap']) . '&background=002147&color=fff' ?>" 
                                    alt="Current" class="w-full h-full object-cover" id="img-preview">
                            </div>
                            <div class="flex-1">
                                <input type="file" name="foto" accept="image/*" class="text-xs font-bold text-[#2271b1] file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file  file:bg-blue-50 file:text-[#2271b1] hover:file:bg-blue-100 cursor-pointer"
                                    onchange="document.getElementById('img-preview').src = window.URL.createObjectURL(this.files[0])">
                                <p class="text-[10px] text-gray-400 font-medium mt-2 italic">Format: JPG, PNG, WEBP. Maksimal 2MB.</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-4">
                        <button type="reset" class="px-6 py-3 text-xs font-black text-gray-400 hover:text-red-500 uppercase  transition-colors">Reset</button>
                        <button type="submit" class="px-8 py-3 bg-[#2271b1] text-white rounded-lg font-bold text-xs hover:bg-[#135e96] transition-all shadow-md shadow-blue-500/10 uppercase ">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>
