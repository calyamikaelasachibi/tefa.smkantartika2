<?php 
include 'header.php';

$pesan = '';
$error = '';

// Action: Tambah Pengguna Baru
if (isset($_POST['tambah'])) {
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $username     = trim($_POST['username']);
    $email        = trim($_POST['email']);
    $password     = $_POST['password'];
    $admin        = $_SESSION['admin'];

    // Cek username sudah dipakai atau belum
    $stmt_cek = $conn->prepare("SELECT id FROM admin WHERE username = ?");
    $stmt_cek->bind_param("s", $username);
    $stmt_cek->execute();
    $ada = $stmt_cek->get_result()->fetch_assoc();

    if ($ada) {
        $error = "Username \"$username\" sudah digunakan, silakan pilih username lain.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal harus 6 karakter.";
    } else {
        // Upload foto jika ada
        $foto = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['name'] != '') {
            $upload_res = upload_file($_FILES['foto']['name'], $_FILES['foto']['tmp_name']);
            if ($upload_res['status']) {
                $foto = $upload_res['filename'];
            } else {
                $error = $upload_res['message'];
            }
        }

        if (empty($error)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt_add = $conn->prepare("INSERT INTO admin (nama_lengkap, username, email, password, foto) VALUES (?, ?, ?, ?, ?)");
            $stmt_add->bind_param("sssss", $nama_lengkap, $username, $email, $password_hash, $foto);

            if ($stmt_add->execute()) {
                mysqli_query($conn, "INSERT INTO log_aktivitas (admin, aksi) VALUES ('$admin', 'Menambah pengguna admin baru: $username')");
                echo "<script>window.location.href='pengguna.php?sukses=tambah';</script>";
                exit();
            } else {
                $error = "Terjadi kesalahan saat menyimpan data pengguna.";
            }
        }
    }
}

// Action: Edit Pengguna
if (isset($_POST['edit'])) {
    $id           = (int)$_POST['id'];
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $username     = trim($_POST['username']);
    $email        = trim($_POST['email']);
    $password     = $_POST['password'];
    $admin        = $_SESSION['admin'];

    // Cek username tidak bentrok dengan pengguna lain
    $stmt_cek = $conn->prepare("SELECT id, username FROM admin WHERE username = ? AND id != ?");
    $stmt_cek->bind_param("si", $username, $id);
    $stmt_cek->execute();
    $ada = $stmt_cek->get_result()->fetch_assoc();

    // Ambil data lama (untuk foto & username lama)
    $stmt_lama = $conn->prepare("SELECT * FROM admin WHERE id = ?");
    $stmt_lama->bind_param("i", $id);
    $stmt_lama->execute();
    $data_lama = $stmt_lama->get_result()->fetch_assoc();

    if ($ada) {
        $error = "Username \"$username\" sudah digunakan oleh pengguna lain.";
    } elseif (!$data_lama) {
        $error = "Data pengguna tidak ditemukan.";
    } else {
        $foto = $data_lama['foto'];
        if (isset($_FILES['foto']) && $_FILES['foto']['name'] != '') {
            $upload_res = upload_file($_FILES['foto']['name'], $_FILES['foto']['tmp_name']);
            if ($upload_res['status']) {
                $foto = $upload_res['filename'];
            } else {
                $error = $upload_res['message'];
            }
        }

        if (empty($error)) {
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $error = "Password baru minimal harus 6 karakter.";
                } else {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt_edit = $conn->prepare("UPDATE admin SET nama_lengkap = ?, username = ?, email = ?, password = ?, foto = ? WHERE id = ?");
                    $stmt_edit->bind_param("sssssi", $nama_lengkap, $username, $email, $password_hash, $foto, $id);
                }
            } else {
                $stmt_edit = $conn->prepare("UPDATE admin SET nama_lengkap = ?, username = ?, email = ?, foto = ? WHERE id = ?");
                $stmt_edit->bind_param("ssssi", $nama_lengkap, $username, $email, $foto, $id);
            }

            if (empty($error) && $stmt_edit->execute()) {
                // Jika mengedit akun sendiri, sinkronkan session
                if ($data_lama['username'] === $_SESSION['admin']) {
                    $_SESSION['admin'] = $username;
                }
                mysqli_query($conn, "INSERT INTO log_aktivitas (admin, aksi) VALUES ('$admin', 'Mengedit data pengguna admin: $username')");
                echo "<script>window.location.href='pengguna.php?sukses=edit';</script>";
                exit();
            } elseif (empty($error)) {
                $error = "Terjadi kesalahan saat memperbarui data pengguna.";
            }
        }
    }
}

