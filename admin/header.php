<?php
session_start();
include '../includes/koneksi.php';

// Auth Check
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

$halaman_admin = basename($_SERVER['PHP_SELF']);

// Use Prepared Statement for fetching admin data
$stmt_admin = $conn->prepare("SELECT * FROM admin WHERE username = ?");
$stmt_admin->bind_param("s", $_SESSION['admin']);
$stmt_admin->execute();
$admin_data = $stmt_admin->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Tefa Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/jpeg" href="../assets/images/logo-smk-antartika-2-sidoarjo.jpg">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f0f2f5; }
        
        /* WordPress Table Style */
        .wp-table { width: 100%; border-collapse: collapse; background: white; }
        .wp-table th { text-align: left; padding: 12px 15px; border-bottom: 1px solid #dcdcde; font-size: 13px; color: #2c3338; font-weight: 600; }
        .wp-table td { padding: 12px 15px; border-bottom: 1px solid #f0f0f1; font-size: 13px; color: #50575e; vertical-align: middle; }
        .wp-table tr:hover { background-color: #f6f7f7; }
        
        /* WP Card */
        .wp-card { background: white; border: 1px solid #dcdcde; box-shadow: 0 1px 1px rgba(0,0,0,.04); }
    </style>
</head>
<body class="antialiased">

    <!-- SIDEBAR -->
    <?php include 'sidebar.php'; ?>

    <!-- MAIN WRAPPER -->
    <div class="lg:ml-64 min-h-screen flex flex-col">
        
        <!-- TOPBAR -->
        <header class="h-12 bg-white border-b border-[#dcdcde] flex items-center justify-between px-4 sticky top-0 z-50">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="lg:hidden text-[#1d2327] hover:text-[#2271b1]">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <div class="hidden md:flex items-center gap-2 text-xs font-medium text-[#1d2327]">
                    <i class="fa-solid fa-house"></i>
                    <span class="opacity-50">/</span>
                    <span><?= ucwords(str_replace('.php', '', $halaman_admin)) ?></span>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <!-- User Profile Dropdown -->
                <div class="relative group">
                    <button class="flex items-center gap-2 text-[#1d2327] hover:text-[#2271b1] transition-colors">
                        <span class="text-xs font-bold hidden sm:inline">Halo, <?= $admin_data['nama_lengkap'] ?></span>
                        <img src="<?= $admin_data['foto'] ? '../assets/images/' . $admin_data['foto'] : 'https://ui-avatars.com/api/?name=' . urlencode($admin_data['nama_lengkap']) . '&background=002147&color=fff' ?>" 
                            alt="Avatar" class="w-7 h-7 rounded-full border border-gray-200">
                    </button>
                    <!-- Dropdown Content -->
                    <div class="absolute right-0 top-full mt-2 w-48 bg-white border border-[#dcdcde] shadow-xl rounded-lg py-2 invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all transform origin-top-right scale-95 group-hover:scale-100">
                        <a href="profil.php" class="flex items-center gap-3 px-4 py-2 text-xs font-medium text-[#1d2327] hover:bg-gray-50">
                            <i class="fa-solid fa-user text-gray-400"></i> Profil Saya
                        </a>
                        <a href="ganti_password.php" class="flex items-center gap-3 px-4 py-2 text-xs font-medium text-[#1d2327] hover:bg-gray-50">
                            <i class="fa-solid fa-lock text-gray-400"></i> Ganti Password
                        </a>
                        <hr class="my-1 border-gray-100">
                        <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')" class="flex items-center gap-3 px-4 py-2 text-xs font-bold text-red-500 hover:bg-red-50">
                            <i class="fa-solid fa-power-off"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- CONTENT AREA -->
        <main class="p-4 md:p-8">
