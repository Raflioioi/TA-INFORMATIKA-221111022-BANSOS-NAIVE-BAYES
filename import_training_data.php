<?php
require_once 'config/database.php';

$file = fopen("data_training_1000.csv", "r");
if ($file !== FALSE) {
    // Hapus data dummy lama jika ada agar tidak duplikat
    $pdo->exec("DELETE FROM warga WHERE nama LIKE 'Data Warga %'");

    // Skip header
    fgetcsv($file);

    $sql = "INSERT IGNORE INTO warga (nik, no_kk, nama, tempat_tanggal_lahir, jenis_kelamin, status_perkawinan, alamat, kecamatan, kelurahan, pekerjaan, penghasilan, jumlah_tanggungan, kondisi_lantai, kondisi_dinding, kondisi_atap, status_kepemilikan_rumah, pendidikan_terakhir, jumlah_anak_sekolah, kepemilikan_aset, pengeluaran_bulanan, akses_listrik, akses_air, kondisi_kesehatan, status_bantuan) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    $count = 0;
    while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
        try {
            $stmt->execute($data);
            $count++;
        } catch (PDOException $e) {
            echo "Error pada baris $count: " . $e->getMessage() . "\n";
        }
    }
    fclose($file);
    echo "Berhasil mengimpor $count data ke tabel warga.\n";
} else {
    echo "Gagal membuka file data_training_1000.csv\n";
}
?>