// Action: Hapus Pengguna
if (isset($_GET['hapus'])) {
    $id    = (int)$_GET['hapus'];
    $admin = $_SESSION['admin'];

    $total_admin_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM admin"));
    $total_admin = $total_admin_row['total'];

    $stmt_target = $conn->prepare("SELECT username FROM admin WHERE id = ?");
    $stmt_target->bind_param("i", $id);
    $stmt_target->execute();
    $target = $stmt_target->get_result()->fetch_assoc();

    if (!$target) {
        $error = "Data pengguna tidak ditemukan.";
    } elseif ($target['username'] === $_SESSION['admin']) {
        $error = "Anda tidak dapat menghapus akun Anda sendiri.";
    } elseif ($total_admin <= 1) {
        $error = "Tidak dapat menghapus pengguna terakhir. Minimal harus ada 1 akun admin.";
    } else {
        $stmt_del = $conn->prepare("DELETE FROM admin WHERE id = ?");
        $stmt_del->bind_param("i", $id);
        if ($stmt_del->execute()) {
            mysqli_query($conn, "INSERT INTO log_aktivitas (admin, aksi) VALUES ('$admin', 'Menghapus pengguna admin: {$target['username']}')");
            echo "<script>window.location.href='pengguna.php?sukses=hapus';</script>";
            exit();
        } else {
            $error = "Terjadi kesalahan saat menghapus data pengguna.";
        }
    }
}

if (isset($_GET['sukses'])) {
    $daftar_pesan_sukses = array(
        'tambah' => 'Pengguna baru berhasil ditambahkan!',
        'edit'   => 'Data pengguna berhasil diperbarui!',
        'hapus'  => 'Pengguna berhasil dihapus!',
    );
    $pesan = isset($daftar_pesan_sukses[$_GET['sukses']]) ? $daftar_pesan_sukses[$_GET['sukses']] : '';
}

$total_user = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM admin"));
?>

