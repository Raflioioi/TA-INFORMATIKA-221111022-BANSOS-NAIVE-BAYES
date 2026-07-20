<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header('Location: login.php');
    exit;
}

$id_warga = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$action = $id_warga ? "Edit" : "Tambah";
$msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik = trim($_POST['nik']);
    $no_kk = trim($_POST['no_kk']);
    $nama = trim($_POST['nama']);
    $ttl = trim($_POST['tempat_tanggal_lahir']);
    $jk = $_POST['jenis_kelamin'];
    $status_kawin = $_POST['status_perkawinan'];
    $alamat = trim($_POST['alamat']);
    $kecamatan = $_POST['kecamatan'] ?? null;
    $kelurahan = $_POST['kelurahan'] ?? null;
    $pekerjaan = trim($_POST['pekerjaan'] ?? '');
    $penghasilan = trim($_POST['penghasilan'] ?? '');
    $tanggungan = trim($_POST['jumlah_tanggungan'] ?? '');
    $kondisi_lantai = $_POST['kondisi_lantai'] ?? null;
    $kondisi_dinding = $_POST['kondisi_dinding'] ?? null;
    $kondisi_atap = $_POST['kondisi_atap'] ?? null;
    $status_kepemilikan_rumah = $_POST['status_kepemilikan_rumah'] ?? null;
    $pendidikan_terakhir = $_POST['pendidikan_terakhir'] ?? null;
    $jumlah_anak_sekolah = $_POST['jumlah_anak_sekolah'] ?? null;
    $kepemilikan_aset = $_POST['kepemilikan_aset'] ?? null;
    $pengeluaran_bulanan = $_POST['pengeluaran_bulanan'] ?? null;
    $akses_listrik = $_POST['akses_listrik'] ?? null;
    $akses_air = $_POST['akses_air'] ?? null;
    $kondisi_kesehatan = $_POST['kondisi_kesehatan'] ?? null;
    $status_bantuan = $_POST['status_bantuan'] ?? 'Proses';

    if (empty($nik) || empty($nama)) {
        $msg = "NIK dan Nama wajib diisi.";
    } else {
        if ($id_warga) {
            $sql = "UPDATE warga SET nik=?, no_kk=?, nama=?, tempat_tanggal_lahir=?, 
                    jenis_kelamin=?, status_perkawinan=?, alamat=?, kecamatan=?, kelurahan=?, pekerjaan=?, 
                    penghasilan=?, jumlah_tanggungan=?, kondisi_lantai=?, kondisi_dinding=?, kondisi_atap=?, 
                    status_kepemilikan_rumah=?, pendidikan_terakhir=?, jumlah_anak_sekolah=?, 
                    kepemilikan_aset=?, pengeluaran_bulanan=?, akses_listrik=?, akses_air=?, kondisi_kesehatan=?, 
                    status_bantuan=? WHERE id_warga=?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$nik, $no_kk, $nama, $ttl, $jk, $status_kawin, $alamat, $kecamatan, $kelurahan, $pekerjaan, $penghasilan, $tanggungan, $kondisi_lantai, $kondisi_dinding, $kondisi_atap, $status_kepemilikan_rumah, $pendidikan_terakhir, $jumlah_anak_sekolah, $kepemilikan_aset, $pengeluaran_bulanan, $akses_listrik, $akses_air, $kondisi_kesehatan, $status_bantuan, $id_warga])) {
                $_SESSION['msg'] = "Data profil warga berhasil diperbarui.";
                $_SESSION['msg_type'] = "success";
                header("Location: warga.php");
                exit;
            } else {
                $msg = "Terjadi kesalahan internal. Gagal memperbarui data.";
            }
        } else {
            $sql = "INSERT INTO warga (nik, no_kk, nama, tempat_tanggal_lahir, jenis_kelamin, status_perkawinan, alamat, kecamatan, kelurahan, pekerjaan, penghasilan, jumlah_tanggungan, kondisi_lantai, kondisi_dinding, kondisi_atap, status_kepemilikan_rumah, pendidikan_terakhir, jumlah_anak_sekolah, kepemilikan_aset, pengeluaran_bulanan, akses_listrik, akses_air, kondisi_kesehatan, status_bantuan) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            try {
                if ($stmt->execute([$nik, $no_kk, $nama, $ttl, $jk, $status_kawin, $alamat, $kecamatan, $kelurahan, $pekerjaan, $penghasilan, $tanggungan, $kondisi_lantai, $kondisi_dinding, $kondisi_atap, $status_kepemilikan_rumah, $pendidikan_terakhir, $jumlah_anak_sekolah, $kepemilikan_aset, $pengeluaran_bulanan, $akses_listrik, $akses_air, $kondisi_kesehatan, $status_bantuan])) {
                    $_SESSION['msg'] = "Data identitas warga baru sukses ditambahkan.";
                    $_SESSION['msg_type'] = "success";
                    header("Location: warga.php");
                    exit;
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $msg = "Validasi Gagal: NIK yang Anda masukkan sudah terdaftar di sistem.";
                } else {
                    $msg = "Sistem Error: " . $e->getMessage();
                }
            }
        }
    }
}

