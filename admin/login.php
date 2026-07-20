<?php
require_once '../config/database.php';
session_start();

if (isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] === true) {
    header('Location: index.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = "Silakan masukkan username dan password.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_auth'] = true;
            $_SESSION['admin_id'] = $admin['id_admin'];
            $_SESSION['admin_user'] = $admin['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = "Username atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Web Admin - BansosKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { 
            display: flex; align-items: center; justify-content: center; min-height: 100vh;
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
        }
        .login-card { 
            width: 100%; max-width: 420px; padding: 3rem 2.5rem; text-align: center;
            background: rgba(255, 255, 255, 0.98); border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .brand-logo { 
            font-size: 3rem; color: var(--primary-color); margin-bottom: 0.5rem;
            background: -webkit-linear-gradient(45deg, #2563EB, #3B82F6);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-logo"><i class="fa-solid fa-shield-halved"></i></div>
        <h2 style="margin-bottom: 0.5rem; color: #1E293B;">Panel Admin</h2>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Silakan masuk untuk mengelola data bantuan sosial.</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error" style="text-align: left; font-size: 0.9rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <div style="position: relative;">
                    <i class="fa-solid fa-user" style="position: absolute; left: 1.25rem; top: 1.15rem; color: #94A3B8;"></i>
                    <input type="text" name="username" class="form-control" style="padding-left: 3rem; padding-top: 1rem; padding-bottom: 1rem;" placeholder="Username" required autofocus>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <div style="position: relative;">
                    <i class="fa-solid fa-lock" style="position: absolute; left: 1.25rem; top: 1.15rem; color: #94A3B8;"></i>
                    <input type="password" name="password" class="form-control" style="padding-left: 3rem; padding-top: 1rem; padding-bottom: 1rem;" placeholder="Password" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                Masuk Sistem <i class="fa-solid fa-arrow-right" style="margin-left: 0.5rem;"></i>
            </button>
        </form>
        
        <div style="margin-top: 2rem; border-top: 1px solid #E2E8F0; padding-top: 1.5rem;">
            <a href="../index.php" style="color: var(--text-muted); font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Website Publik
            </a>
        </div>
    </div>
</body>
</html>