<!-- PAGE HEADER -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-[#1d2327] ">Pengelolaan User</h1>
        <p class="text-sm text-[#646970] font-medium mt-1">Kelola akun administrator yang memiliki akses ke panel admin</p>
    </div>
    <button onclick="bukaModalTambah()"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#2271b1] text-white rounded font-bold text-xs hover:bg-[#135e96] transition-all shadow-sm uppercase ">
        <i class="fa-solid fa-user-plus"></i>
        Tambah Pengguna
    </button>
</div>

<!-- MESSAGES -->
<?php if ($pesan): ?>
<div id="alert-pesan" class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl flex items-center gap-3 transition-opacity duration-500">
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

<!-- USER TABLE -->
<div class="wp-card overflow-hidden">
    <div class="p-5 border-b border-[#dcdcde] bg-gray-50/50 flex items-center justify-between">
        <h3 class="text-xs font-black text-[#1d2327] uppercase  flex items-center gap-2">
            <i class="fa-solid fa-users-gear text-[#2271b1]"></i>
            Daftar Pengguna
        </h3>
        <span class="text-[10px] font-black text-[#646970] bg-gray-200 px-2 py-0.5 rounded-full uppercase "><?= $total_user ?> Pengguna</span>
    </div>
    <div class="overflow-x-auto">
        <table class="wp-table">
            <thead>
                <tr>
                    <th class="w-16 text-center">ID</th>
                    <th>Pengguna</th>
                    <th>Email</th>
                    <th class="text-center w-32">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = mysqli_query($conn, "SELECT * FROM admin ORDER BY id ASC");
                while ($u = mysqli_fetch_assoc($query)):
                    $foto_url = $u['foto'] ? '../assets/images/' . $u['foto'] : 'https://ui-avatars.com/api/?name=' . urlencode($u['nama_lengkap']) . '&background=002147&color=fff';
                ?>
                <tr>
                    <td class="text-center font-bold text-gray-300">#<?= str_pad($u['id'], 3, '0', STR_PAD_LEFT) ?></td>
                    <td>
                        <div class="flex items-center gap-3">
                            <img src="<?= $foto_url ?>" alt="Avatar" class="w-9 h-9 rounded-full object-cover border border-gray-200">
                            <div>
                                <div class="font-bold text-[#1d2327] flex items-center gap-2">
                                    <?= $u['nama_lengkap'] ?>
                                    <?php if ($u['username'] === $_SESSION['admin']): ?>
                                    <span class="text-[9px] font-black text-[#2271b1] bg-blue-50 px-2 py-0.5 rounded-full uppercase ">Anda</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-[11px] text-gray-400 font-medium">@<?= $u['username'] ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?= $u['email'] ?></td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button type="button"
                                onclick='bukaModalEdit(<?= json_encode($u, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
                                class="w-8 h-8 inline-flex items-center justify-center bg-gray-100 text-[#1d2327] rounded hover:bg-[#2271b1] hover:text-white transition-all shadow-sm" title="Edit">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </button>
                            <?php if ($u['username'] !== $_SESSION['admin']): ?>
                            <a href="pengguna.php?hapus=<?= $u['id'] ?>" onclick="return confirm('Yakin hapus pengguna \'<?= $u['username'] ?>\'? Tindakan ini tidak dapat dibatalkan.')"
                                class="w-8 h-8 inline-flex items-center justify-center bg-red-50 text-red-500 rounded hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Hapus">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </a>
                            <?php else: ?>
                            <span class="w-8 h-8 inline-flex items-center justify-center text-gray-200" title="Tidak dapat menghapus diri sendiri">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL: TAMBAH PENGGUNA -->
<div id="modal-tambah" class="fixed inset-0 z-[200] hidden items-center justify-center p-4 bg-black/50">
    <div class="wp-card w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="p-5 border-b border-[#dcdcde] bg-gray-50/50 flex items-center justify-between sticky top-0 bg-white">
            <h3 class="text-xs font-black text-[#1d2327] uppercase  flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-[#2271b1]"></i>
                Tambah Pengguna Baru
            </h3>
            <button type="button" onclick="tutupModal('modal-tambah')" class="text-gray-400 hover:text-red-500">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            <div class="space-y-2">
                <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" required
                    class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all">
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Username</label>
                <input type="text" name="username" required
                    class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all">
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Email</label>
                <input type="email" name="email" required
                    class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all">
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Password</label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter"
                    class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all">
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Foto Profil (Opsional)</label>
                <input type="file" name="foto" accept="image/*"
                    class="text-xs font-bold text-[#2271b1] file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file  file:bg-blue-50 file:text-[#2271b1] hover:file:bg-blue-100 cursor-pointer">
            </div>
            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" onclick="tutupModal('modal-tambah')" class="px-6 py-3 text-xs font-black text-gray-400 hover:text-red-500 uppercase  transition-colors">Batal</button>
                <button type="submit" name="tambah"
                    class="px-8 py-3 bg-[#2271b1] text-white rounded-lg font-bold text-xs hover:bg-[#135e96] transition-all shadow-md shadow-blue-500/10 uppercase ">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: EDIT PENGGUNA -->
<div id="modal-edit" class="fixed inset-0 z-[200] hidden items-center justify-center p-4 bg-black/50">
    <div class="wp-card w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="p-5 border-b border-[#dcdcde] bg-gray-50/50 flex items-center justify-between sticky top-0 bg-white">
            <h3 class="text-xs font-black text-[#1d2327] uppercase  flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-[#2271b1]"></i>
                Edit Pengguna
            </h3>
            <button type="button" onclick="tutupModal('modal-edit')" class="text-gray-400 hover:text-red-500">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            <input type="hidden" name="id" id="edit-id">
            <div class="flex items-center gap-4">
                <img id="edit-preview" src="" alt="Avatar" class="w-14 h-14 rounded-full object-cover border border-gray-200">
                <div class="flex-1 space-y-2">
                    <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Ganti Foto (Opsional)</label>
                    <input type="file" name="foto" accept="image/*"
                        onchange="document.getElementById('edit-preview').src = window.URL.createObjectURL(this.files[0])"
                        class="text-xs font-bold text-[#2271b1] file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file  file:bg-blue-50 file:text-[#2271b1] hover:file:bg-blue-100 cursor-pointer">
                </div>
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" id="edit-nama" required
                    class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all">
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Username</label>
                <input type="text" name="username" id="edit-username" required
                    class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all">
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Email</label>
                <input type="email" name="email" id="edit-email" required
                    class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all">
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-bold text-[#1d2327] uppercase  ml-1">Password Baru (Opsional)</label>
                <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"
                    class="w-full bg-gray-50 border border-[#dcdcde] text-sm font-medium rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2271b1]/10 focus:border-[#2271b1] transition-all">
                <p class="text-[10px] text-gray-400 italic ml-1">Minimal 6 karakter jika diisi.</p>
            </div>
            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" onclick="tutupModal('modal-edit')" class="px-6 py-3 text-xs font-black text-gray-400 hover:text-red-500 uppercase  transition-colors">Batal</button>
                <button type="submit" name="edit"
                    class="px-8 py-3 bg-[#2271b1] text-white rounded-lg font-bold text-xs hover:bg-[#135e96] transition-all shadow-md shadow-blue-500/10 uppercase ">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function bukaModalTambah() {
    document.getElementById('modal-tambah').classList.remove('hidden');
    document.getElementById('modal-tambah').classList.add('flex');
}

function bukaModalEdit(u) {
    document.getElementById('edit-id').value = u.id;
    document.getElementById('edit-nama').value = u.nama_lengkap;
    document.getElementById('edit-username').value = u.username;
    document.getElementById('edit-email').value = u.email;
    document.getElementById('edit-preview').src = u.foto ? '../assets/images/' + u.foto : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(u.nama_lengkap) + '&background=002147&color=fff';

    document.getElementById('modal-edit').classList.remove('hidden');
    document.getElementById('modal-edit').classList.add('flex');
}

function tutupModal(idModal) {
    document.getElementById(idModal).classList.add('hidden');
    document.getElementById(idModal).classList.remove('flex');
}

// Auto-hilangkan alert sukses & bersihkan URL
var alertPesan = document.getElementById('alert-pesan');
if (alertPesan) {
    setTimeout(function() {
        alertPesan.style.opacity = '0';
        setTimeout(function() { alertPesan.style.display = 'none'; }, 500);
    }, 3000);
}

if (window.location.search.indexOf('sukses=') !== -1) {
    var urlBersih = window.location.pathname;
    window.history.replaceState({}, document.title, urlBersih);
}

<?php if ($error): ?>
// Buka kembali modal jika terjadi error saat submit
<?php if (isset($_POST['tambah'])): ?>
document.addEventListener('DOMContentLoaded', function() { bukaModalTambah(); });
<?php elseif (isset($_POST['edit'])): ?>
document.addEventListener('DOMContentLoaded', function() { bukaModalEdit(<?= json_encode($_POST, JSON_HEX_APOS | JSON_HEX_QUOT) ?>); });
<?php endif; ?>
<?php endif; ?>
</script>

<?php include 'footer.php'; ?>