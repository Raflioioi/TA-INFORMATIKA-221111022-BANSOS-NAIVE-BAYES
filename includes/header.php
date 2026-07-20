<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_url = "http://localhost/bansos-app"; // Disesuaikan jika folder berbeda
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Bantuan Sosial</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Vanilla stylesheet -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <nav class="navbar" style="background-color: #113058ff; padding: 0.8rem 0; border-bottom: none;">
        <div class="nav-container" style="display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; width: 100%;">
            <a href="<?php echo $base_url; ?>/index.php" class="nav-brand" style="color: white; font-size: 1.8rem; font-weight: 700; display: flex; align-items: center; gap: 0.75rem; text-decoration: none; letter-spacing: 0.5px;">
                <img src="<?php echo $base_url; ?>/assets/images/logo.png" alt="Logo BMK" style="height: 45px; width: auto; object-fit: contain;">
                BMK
            </a>
            <div class="nav-links" style="display: flex; gap: 1.5rem; align-items: center; font-size: 0.85rem;">
                <a href="<?php echo $base_url; ?>/index.php" style="color: white; text-decoration: none;">Beranda</a>
                <a href="<?php echo $base_url; ?>/index.php#kontak" style="color: white; text-decoration: none;">Kontak</a>
                <?php if(isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] === true): ?>
                    <a href="<?php echo $base_url; ?>/admin/index.php" style="background-color: #e11d48; color: white; padding: 0.5rem 1.5rem; border-radius: 20px; text-decoration: none;">Dashboard</a>
                    <a href="<?php echo $base_url; ?>/admin/logout.php" style="color: white; margin-left: 1rem; text-decoration: none;">Logout</a>
                <?php else: ?>
                    <a href="<?php echo $base_url; ?>/admin/login.php" style="background-color: #e11d48; color: white; padding: 0.5rem 1.5rem; border-radius: 20px; text-decoration: none; font-weight: 500;">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="main-content">
