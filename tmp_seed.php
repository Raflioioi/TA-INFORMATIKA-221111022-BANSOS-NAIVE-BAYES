<?php
require_once __DIR__ . '/config/database.php';

// Hapus data lama
$pdo->exec("TRUNCATE TABLE warga"); 

$dummy_data = [];

function getRandom($array) {
    return $array[array_rand($array)];
}

// Generate Profil Rasional
function generateProfile($id, $is_layak) {
    $nik = ($is_layak ? "333333" : "444444") . "3333" . str_pad($id, 6, "0", STR_PAD_LEFT);
    $kk = ($is_layak ? "333333" : "444444") . "3333" . str_pad($id + 100, 6, "0", STR_PAD_LEFT);
    $tahun = rand($is_layak ? 1960 : 1975, $is_layak ? 1990 : 1995);
    $bulan = str_pad(rand(1, 12), 2, "0", STR_PAD_LEFT);
    $hari = str_pad(rand(1, 28), 2, "0", STR_PAD_LEFT);
    $ttl = "$tahun-$bulan-$hari";
    $jk = getRandom(['Laki-laki', 'Perempuan']);
    
    if ($is_layak) {
        $nama = "Data Training Layak $id";
        $status_kawin = getRandom(['Kawin', 'Cerai Hidup', 'Cerai Mati']);
        $alamat = "Desa Miskin RT " . rand(1, 10) . " RW " . rand(1, 5);
        
        $pekerjaan = getRandom(['Tidak Bekerja', 'Petani / Buruh Tani', 'Buruh Pabrik / Bangunan']);
        if ($pekerjaan == 'Tidak Bekerja') {
            $penghasilan = '< Rp 500.000';
            $pengeluaran = getRandom(['< Rp 500.000', 'Rp 500.000 - Rp 1.000.000']);
        } else {
            $penghasilan = getRandom(['< Rp 500.000', 'Rp 500.000 - Rp 1.000.000']);
            $pengeluaran = $penghasilan;
        }
        
        $tanggungan = getRandom(['3 - 4 Orang', '> 4 Orang']);
        $kondisi_lantai = getRandom(['Tanah / Kayu Kualitas Rendah', 'Semen Kasar / Papan Kayu Biasa']);
        $kondisi_dinding = getRandom(['Bilik Bambu / Rumbia / Terpal', 'Kayu Murah / Setengah Tembok (Bata tanpa plester)']);
        $kondisi_atap = getRandom(['Ijuk / Rumbia / Daun', 'Seng Karatan / Asbes Tua']);
        $status_rumah = getRandom(['Numpang / Bebas Sewa (Fasum / Lahan orang lain)', 'Sewa / Kontrak (Membayar bulanan/tahunan)']);
        $pendidikan = getRandom(['Tidak Sekolah', 'Tamat SD', 'Tamat SMP']);
        $anak_sekolah = getRandom(['1 - 2 Anak Sekolah', 'Lebih dari 2 Anak Sekolah']);
        $aset = getRandom(['Tidak Ada Aset Sama Sekali', 'Hanya Aset Kecil (Sepeda / HP Murah / Unggas)']);
        $listrik = getRandom(['Numpang / Tidak Ada Listrik', 'Listrik 450 VA']);
        $air = getRandom(['Mata Air Tidak Terlindung / Sungai / Air Hujan', 'Sumur Gali Terlindung / Pompa Tangan']);
        $kesehatan = getRandom(['Disabilitas Berat / Penyakit Menahun (Stroke/TBC)', 'Rentan Sakit / Lansia / Ibu Hamil', 'Sehat Fisik & Jasmani']);
        $status_bantuan = 'Layak';
        
    } else {
        $nama = "Data Training Tidak Layak $id";
        $status_kawin = getRandom(['Kawin', 'Belum Kawin']);
        $alamat = "Perumahan Mapan Blok " . chr(rand(65, 90)) . " No " . rand(1, 50);
        
        $pekerjaan = getRandom(['Wiraswasta / Pedagang', 'Karyawan Swasta', 'PNS / TNI / POLRI']);
        $penghasilan = getRandom(['Rp 2.000.000 - Rp 3.000.000', 'Rp 3.000.000 - Rp 4.000.000', '> Rp 4.000.000']);
        
        // Pengeluaran berkorelasi dengan penghasilan
        if ($penghasilan == '> Rp 4.000.000') {
            $pengeluaran = getRandom(['Rp 3.000.000 - Rp 4.000.000', '> Rp 4.000.000']);
        } else {
            $pengeluaran = $penghasilan; 
        }
        
        $tanggungan = getRandom(['0 - 2 Orang', '3 - 4 Orang']);
        $kondisi_lantai = getRandom(['Semen Kasar / Papan Kayu Biasa', 'Keramik / Marmer / Granit']);
        $kondisi_dinding = getRandom(['Tembok Penuh (Diplester & Dicat)']);
        $kondisi_atap = getRandom(['Genteng Tanah Liat / Baja Ringan / Genteng Keramik']);
        $status_rumah = getRandom(['Milik Sendiri / Warisan']);
        $pendidikan = getRandom(['Tamat SMA / SMK', 'Sarjana / Perguruan Tinggi']);
        $anak_sekolah = getRandom(['Tidak Ada Anak Sekolah', '1 - 2 Anak Sekolah']);
        $aset = getRandom(['Aset Menengah Atas (Motor Baru / Ternak Sapi)', 'Aset Mewah (Mobil / Lahan Kosong / Emas Murni)']);
        $listrik = getRandom(['Listrik 900 VA', 'Listrik 1300 VA', 'Listrik > 1300 VA']);
        $air = getRandom(['PAM / Leding Meteran / Air Kemasan Bermerk']);
        $kesehatan = getRandom(['Sehat Fisik & Jasmani']);
        $status_bantuan = 'Tidak Layak';
    }

    return [$nik, $kk, $nama, $ttl, $jk, $status_kawin, $alamat, $pekerjaan, $penghasilan, $tanggungan, $kondisi_lantai, $kondisi_dinding, $kondisi_atap, $status_rumah, $pendidikan, $anak_sekolah, $aset, $pengeluaran, $listrik, $air, $kesehatan, $status_bantuan];
}

