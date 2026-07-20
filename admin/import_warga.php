<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_csv'])) {
    
    // Allow basic mimes representing comma separated text.
    $fileMimes = array(
        'text/x-comma-separated-values',
        'text/comma-separated-values',
        'application/octet-stream',
        'application/vnd.ms-excel',
        'application/x-csv',
        'text/x-csv',
        'text/csv',
        'application/csv',
        'application/excel',
        'application/vnd.msexcel',
        'text/plain'
    );
    
    if (!empty($_FILES['file_csv']['name']) && in_array($_FILES['file_csv']['type'], $fileMimes)) {
        if (is_uploaded_file($_FILES['file_csv']['tmp_name'])) {
            $csvFile = fopen($_FILES['file_csv']['tmp_name'], 'r');
            // Abaikan baris pertama (yang berisi column header)
            fgetcsv($csvFile);
            
            $imported_niks = [];
            $success_count = 0;
            $fail_count = 0;
            $pdo->beginTransaction();
            try {
                // Update schema to include all columns for import
                $stmt = $pdo->prepare("INSERT INTO warga (nik, no_kk, nama, tempat_tanggal_lahir, jenis_kelamin, status_perkawinan, alamat, kecamatan, kelurahan, pekerjaan, penghasilan, jumlah_tanggungan, kondisi_lantai, kondisi_dinding, kondisi_atap, status_kepemilikan_rumah, pendidikan_terakhir, jumlah_anak_sekolah, kepemilikan_aset, pengeluaran_bulanan, akses_listrik, akses_air, kondisi_kesehatan, status_bantuan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Proses')");
                
                // Prepare duplicate check statement outside the loop for efficiency
                $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM warga WHERE nik = ?");
                
                while (($line = fgetcsv($csvFile)) !== FALSE) {
                    if (count($line) >= 23) {
                        $nik = trim($line[0]);
                        $kk = trim($line[1]);
                        $nama = trim($line[2]);
                        $ttl = trim($line[3]);
                        $jk = trim($line[4]);
                        $status_kawin = trim($line[5]);
                        $alamat = trim($line[6]);
                        $kecamatan = trim($line[7]);
                        $kelurahan = trim($line[8]);
                        $pekerjaan = trim($line[9]);
                        $penghasilan = trim($line[10]);
                        $tanggungan = trim($line[11]);
                        $kondisi_lantai = trim($line[12]);
                        $kondisi_dinding = trim($line[13]);
                        $kondisi_atap = trim($line[14]);
                        $status_kepemilikan_rumah = trim($line[15]);
                        $pendidikan_terakhir = trim($line[16]);
                        $jumlah_anak_sekolah = trim($line[17]);
                        $kepemilikan_aset = trim($line[18]);
                        $pengeluaran_bulanan = trim($line[19]);
                        $akses_listrik = trim($line[20]);
                        $akses_air = trim($line[21]);
                        $kondisi_kesehatan = trim($line[22]);

                        // Handle scientific notation from Excel (e.g., 3.50124E+15)
                        if (stripos($nik, 'e') !== false) {
                            $nik = number_format((float)$nik, 0, '', '');
                        }
                        if (stripos($kk, 'e') !== false) {
                            $kk = number_format((float)$kk, 0, '', '');
                        }
                        
                        if (empty($nik)) {
                            $fail_count++;
                            continue;
                        }

                        // 1. Cek duplikasi di internal file CSV yang sedang diproses dalam batch ini
                        if (in_array($nik, $imported_niks)) {
                            $fail_count++;
                            continue;
                        }
                        
                        // 2. Cek duplikasi di database
                        $checkStmt->execute([$nik]);
                        $exists = $checkStmt->fetchColumn() > 0;
                        
                        if (!$exists) {
                            $stmt->execute([$nik, $kk, $nama, $ttl, $jk, $status_kawin, $alamat, $kecamatan, $kelurahan, $pekerjaan, $penghasilan, $tanggungan, $kondisi_lantai, $kondisi_dinding, $kondisi_atap, $status_kepemilikan_rumah, $pendidikan_terakhir, $jumlah_anak_sekolah, $kepemilikan_aset, $pengeluaran_bulanan, $akses_listrik, $akses_air, $kondisi_kesehatan]);
                            $imported_niks[] = $nik; // Catat agar baris berikutnya dengan NIK sama dilewati
                            $success_count++;
                        } else {
                            $fail_count++;
                        }
                    }
                }
                
                $pdo->commit();
                
                if ($success_count > 0) {
                    $_SESSION['msg'] = "Impor Selesai! {$success_count} data warga baru ditambahkan." . ($fail_count > 0 ? " (Terdapat {$fail_count} terindikasi NIK duplikat/gagal dilewati)." : "");
                    $_SESSION['msg_type'] = "success";
                } else {
                    $_SESSION['msg'] = "Gagal mengimpor. {$fail_count} baris ditolak karena format tidak valid atau NIK telah terdaftar sebelumnya.";
                    $_SESSION['msg_type'] = "error";
                }
                
            } catch (Exception $e) {
                $pdo->rollBack();
                $_SESSION['msg'] = "Kesalahan Sistem CSV: " . $e->getMessage();
                $_SESSION['msg_type'] = "error";
            }
            fclose($csvFile);
        } else {
            $_SESSION['msg'] = "Masalah pengunggahan file.";
            $_SESSION['msg_type'] = "error";
        }
    } else {
        $_SESSION['msg'] = "Format tidak sah! Harap upload menggunakan file dengan akhiran .csv";
        $_SESSION['msg_type'] = "error";
    }
} else {
    $_SESSION['msg'] = "Tidak ada aksi yang dikirimkan.";
    $_SESSION['msg_type'] = "error";
}

header("Location: warga.php");
exit;
?>