$data = [];
if ($id_warga) {
    $stmt = $pdo->prepare("SELECT * FROM warga WHERE id_warga = ?");
    $stmt->execute([$id_warga]);
    $data = $stmt->fetch();
    if (!$data) {
        die("Fatal: ID Data tidak ditemukan di database!");
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $action ?> Warga - BansosKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-nav {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .brand {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .form-section-title {
            font-size: 1.1rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border-color);
        }
    </style>
</head>

<body style="background: #F8FAFC;">
    <div class="admin-nav">
        <div class="brand"><i class="fa-solid fa-shield-halved"></i> Panel Admin Bansos</div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <a href="../index.php" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.85rem;"><i
                    class="fa-solid fa-globe"></i> Lihat Web</a>
        </div>
    </div>

    <div class="container admin-layout" style="max-width: 1400px; padding: 2rem;">
        <aside class="sidebar"
            style="border-radius: 16px; height: calc(100vh - 120px); box-shadow: var(--shadow-sm); padding: 1.5rem 1rem;">
            <nav class="sidebar-nav">
                <a href="index.php" class="sidebar-link"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
                <a href="warga.php" class="sidebar-link active"><i class="fa-solid fa-users"></i> Kelola Warga</a>
                <a href="warga_kecamatan.php" class="sidebar-link"><i class="fa-solid fa-map-location-dot"></i> Filter Kecamatan</a>
            </nav>
        </aside>

        <main class="admin-content" style="padding: 0; padding-left: 2rem;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                <a href="warga.php" class="btn btn-secondary" style="padding: 0.6rem 1rem; border-radius: 50px;"><i
                        class="fa-solid fa-arrow-left"></i> Kembali</a>
                <h2 style="margin: 0; font-size: 2rem; color: #0F172A;"><?= $action ?> Identitas Penerima Bansos</h2>
            </div>

            <?php if ($msg): ?>
                <div class="alert alert-error" style="border-radius: 12px;">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>

            <div class="glass-card"
                style="padding: 2.5rem; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); max-width: 900px;">
                <form method="POST" action="">

                    <h3 class="form-section-title"><i class="fa-solid fa-id-card"></i> Informasi Identitas Pokok</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                        <div class="form-group">
                            <label>Nomor Induk Kependudukan (NIK)</label>
                            <input type="text" name="nik" class="form-control" required minlength="16" maxlength="16"
                                pattern="\d{16}" value="<?= htmlspecialchars($data['nik'] ?? '') ?>"
                                placeholder="16 Digit Angka">
                        </div>
                        <div class="form-group">
                            <label>Nomor Kartu Keluarga (KK)</label>
                            <input type="text" name="no_kk" class="form-control" required minlength="16" maxlength="16"
                                pattern="\d{16}" value="<?= htmlspecialchars($data['no_kk'] ?? '') ?>"
                                placeholder="16 Digit Angka">
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label>Nama Lengkap (Sesuai KTP)</label>
                            <input type="text" name="nama" class="form-control" required
                                value="<?= htmlspecialchars($data['nama'] ?? '') ?>">
                        </div>
                    </div>

                    <h3 class="form-section-title"><i class="fa-solid fa-user-tag"></i> Data Personal & Sosio-Demografis
                    </h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                        <div class="form-group">
                            <label>Tempat, Tanggal Lahir</label>
                            <input type="date" name="tempat_tanggal_lahir" class="form-control"
                                placeholder="Contoh: Jakarta, 12 Agustus 1980" required
                                value="<?= htmlspecialchars($data['tempat_tanggal_lahir'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <div class="radio-group"
                                style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                <?php
                                $opts = ['Laki-laki', 'Perempuan'];
                                foreach ($opts as $opt) {
                                    $chk = (isset($data['jenis_kelamin']) && $data['jenis_kelamin'] == $opt) ? 'checked' : '';
                                    echo "<label style='display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;'><input type=\"radio\" name=\"jenis_kelamin\" value=\"$opt\" $chk required style='margin-top: 0.25rem;'> <span style='font-size: 0.95rem; color: #334155; line-height: 1.4;'>$opt</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Status Perkawinan</label>
                            <div class="radio-group"
                                style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                <?php
                                $opts = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'];
                                foreach ($opts as $opt) {
                                    $chk = (isset($data['status_perkawinan']) && $data['status_perkawinan'] == $opt) ? 'checked' : '';
                                    echo "<label style='display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;'><input type=\"radio\" name=\"status_perkawinan\" value=\"$opt\" $chk required style='margin-top: 0.25rem;'> <span style='font-size: 0.95rem; color: #334155; line-height: 1.4;'>$opt</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label>Alamat Lengkap Domisili</label>
                            <textarea name="alamat" class="form-control" rows="3" required
                                placeholder="Nama Jalan, RT/RW..."><?= htmlspecialchars($data['alamat'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Kecamatan (Kota Malang)</label>
                            <select name="kecamatan" id="kecamatan" class="form-control" required>
                                <option value="" disabled <?= empty($data['kecamatan']) ? 'selected' : '' ?>>Pilih Kecamatan...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Kelurahan</label>
                            <select name="kelurahan" id="kelurahan" class="form-control" required>
                                <option value="" disabled <?= empty($data['kelurahan']) ? 'selected' : '' ?>>Pilih Kelurahan...</option>
                            </select>
                        </div>
                    </div>

                    <h3 class="form-section-title"><i class="fa-solid fa-coins"></i> Kondisi Ekonomi & Kelayakan Bantuan
                    </h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                        <div class="form-group">
                            <label>Pekerjaan Utama</label>
                            <div class="radio-group"
                                style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                <?php
                                $opts = ['Tidak Bekerja', 'Petani / Buruh Tani', 'Buruh Pabrik / Bangunan', 'Wiraswasta / Pedagang', 'Karyawan Swasta', 'PNS / TNI / POLRI'];
                                foreach ($opts as $opt) {
                                    $chk = (isset($data['pekerjaan']) && $data['pekerjaan'] == $opt) ? 'checked' : '';
                                    echo "<label style='display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;'><input type=\"radio\" name=\"pekerjaan\" value=\"$opt\" $chk required style='margin-top: 0.25rem;'> <span style='font-size: 0.95rem; color: #334155; line-height: 1.4;'>$opt</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Perkiraan Penghasilan (Per Bulan)</label>
                            <select name="penghasilan" class="form-control" required>
                                <option value="" disabled <?= empty($data['penghasilan']) ? 'selected' : '' ?>>Pilih
                                    Penghasilan...</option>
                                <?php
                                $penghasilan_opts = [
                                    '< Rp 500.000',
                                    'Rp 500.000 - Rp 1.000.000',
                                    'Rp 1.000.000 - Rp 2.000.000',
                                    'Rp 2.000.000 - Rp 3.000.000',
                                    'Rp 3.000.000 - Rp 4.000.000',
                                    '> Rp 4.000.000'
                                ];
                                foreach ($penghasilan_opts as $opt) {
                                    $sel = (isset($data['penghasilan']) && $data['penghasilan'] == $opt) ? 'selected' : '';
                                    echo "<option value=\"$opt\" $sel>$opt</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Jumlah Tanggungan Keluarga</label>
                            <div class="radio-group"
                                style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                <?php
                                $opts = ['0 - 2 Orang', '3 - 4 Orang', '> 4 Orang'];
                                foreach ($opts as $opt) {
                                    $chk = (isset($data['jumlah_tanggungan']) && $data['jumlah_tanggungan'] == $opt) ? 'checked' : '';
                                    echo "<label style='display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;'><input type=\"radio\" name=\"jumlah_tanggungan\" value=\"$opt\" $chk required style='margin-top: 0.25rem;'> <span style='font-size: 0.95rem; color: #334155; line-height: 1.4;'>$opt</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Kondisi Lantai</label>
                            <div class="radio-group"
                                style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                <?php
                                $opts = ['Tanah / Kayu Kualitas Rendah', 'Semen Kasar / Papan Kayu Biasa', 'Keramik / Marmer / Granit'];
                                foreach ($opts as $opt) {
                                    $chk = (isset($data['kondisi_lantai']) && $data['kondisi_lantai'] == $opt) ? 'checked' : '';
                                    echo "<label style='display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;'><input type=\"radio\" name=\"kondisi_lantai\" value=\"$opt\" $chk required style='margin-top: 0.25rem;'> <span style='font-size: 0.95rem; color: #334155; line-height: 1.4;'>$opt</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Kondisi Dinding</label>
                            <div class="radio-group"
                                style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                <?php
                                $opts = ['Bilik Bambu / Rumbia / Terpal', 'Kayu Murah / Setengah Tembok (Bata tanpa plester)', 'Tembok Penuh (Diplester & Dicat)'];
                                foreach ($opts as $opt) {
                                    $chk = (isset($data['kondisi_dinding']) && $data['kondisi_dinding'] == $opt) ? 'checked' : '';
                                    echo "<label style='display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;'><input type=\"radio\" name=\"kondisi_dinding\" value=\"$opt\" $chk required style='margin-top: 0.25rem;'> <span style='font-size: 0.95rem; color: #334155; line-height: 1.4;'>$opt</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Kondisi Atap</label>
                            <div class="radio-group"
                                style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                <?php
                                $opts = ['Ijuk / Rumbia / Daun', 'Seng Karatan / Asbes Tua', 'Genteng Tanah Liat / Baja Ringan / Genteng Keramik'];
                                foreach ($opts as $opt) {
                                    $chk = (isset($data['kondisi_atap']) && $data['kondisi_atap'] == $opt) ? 'checked' : '';
                                    echo "<label style='display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;'><input type=\"radio\" name=\"kondisi_atap\" value=\"$opt\" $chk required style='margin-top: 0.25rem;'> <span style='font-size: 0.95rem; color: #334155; line-height: 1.4;'>$opt</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Status Kepemilikan Rumah</label>
                            <div class="radio-group"
                                style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                <?php
                                $opts = ['Milik Sendiri / Warisan', 'Sewa / Kontrak (Membayar bulanan/tahunan)', 'Numpang / Bebas Sewa (Fasum / Lahan orang lain)'];
                                foreach ($opts as $opt) {
                                    $chk = (isset($data['status_kepemilikan_rumah']) && $data['status_kepemilikan_rumah'] == $opt) ? 'checked' : '';
                                    echo "<label style='display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;'><input type=\"radio\" name=\"status_kepemilikan_rumah\" value=\"$opt\" $chk required style='margin-top: 0.25rem;'> <span style='font-size: 0.95rem; color: #334155; line-height: 1.4;'>$opt</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Pendidikan Terakhir</label>
                            <div class="radio-group"
                                style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                <?php
                                $opts = ['Tidak Sekolah', 'Tamat SD', 'Tamat SMP', 'Tamat SMA / SMK', 'Sarjana / Perguruan Tinggi'];
                                foreach ($opts as $opt) {
                                    $chk = (isset($data['pendidikan_terakhir']) && $data['pendidikan_terakhir'] == $opt) ? 'checked' : '';
                                    echo "<label style='display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;'><input type=\"radio\" name=\"pendidikan_terakhir\" value=\"$opt\" $chk required style='margin-top: 0.25rem;'> <span style='font-size: 0.95rem; color: #334155; line-height: 1.4;'>$opt</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Jumlah Anak Sekolah</label>
                            <div class="radio-group"
                                style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                <?php
                                $opts = ['Tidak Ada Anak Sekolah', '1 - 2 Anak Sekolah', 'Lebih dari 2 Anak Sekolah'];
                                foreach ($opts as $opt) {
                                    $chk = (isset($data['jumlah_anak_sekolah']) && $data['jumlah_anak_sekolah'] == $opt) ? 'checked' : '';
                                    echo "<label style='display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;'><input type=\"radio\" name=\"jumlah_anak_sekolah\" value=\"$opt\" $chk required style='margin-top: 0.25rem;'> <span style='font-size: 0.95rem; color: #334155; line-height: 1.4;'>$opt</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Kepemilikan Aset</label>
                            <div class="radio-group"
                                style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                <?php
                                $opts = ['Tidak Ada Aset Sama Sekali', 'Hanya Aset Kecil (Sepeda / HP Murah / Unggas)', 'Aset Menengah Bawah (Motor Tua < Rp 3 Juta / Kambing)', 'Aset Menengah Atas (Motor Baru / Ternak Sapi)', 'Aset Mewah (Mobil / Lahan Kosong / Emas Murni)'];
                                foreach ($opts as $opt) {
                                    $chk = (isset($data['kepemilikan_aset']) && $data['kepemilikan_aset'] == $opt) ? 'checked' : '';
                                    echo "<label style='display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;'><input type=\"radio\" name=\"kepemilikan_aset\" value=\"$opt\" $chk required style='margin-top: 0.25rem;'> <span style='font-size: 0.95rem; color: #334155; line-height: 1.4;'>$opt</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Pengeluaran Bulanan</label>
                            <div class="radio-group"
                                style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                <?php
                                $opts = ['< Rp 500.000', 'Rp 500.000 - Rp 1.000.000', 'Rp 1.000.000 - Rp 2.000.000', 'Rp 2.000.000 - Rp 3.000.000', 'Rp 3.000.000 - Rp 4.000.000', '> Rp 4.000.000'];
                                foreach ($opts as $opt) {
                                    $chk = (isset($data['pengeluaran_bulanan']) && $data['pengeluaran_bulanan'] == $opt) ? 'checked' : '';
                                    echo "<label style='display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;'><input type=\"radio\" name=\"pengeluaran_bulanan\" value=\"$opt\" $chk required style='margin-top: 0.25rem;'> <span style='font-size: 0.95rem; color: #334155; line-height: 1.4;'>$opt</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Daya Listrik</label>
                            <div class="radio-group"
                                style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                <?php
                                $opts = ['Numpang / Tidak Ada Listrik', 'Listrik 450 VA', 'Listrik 900 VA', 'Listrik 1300 VA', 'Listrik > 1300 VA'];
                                foreach ($opts as $opt) {
                                    $chk = (isset($data['akses_listrik']) && $data['akses_listrik'] == $opt) ? 'checked' : '';
                                    echo "<label style='display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;'><input type=\"radio\" name=\"akses_listrik\" value=\"$opt\" $chk required style='margin-top: 0.25rem;'> <span style='font-size: 0.95rem; color: #334155; line-height: 1.4;'>$opt</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Sumber Air Bersih</label>
                            <div class="radio-group"
                                style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                <?php
                                $opts = ['Mata Air Tidak Terlindung / Sungai / Air Hujan', 'Sumur Gali Terlindung / Pompa Tangan', 'PAM / Leding Meteran / Air Kemasan Bermerk'];
                                foreach ($opts as $opt) {
                                    $chk = (isset($data['akses_air']) && $data['akses_air'] == $opt) ? 'checked' : '';
                                    echo "<label style='display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;'><input type=\"radio\" name=\"akses_air\" value=\"$opt\" $chk required style='margin-top: 0.25rem;'> <span style='font-size: 0.95rem; color: #334155; line-height: 1.4;'>$opt</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Kondisi Kesehatan</label>
                            <div class="radio-group"
                                style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                <?php
                                $opts = ['Sehat Fisik & Jasmani', 'Rentan Sakit / Lansia / Ibu Hamil', 'Disabilitas Berat / Penyakit Menahun (Stroke/TBC)'];
                                foreach ($opts as $opt) {
                                    $chk = (isset($data['kondisi_kesehatan']) && $data['kondisi_kesehatan'] == $opt) ? 'checked' : '';
                                    echo "<label style='display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;'><input type=\"radio\" name=\"kondisi_kesehatan\" value=\"$opt\" $chk required style='margin-top: 0.25rem;'> <span style='font-size: 0.95rem; color: #334155; line-height: 1.4;'>$opt</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label style="color: var(--primary-color);">Status Keputusan Penyaluran</label>
                            <select name="status_bantuan" class="form-control" required
                                style="border-width: 2px; border-color: var(--primary-color); background: #EFF6FF; font-weight: 600;">
                                <?php
                                $status_b = ['Proses', 'Layak', 'Tidak Layak'];
                                foreach ($status_b as $sb) {
                                    $sel = (isset($data['status_bantuan']) && $data['status_bantuan'] == $sb) ? 'selected' : '';
                                    echo "<option value=\"$sb\" $sel>$sb</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div
                        style="display: flex; gap: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <button type="submit" class="btn btn-primary"
                            style="padding: 1rem 2.5rem; font-size: 1.1rem; border-radius: 50px;"><i
                                class="fa-solid fa-floppy-disk"></i> Integrasikan Data</button>
                        <a href="warga.php" class="btn btn-secondary"
                            style="padding: 1rem 2.5rem; font-size: 1.1rem; border-radius: 50px;">Batal</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        const kelurahanData = {
            "Klojen": ["Klojen", "Rampal Celaket", "Samaan", "Kiduldalem", "Sukoharjo", "Kasin", "Oro-oro Dowo", "Bareng", "Gading Kasri", "Penanggungan", "Kauman"],
            "Blimbing": ["Blimbing", "Balearjosari", "Arjosari", "Purwodadi", "Polowijen", "Pandanwangi", "Purwantoro", "Bunulrejo", "Kesatrian", "Polehan", "Jodipan"],
            "Lowokwaru": ["Tasikmadu", "Tunggulwulung", "Merjosari", "Tlogomas", "Dinoyo", "Sumbersari", "Ketawanggede", "Jatimulyo", "Tunjungsekar", "Mojolangu", "Tulusrejo", "Lowokwaru"],
            "Sukun": ["Ciptomulyo", "Gadang", "Bandungrejosari", "Sukun", "Tanjungrejo", "Pisangcandi", "Bandulan", "Karangbesuki", "Mulyorejo", "Bakalankrajan", "Kebonsari"],
            "Kedungkandang": ["Kedungkandang", "Wonokoyo", "Buring", "Kotalama", "Mergosono", "Bumiayu", "Arjowinangun", "Tlogowaru", "Lesanpuro", "Sawojajar", "Madyopuro"]
        };

        const kecSelect = document.getElementById('kecamatan');
        const kelSelect = document.getElementById('kelurahan');
        const currentKec = "<?= addslashes($data['kecamatan'] ?? '') ?>";
        const currentKel = "<?= addslashes($data['kelurahan'] ?? '') ?>";

        // Populate Kecamatan
        for (const kec in kelurahanData) {
            const option = document.createElement('option');
            option.value = kec;
            option.textContent = kec;
            if (kec === currentKec) option.selected = true;
            kecSelect.appendChild(option);
        }

        // Function to populate Kelurahan based on Kecamatan
        function populateKelurahan(kec) {
            kelSelect.innerHTML = '<option value="" disabled <?= empty($data['kelurahan']) ? 'selected' : '' ?>>Pilih Kelurahan...</option>';
            if (kec && kelurahanData[kec]) {
                kelurahanData[kec].forEach(kel => {
                    const option = document.createElement('option');
                    option.value = kel;
                    option.textContent = kel;
                    if (kel === currentKel) option.selected = true;
                    kelSelect.appendChild(option);
                });
            }
        }

        // Initial population
        if (currentKec) {
            populateKelurahan(currentKec);
        }

        // Event listener for changes
        kecSelect.addEventListener('change', function() {
            populateKelurahan(this.value);
        });
    </script>
</body>

</html>