for ($i = 1; $i <= 100; $i++) {
    $dummy_data[] = generateProfile($i, true);
}
for ($i = 1; $i <= 100; $i++) {
    $dummy_data[] = generateProfile($i, false);
}

// Tambahkan 2 Data Uji Coba (Proses)
$dummy_data[] = ['1111111111111111', '1111111111111112', 'Agus Salim (Uji Coba Warga Miskin)', '1980-05-12', 'Laki-laki', 'Kawin', 'Desa Pinggiran', 'Tidak Bekerja', '< Rp 500.000', '> 4 Orang', 'Tanah / Kayu Kualitas Rendah', 'Bilik Bambu / Rumbia / Terpal', 'Ijuk / Rumbia / Daun', 'Numpang / Bebas Sewa (Fasum / Lahan orang lain)', 'Tidak Sekolah', 'Lebih dari 2 Anak Sekolah', 'Tidak Ada Aset Sama Sekali', '< Rp 500.000', 'Numpang / Tidak Ada Listrik', 'Mata Air Tidak Terlindung / Sungai / Air Hujan', 'Disabilitas Berat / Penyakit Menahun (Stroke/TBC)', 'Proses'];
$dummy_data[] = ['2222222222222222', '2222222222222223', 'Wira Pradana (Uji Coba Warga Mampu)', '1990-05-12', 'Laki-laki', 'Kawin', 'Kota Elite', 'Wiraswasta / Pedagang', '> Rp 4.000.000', '0 - 2 Orang', 'Keramik / Marmer / Granit', 'Tembok Penuh (Diplester & Dicat)', 'Genteng Tanah Liat / Baja Ringan / Genteng Keramik', 'Milik Sendiri / Warisan', 'Sarjana / Perguruan Tinggi', '1 - 2 Anak Sekolah', 'Aset Mewah (Mobil / Lahan Kosong / Emas Murni)', '> Rp 4.000.000', 'Listrik > 1300 VA', 'PAM / Leding Meteran / Air Kemasan Bermerk', 'Sehat Fisik & Jasmani', 'Proses'];

$berhasil = 0;

foreach ($dummy_data as $b) {
    try {
        $sql = "INSERT INTO warga (nik, no_kk, nama, tempat_tanggal_lahir, jenis_kelamin, status_perkawinan, alamat, pekerjaan, penghasilan, jumlah_tanggungan, kondisi_lantai, kondisi_dinding, kondisi_atap, status_kepemilikan_rumah, pendidikan_terakhir, jumlah_anak_sekolah, kepemilikan_aset, pengeluaran_bulanan, akses_listrik, akses_air, kondisi_kesehatan, status_bantuan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if($stmt->execute($b)) $berhasil++;
    } catch (Exception $e) {
        // Abaikan jika NIK duplikat
    }
}
echo "<h3>$berhasil Data dummy berhasil di-generate secara rasional.</h3>";
echo "<a href='index.php'>Kembali ke Web</a> | <a href='admin/warga.php'>Ke Halaman Admin</a>";
?>
