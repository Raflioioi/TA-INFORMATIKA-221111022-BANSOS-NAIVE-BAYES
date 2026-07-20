<?php
require_once 'config/database.php';

$result = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik = trim($_POST['nik'] ?? '');

    if (empty($nik)) {
        $error = "Silakan masukkan NIK Anda.";
    } elseif (!is_numeric($nik) || strlen($nik) !== 16) {
        $error = "NIK harus berupa 16 digit angka kependudukan yang valid.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM warga WHERE nik = ?");
        $stmt->execute([$nik]);
        $row = $stmt->fetch();

        if ($row) {
            $result = $row;
        } else {
            $error = "Maaf, NIK <strong>" . htmlspecialchars($nik) . "</strong> tidak ditemukan dalam sistem kami sebagai penerima bansos.";
        }
    }
}

require_once 'includes/header.php';
?>

<div style="font-family: 'Inter', sans-serif; margin-top: -3rem;">
    <!-- PREMIUM HERO SECTION -->
    <section
        style="position: relative; padding: 6rem 2rem 8rem; background: url('assets/images/hero_background.png') center/cover no-repeat; display: flex; flex-direction: column; align-items: center; text-align: center; color: #fff;">
        <div
            style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: linear-gradient(135deg, rgba(8, 25, 48, 0.85) 0%, rgba(13, 33, 73, 0.75) 100%), url('assets/images/kota malang.jpg'); background-size: cover; background-position: center;">
        </div>

        <div style="position: relative; z-index: 2; max-width: 800px;">
            <h1 style="font-size: 2.8rem; font-weight: 800; margin-bottom: 1.5rem; line-height: 1.2; color: #ffffff;">
                Wujud Kepedulian Untuk <br>Kesejahteraan Bersama</h1>
            <p style="font-size: 1.15rem; line-height: 1.6; color: #e2e8f0; font-weight: 300;">Bantuan Sosial (Bansos)
                merupakan program strategis untuk meringankan beban masyarakat. Platform ini hadir agar Anda dapat
                memantau status penyaluran bansos di Kota Malang secara cepat, transparan, dan akurat langsung dari
                perangkat Anda.</p>
        </div>
    </section>

    <!-- FLOATING SEARCH SECTION -->
    <section style="position: relative; z-index: 10; margin-top: -4rem; padding: 0 1.5rem;">
        <div
            style="max-width: 700px; margin: 0 auto; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(16px); border-radius: 20px; padding: 2.5rem; box-shadow: 0 20px 40px -15px rgba(0,0,0,0.15); border: 1px solid rgba(255,255,255,1); text-align: center;">
            <h2 style="color: #0d2149; font-size: 1.6rem; margin-bottom: 1.5rem; font-weight: 700;">Cek Status Bantuan
                Anda</h2>

            <form method="POST" action="index.php" style="display: flex; flex-direction: column; gap: 1rem;">
                <div
                    style="display: flex; align-items: stretch; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; padding: 0.4rem; transition: all 0.3s ease;">
                    <div
                        style="display: flex; align-items: center; padding: 0 1rem; color: #94a3b8; font-size: 1.2rem;">
                        <i class="fa-solid fa-id-card"></i></div>
                    <input type="text" name="nik" placeholder="Masukkan 16 Digit NIK Kepala Keluarga..." required
                        minlength="16" maxlength="16" pattern="\d{16}"
                        value="<?php echo isset($_POST['nik']) ? htmlspecialchars($_POST['nik']) : ''; ?>"
                        style="flex: 1; border: none; background: transparent; padding: 0.8rem 0; font-size: 1rem; outline: none; color: #1e293b; font-weight: 500;">
                    <button type="submit"
                        style="background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; border: none; padding: 0 2rem; border-radius: 8px; cursor: pointer; font-size: 1rem; font-weight: 600; box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.39); transition: transform 0.2s ease;"><i
                            class="fa-solid fa-magnifying-glass" style="margin-right: 0.5rem;"></i> Cari</button>
                </div>
            </form>

            <?php if ($error): ?>
                <div class="alert alert-error" style="text-align: left; margin-top: 1.5rem; border-radius: 8px;">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- INFORMATION GRID -->
    <div class="container" style="max-width: 1100px; margin: 0 auto; padding: 4rem 1.5rem;">
        <?php if (!$result): ?>
            <div
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 3rem; text-align: left; margin-bottom: 2rem;">
                <div
                    style="background: white; border-radius: 24px; padding: 2.5rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; display: flex; flex-direction: column;">
                    <div
                        style="background: #eff6ff; width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; color: #3b82f6; font-size: 1.8rem;">
                        <i class="fa-solid fa-laptop-code"></i>
                    </div>
                    <h3 style="color: #0f172a; margin-bottom: 1rem; font-size: 1.3rem; font-weight: 700;">Mudah & Transparan
                    </h3>
                    <p style="color: #64748b; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem;">Cukup gunakan
                        Nomor Induk Kependudukan (NIK) Anda untuk mengakses sistem. Sistem kami akan memberikan informasi
                        secara *real-time* langsung dari pusat data Dinas Sosial Kota Malang.</p>
                </div>

                <div
                    style="background: white; border-radius: 24px; padding: 2.5rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; display: flex; flex-direction: column;">
                    <div
                        style="background: #fdf2f8; width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; color: #db2777; font-size: 1.8rem;">
                        <i class="fa-solid fa-users-viewfinder"></i>
                    </div>
                    <h3 style="color: #0f172a; margin-bottom: 1rem; font-size: 1.3rem; font-weight: 700;">Klasifikasi Tepat
                        Sasaran</h3>
                    <p style="color: #64748b; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem;">Platform cerdas
                        kami memastikan keakuratan penyaluran menggunakan lebih dari 14 parameter standar ketetapan Menteri
                        Sosial RI untuk perlindungan fakir miskin di seluruh kecamatan.</p>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($result): ?>
            <div id="hasil-cek" class="glass-card"
                style="margin-top: 2rem; margin-bottom: 4rem; animation: slideUp 0.5s ease; max-width: 800px; margin-left: auto; margin-right: auto;">
                <h3
                    style="border-bottom: 2px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-id-card" style="color: var(--primary-color);"></i> Detail Penerima Manfaat
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600;">NIK</label>
                        <p style="font-weight: 600; font-size: 1.1rem; color: var(--text-color);">
                            <?php echo htmlspecialchars($result['nik']); ?>
                        </p>
                    </div>
                    <div>
                        <label style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600;">Nama Lengkap</label>
                        <p style="font-weight: 600; font-size: 1.1rem; color: var(--text-color);">
                            <?php echo htmlspecialchars($result['nama']); ?>
                        </p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                    <div>
                        <label style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600;">Umur</label>
                        <p style="font-weight: 600; font-size: 1.1rem; color: var(--text-color);">
                            <?php 
                            $umur = 'Tidak diketahui';
                            if (!empty($result['tempat_tanggal_lahir'])) {
                                try {
                                    $lahir = new DateTime($result['tempat_tanggal_lahir']);
                                    $sekarang = new DateTime('today');
                                    $umur = $lahir->diff($sekarang)->y . ' Tahun';
                                } catch (Exception $e) {
                                    // Fallback jika format tanggal tidak valid
                                    $umur = htmlspecialchars($result['tempat_tanggal_lahir']);
                                }
                            }
                            echo $umur;
                            ?>
                        </p>
                    </div>
                    <div>
                        <label style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600;">Alamat Tempat Tinggal</label>
                        <p style="font-weight: 600; font-size: 1.1rem; color: var(--text-color); line-height: 1.5;">
                            <?php echo !empty($result['alamat']) ? nl2br(htmlspecialchars($result['alamat'])) : '-'; ?>
                        </p>
                    </div>
                </div>

                <div
                    style="margin-top: 2rem; padding: 1.5rem; background: var(--bg-color); border-radius: 12px; display: flex; justify-content: space-between; align-items: center; border-left: 5px solid var(--primary-color);">
                    <div>
                        <label
                            style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600; display: block; margin-bottom: 0.5rem;">Status
                            Bansos Anda Saat Ini</label>
                        <?php
                        $status_class = '';
                        $icon = '';
                        switch ($result['status_bantuan']) {
                            case 'Layak':
                                $status_class = 'badge-layak';
                                $icon = '<i class="fa-solid fa-check-circle"></i>';
                                break;
                            case 'Tidak Layak':
                                $status_class = 'badge-tidak-layak';
                                $icon = '<i class="fa-solid fa-times-circle"></i>';
                                break;
                            case 'Disalurkan':
                                $status_class = 'badge-disalurkan';
                                $icon = '<i class="fa-solid fa-handshake-angle"></i>';
                                break;
                            default:
                                $status_class = 'badge-proses';
                                $icon = '<i class="fa-solid fa-clock"></i>';
                                break;
                        }
                        ?>
                        <span class="badge <?php echo $status_class; ?>" style="font-size: 1.1rem; padding: 0.5rem 1.2rem;">
                            <?php echo $icon . ' ' . htmlspecialchars($result['status_bantuan']); ?>
                        </span>
                    </div>
                    <div style="text-align: right;">
                        <i class="fa-solid fa-shield-check"
                            style="font-size: 2.5rem; color: var(--primary-color); opacity: 0.2;"></i>
                    </div>
                </div>
            </div>

            <script>
                // Auto scroll to results if searching
                document.addEventListener("DOMContentLoaded", function () {
                    document.getElementById('hasil-cek').scrollIntoView({ behavior: 'smooth' });
                });
            </script>
        <?php endif; ?>
    </div>

    <!-- CONTACT US SECTION -->
    <section id="kontak" style="padding: 4rem 0; text-align: center; background-color: #ffffff; margin-bottom: 0;">
        <h4 style="color: #0d2149; font-size: 1.1rem; font-weight: 700; text-transform: uppercase;">Hubungi Kami</h4>
        <h2 style="color: #0d2149; font-size: 2rem; font-weight: 700; margin-top: 0.5rem;">Hubungi kami untuk info lebih
            lanjut</h2>
        <div style="width: 50px; height: 3px; background-color: #0d2149; margin: 1rem auto 3rem;"></div>

        <div
            style="display: flex; flex-wrap: wrap; gap: 2rem; max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; align-items: flex-start;">
            <!-- Left Info Panel -->
            <div
                style="flex: 1; min-width: 300px; border-top: 3px solid #0d2149; border-bottom: 3px solid #0d2149; padding: 2.5rem 2rem; text-align: left; background: #fff; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
                <div style="display: flex; gap: 1.5rem; margin-bottom: 2.5rem;">
                    <div
                        style="width: 50px; height: 50px; border-radius: 50%; background: #fef2f2; color: #0d2149; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 1.1rem; font-weight: 700; color: #4b5563; margin-bottom: 0.5rem;">Lokasi:
                        </h4>
                        <p style="color: #9ca3af; font-size: 0.95rem; line-height: 1.6; margin: 0;">Jl. Ki Ageng Gribig
                            No.5, Kedungkandang, Kec. Kedungkandang, Kota Malang, Jawa Timur 65139</p>
                    </div>
                </div>

                <div style="display: flex; gap: 1.5rem;">
                    <div
                        style="width: 50px; height: 50px; border-radius: 50%; background: #fef2f2; color: #0d2149; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 1.1rem; font-weight: 700; color: #4b5563; margin-bottom: 0.5rem;">Email:
                        </h4>
                        <p style="color: #9ca3af; font-size: 0.95rem; line-height: 1.6; margin: 0;">
                            dinsos@malangkota.go.id</p>
                    </div>
                </div>
            </div>

            <!-- Right Map Panel -->
            <div
                style="flex: 2; min-width: 400px; border-top: 3px solid #0d2149; border-bottom: 3px solid #230d214900e9ff; padding: 1rem; background: #fff; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.0942438562815!2d112.64864507593452!3d-7.98920367967679!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd6286663472655%3A0xd2a9cccc558cfb8a!2sKantor%20Dinas%20Sosial%20P3AP2KB%20Kota%20Malang!5e0!3m2!1sid!2sid!4v1775461656164!5m2!1sid!2sid"
                    width="100%" height="450" style="border:0; border-radius: 4px;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>

    <a href="#"
        style="position: fixed; bottom: 20px; right: 20px; background-color: #0d2149; color: white; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 1.2rem; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 50;">
        <i class="fa-solid fa-arrow-up"></i>
    </a>

    <style>
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <?php require_once 'includes/footer.php'; ?>