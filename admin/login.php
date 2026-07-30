<?php include '../includes/koneksi.php'; ?>
<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Use Prepared Statements to prevent SQL Injection
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $query = $result->fetch_assoc();
    
    // Check password (supporting both old MD5 and new password_hash for migration)
    if ($query) {
        $authenticated = false;
        
        // Try password_verify first (new way)
        if (password_verify($password, $query['password'])) {
            $authenticated = true;
        } 
        // Fallback to MD5 (old way) - then auto-migrate to password_hash
        elseif (MD5($password) === $query['password']) {
            $authenticated = true;
            
            // Auto-migrate to secure hashing
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            $migrate_stmt = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
            $migrate_stmt->bind_param("si", $new_hash, $query['id']);
            $migrate_stmt->execute();
        }

        if ($authenticated) {
            $_SESSION['admin'] = $query['username'];
            session_regenerate_id(true);
            header('Location: index.php');
            exit();
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Tefa Store Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/jpeg" href="../assets/images/logo-smk-antartika-2-sidoarjo.jpg">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-white overflow-hidden">

    <div class="flex h-screen w-full">
        
        <!-- LEFT SIDE: LOGIN FORM -->
        <div class="w-full lg:w-[40%] flex flex-col px-8 md:px-20 lg:px-24 py-12 bg-white h-full relative">
            
            <!-- Logo Section -->
            <div class="mb-16">
                <div class="flex items-center gap-3">
                    <img src="../assets/images/logo-smk-antartika-2-sidoarjo.jpg" alt="Logo SMK" class="w-12 h-12 object-contain">
                </div>
            </div>

            <!-- Login Text Section -->
            <div class="mb-10">
                <h1 class="text-3xl font-extrabold text-[#002147] mb-2 ">Masuk</h1>
                <p class="text-gray-400 text-sm font-medium">Login untuk melanjutkan</p>
            </div>

            <!-- Error Alert -->
            <?php if (isset($error)): ?>
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl flex items-center gap-3 animate-pulse">
                    <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                    <span class="text-red-700 text-sm font-semibold"><?= $error ?></span>
                </div>
            <?php endif; ?>

            <!-- Form Section -->
            <form method="POST" autocomplete="off" class="space-y-6">
                <!-- Username -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-[#002147]  ml-1">Username</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#002147] transition-colors">
                            <i class="fa-solid fa-user text-sm"></i>
                        </div>
                        <input type="text" name="username" required placeholder="Masukkan username anda" 
                            class="w-full pl-12 pr-5 py-4 bg-gray-50 border-0 rounded-md focus:outline-none focus:ring-2 focus:ring-[#002147]/10 focus:bg-white text-sm font-medium transition-all placeholder:text-gray-300">
                    </div>
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-[#002147]  ml-1">Password</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#002147] transition-colors">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input type="password" name="password" required placeholder="••••••••" 
                            class="w-full pl-12 pr-5 py-4 bg-gray-50 border-0 rounded-md focus:outline-none focus:ring-2 focus:ring-[#002147]/10 focus:bg-white text-sm font-medium transition-all placeholder:text-gray-300 0.3em]">
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-4">
                    <button type="submit" 
                        class="w-full bg-[#002147] text-white py-4 rounded-lg font-semibold hover:bg-blue-900 transition-all active:scale-[0.98] text-md">
                        Masuk
                    </button>
                </div>
            </form>
        </div>

        <!-- RIGHT SIDE: ILLUSTRATION -->
        <div class="hidden lg:flex w-[60%] bg-[#002147] relative overflow-hidden items-center justify-center">
            <img src="../assets/images/login-tefa.webp" alt="Login Illustration" class="max-w-full h-full object-cover">
        </div>

    </div>

</body>
</html>
