<?php
$halaman_admin = basename($_SERVER['PHP_SELF']);

// Use Prepared Statement for fetching admin data
$stmt_admin_sidebar = $conn->prepare("SELECT * FROM admin WHERE username = ?");
$stmt_admin_sidebar->bind_param("s", $_SESSION['admin']);
$stmt_admin_sidebar->execute();
$admin_data = $stmt_admin_sidebar->get_result()->fetch_assoc();

$foto_admin = $admin_data['foto'] ? '../assets/images/' . $admin_data['foto'] : 'https://ui-avatars.com/api/?name=' . urlencode($admin_data['nama_lengkap']) . '&background=002147&color=fff';
?>

<!-- SIDEBAR -->
<aside id="admin-sidebar" class="fixed top-0 left-0 h-full w-64 bg-[#1d2327] text-[#c3c4c7] z-[100] transition-transform duration-300 -translate-x-full lg:translate-x-0 overflow-y-auto">
    <div class="h-16 flex items-center px-6 bg-[#2c3338] mb-4">
        <h3 class="text-lg font-bold text-white/50">Admin Panel</h3>
    </div>

    <!-- MENU LIST -->
    <nav class="px-3 space-y-1">

        <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded hover:bg-[#2c3338] hover:text-white transition-all group <?= $halaman_admin == 'index.php' ? 'bg-[#2271b1] text-white' : '' ?>">
            <i class="fa-solid fa-gauge text-sm <?= $halaman_admin == 'index.php' ? 'text-white' : 'text-[#646970] group-hover:text-white' ?>"></i>
            <span class="text-sm font-medium">Dashboard</span>
        </a>

        <a href="kategori.php" class="flex items-center gap-3 px-3 py-2.5 rounded hover:bg-[#2c3338] hover:text-white transition-all group <?= $halaman_admin == 'kategori.php' ? 'bg-[#2271b1] text-white' : '' ?>">
            <i class="fa-solid fa-tags text-sm <?= $halaman_admin == 'kategori.php' ? 'text-white' : 'text-[#646970] group-hover:text-white' ?>"></i>
            <span class="text-sm font-medium">Kategori</span>
        </a>

        <a href="tambah.php" class="flex items-center gap-3 px-3 py-2.5 rounded hover:bg-[#2c3338] hover:text-white transition-all group <?= $halaman_admin == 'tambah.php' ? 'bg-[#2271b1] text-white' : '' ?>">
            <i class="fa-solid fa-circle-plus text-sm <?= $halaman_admin == 'tambah.php' ? 'text-white' : 'text-[#646970] group-hover:text-white' ?>"></i>
            <span class="text-sm font-medium">Tambah Produk</span>
        </a>

        <a href="ulasan.php" class="flex items-center gap-3 px-3 py-2.5 rounded hover:bg-[#2c3338] hover:text-white transition-all group <?= $halaman_admin == 'ulasan.php' ? 'bg-[#2271b1] text-white' : '' ?>">
            <i class="fa-solid fa-star text-sm <?= $halaman_admin == 'ulasan.php' ? 'text-white' : 'text-[#646970] group-hover:text-white' ?>"></i>
            <span class="text-sm font-medium">Ulasan</span>
        </a>

        <a href="profil.php" class="flex items-center gap-3 px-3 py-2.5 rounded hover:bg-[#2c3338] hover:text-white transition-all group <?= $halaman_admin == 'profil.php' ? 'bg-[#2271b1] text-white' : '' ?>">
            <i class="fa-solid fa-user-gear text-sm <?= $halaman_admin == 'profil.php' ? 'text-white' : 'text-[#646970] group-hover:text-white' ?>"></i>
            <span class="text-sm font-medium">Profil</span>
        </a>

        <a href="ganti_password.php" class="flex items-center gap-3 px-3 py-2.5 rounded hover:bg-[#2c3338] hover:text-white transition-all group <?= $halaman_admin == 'ganti_password.php' ? 'bg-[#2271b1] text-white' : '' ?>">
            <i class="fa-solid fa-lock text-sm <?= $halaman_admin == 'ganti_password.php' ? 'text-white' : 'text-[#646970] group-hover:text-white' ?>"></i>
            <span class="text-sm font-medium">Ganti Password</span>
        </a>

        <a href="log.php" class="flex items-center gap-3 px-3 py-2.5 rounded hover:bg-[#2c3338] hover:text-white transition-all group <?= $halaman_admin == 'log.php' ? 'bg-[#2271b1] text-white' : '' ?>">
            <i class="fa-solid fa-clock-rotate-left text-sm <?= $halaman_admin == 'log.php' ? 'text-white' : 'text-[#646970] group-hover:text-white' ?>"></i>
            <span class="text-sm font-medium">Log Aktivitas</span>
        </a>

    </nav>

</aside>

<!-- MOBILE OVERLAY -->
<div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/60 z-[90] hidden lg:hidden backdrop-blur-sm transition-all duration-300 opacity-0 pointer-events-none"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        if (sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden', 'opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            setTimeout(() => overlay.classList.add('hidden'), 300);
        }
    }
</script>