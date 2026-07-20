<?php
require_once 'config/database.php';

try {
    $sql = "
    ALTER TABLE warga 
    ADD COLUMN kondisi_tempat_tinggal VARCHAR(100) DEFAULT NULL AFTER jumlah_tanggungan,
    ADD COLUMN status_kepemilikan_rumah VARCHAR(100) DEFAULT NULL AFTER kondisi_tempat_tinggal,
    ADD COLUMN pendidikan_terakhir VARCHAR(100) DEFAULT NULL AFTER status_kepemilikan_rumah,
    ADD COLUMN jumlah_anak_sekolah VARCHAR(50) DEFAULT NULL AFTER pendidikan_terakhir,
    ADD COLUMN kepemilikan_aset VARCHAR(100) DEFAULT NULL AFTER jumlah_anak_sekolah,
    ADD COLUMN pengeluaran_bulanan VARCHAR(50) DEFAULT NULL AFTER kepemilikan_aset,
    ADD COLUMN akses_listrik_air VARCHAR(100) DEFAULT NULL AFTER pengeluaran_bulanan,
    ADD COLUMN kondisi_kesehatan VARCHAR(100) DEFAULT NULL AFTER akses_listrik_air;
    ";
    
    $pdo->exec($sql);
    echo "Migration successful: 8 new columns added to 'warga' table.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Migration already applied (columns exist).\n";
    } else {
        echo "Migration failed: " . $e->getMessage() . "\n";
    }
}
?>
