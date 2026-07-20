<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proses_semua'])) {
    $api_host = getenv('API_HOST') ?: 'localhost';
    $api_port = getenv('API_PORT') ?: '5000';
    $url = "http://$api_host:$api_port/predict_all";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 60); // Timeout 60 detik jika data sangat banyak
    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($httpcode !== 200 || !$response) {
        $error_message = "Gagal terhubung ke API Prediksi (Python) untuk memproses massal. Pastikan server Python berjalan di port 5000.";
        if ($response) {
            $err_data = json_decode($response, true);
            if (isset($err_data['error'])) {
                $error_message = htmlspecialchars($err_data['error']);
            }
        }
        $_SESSION['msg'] = $error_message;
        $_SESSION['msg_type'] = "error";
        header('Location: warga.php');
        exit;
    }

    $result = json_decode($response, true);
    if (isset($result['error'])) {
        $_SESSION['msg'] = "Gagal memproses prediksi massal: " . htmlspecialchars($result['error']);
        $_SESSION['msg_type'] = "error";
        header('Location: warga.php');
        exit;
    }

    $success_count = $result['success_count'];
    $layak_count = $result['layak_count'];
    $tidak_layak_count = $result['tidak_layak_count'];

    if ($success_count > 0) {
        $_SESSION['msg'] = "<strong>Proses Prediksi Massal Selesai!</strong><br>
                            Berhasil memproses <strong>$success_count warga</strong>.<br>
                            - Layak: <strong>$layak_count warga</strong><br>
                            - Tidak Layak: <strong>$tidak_layak_count warga</strong>";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['msg'] = "Tidak ada data warga dengan status 'Proses' yang perlu diprediksi.";
        $_SESSION['msg_type'] = "warning";
    }

    header('Location: warga.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_warga'])) {
    $id_warga = (int) $_POST['id_warga'];

    // Ambil data warga yang akan diprediksi
    $stmt = $pdo->prepare("SELECT * FROM warga WHERE id_warga = ?");
    $stmt->execute([$id_warga]);
    $target = $stmt->fetch();

    if (!$target) {
        $_SESSION['msg'] = "Data warga tidak ditemukan.";
        $_SESSION['msg_type'] = "error";
        header('Location: warga.php');
        exit;
    }

    // --- MENGGUNAKAN API PYTHON ---
    $api_host = getenv('API_HOST') ?: 'localhost';
    $api_port = getenv('API_PORT') ?: '5000';
    $url = "http://$api_host:$api_port/predict?id_warga=" . $id_warga;
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($httpcode !== 200 || !$response) {
        $error_message = "Gagal terhubung ke API Prediksi (Python). Pastikan server Python berjalan di port 5000.";
        if ($response) {
            $err_data = json_decode($response, true);
            if (isset($err_data['error'])) {
                $error_message = htmlspecialchars($err_data['error']);
            }
        }
        $_SESSION['msg'] = $error_message;
        $_SESSION['msg_type'] = "error";
        header('Location: warga.php');
        exit;
    }

    $result = json_decode($response, true);
    
    if (isset($result['error'])) {
        $_SESSION['msg'] = "Sistem gagal memprediksi: " . htmlspecialchars($result['error']);
        $_SESSION['msg_type'] = "error";
        header('Location: warga.php');
        exit;
    }

    $predicted_status = $result['predicted_status'];
    $score_layak = $result['score_layak'];
    $score_tidak_layak = $result['score_tidak_layak'];
    $alasan = $result['alasan'];

    // 5. Simpan Hasil Prediksi ke Database
    $update = $pdo->prepare("UPDATE warga SET status_bantuan = ? WHERE id_warga = ?");
    if ($update->execute([$predicted_status, $id_warga])) {
        $_SESSION['msg'] = "<strong>Prediksi Naive Bayes Berhasil!</strong><br>
                            Skor Probabilitas Layak: " . sprintf("%.4e", $score_layak) . "<br>
                            Skor Probabilitas Tidak Layak: " . sprintf("%.4e", $score_tidak_layak) . "<br>
                            Keputusan Sistem: <strong>$predicted_status</strong><br>
                            <small style='display:block; margin-top:8px; color:#0f766e;'><strong>Alasan utama:</strong> Data profil warga ini sangat mengarah pada keputusan <strong>$predicted_status</strong> khususnya pada faktor: <u>$alasan</u>.</small>";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['msg'] = "Gagal menyimpan hasil prediksi ke database.";
        $_SESSION['msg_type'] = "error";
    }

    header('Location: warga.php');
    exit;
} else {
    header('Location: warga.php');
    exit;
}
