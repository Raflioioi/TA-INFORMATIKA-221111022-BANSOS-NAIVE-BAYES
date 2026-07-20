<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header('Location: login.php');
    exit;
}

// Catch message
$msg = $_SESSION['msg'] ?? null;
$msg_type = $_SESSION['msg_type'] ?? 'success';
unset($_SESSION['msg'], $_SESSION['msg_type']);

// Handle Filter, Search, and Sort
$filter_kecamatan = $_GET['kecamatan'] ?? '';
$filter_kelurahan = $_GET['kelurahan'] ?? '';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'terbaru';

$order_clause = "ORDER BY id_warga DESC";
switch ($sort) {
    case 'terlama':   $order_clause = "ORDER BY id_warga ASC"; break;
    case 'nama_az':   $order_clause = "ORDER BY nama ASC"; break;
    case 'nama_za':   $order_clause = "ORDER BY nama DESC"; break;
    case 'status':    $order_clause = "ORDER BY status_bantuan ASC, id_warga DESC"; break;
}

$where_conditions = [];
$params = [];

if (!empty($filter_kecamatan)) {
    $where_conditions[] = "kecamatan = ?";
    $params[] = $filter_kecamatan;
}
if (!empty($filter_kelurahan)) {
    $where_conditions[] = "kelurahan = ?";
    $params[] = $filter_kelurahan;
}
if (!empty($search)) {
    $where_conditions[] = "nama LIKE ?";
    $params[] = "%" . $search . "%";
}

$where_clause = "";
if (count($where_conditions) > 0) {
    $where_clause = "WHERE " . implode(" AND ", $where_conditions);
}

// Pagination Setup
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM warga {$where_clause}");
$count_stmt->execute($params);
$total_records = (int) $count_stmt->fetchColumn();

$limit = 10;
$total_pages = ceil($total_records / $limit);

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
} elseif ($page > $total_pages && $total_pages > 0) {
    $page = $total_pages;
}

$offset = ($page - 1) * $limit;
if ($offset < 0) {
    $offset = 0;
}

// Get all unique kecamatan for filter options
$stmt_kec = $pdo->query("SELECT DISTINCT kecamatan FROM warga WHERE kecamatan IS NOT NULL AND kecamatan != '' ORDER BY kecamatan ASC");
$kecamatan_list = $stmt_kec->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter Kecamatan - BansosKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="admin-nav">
        <div class="admin-brand"><i class="fa-solid fa-shield-halved"></i> Halaman Admin</div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <span style="background: rgba(37, 99, 235, 0.08); color: var(--primary-color); padding: 0.45rem 1.1rem; border-radius: 9999px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.85rem;">
                <i class="fa-solid fa-user-shield"></i>
                <?php echo htmlspecialchars($_SESSION['admin_user'] ?? 'admin'); ?>
            </span>
            <a href="../index.php" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.85rem;"><i
                    class="fa-solid fa-globe"></i> Lihat Web</a>
            <a href="logout.php" class="btn"
                style="padding: 0.5rem 1rem; font-size: 0.85rem; border-radius: 8px; background: rgba(239, 68, 68, 0.1); color: #EF4444; border: 1px solid rgba(239, 68, 68, 0.2);"><i
                    class="fa-solid fa-right-from-bracket"></i> Keluar</a>
        </div>
    </div>

    <div class="container admin-layout" style="max-width: 1400px; padding: 2rem;">
        <aside class="sidebar"
            style="border-radius: 16px; height: calc(100vh - 120px); box-shadow: var(--shadow-sm); padding: 1.5rem 1rem;">
            <nav class="sidebar-nav">
                <a href="index.php" class="sidebar-link"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
                <a href="warga.php" class="sidebar-link"><i class="fa-solid fa-users"></i> Kelola Warga</a>
                <a href="warga_kecamatan.php" class="sidebar-link active"><i class="fa-solid fa-map-location-dot"></i> Filter Kecamatan</a>
            </nav>
        </aside>

        <main class="admin-content" style="padding: 0; padding-left: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
                <div>
                    <h2 style="font-size: 2rem; margin-bottom: 0.5rem; color: #0F172A;">Filter Warga per Kecamatan</h2>
                    <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 0;">Lihat data warga penerima bantuan berdasarkan lokasi kecamatan tempat tinggal.</p>
                </div>
            </div>

            <?php if ($msg): ?>
                <div class="alert alert-<?php echo $msg_type; ?>" style="animation: slideDown 0.3s ease;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <form method="GET" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                <!-- Kotak Filter & Pencarian -->
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="position: relative;">
                        <input type="text" name="search" placeholder="Cari nama warga..." value="<?php echo htmlspecialchars($search); ?>" style="padding: 0.6rem 1rem 0.6rem 2.5rem; border-radius: 8px; border: 1px solid #e2e8f0; width: 220px; font-size: 0.95rem; outline: none; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);">
                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    </div>

                    <label for="kecamatan" style="color: #64748b; font-size: 0.95rem; font-weight: 600; margin-left: 0.5rem;">Pilih Kecamatan:</label>
                    <div style="position: relative;">
                        <select name="kecamatan" id="kecamatan" onchange="populateKelurahan(this.value);" style="appearance: none; padding: 0.6rem 2.5rem 0.6rem 1rem; border-radius: 8px; border: 1px solid #e2e8f0; background: white; color: #0F172A; font-size: 0.95rem; outline: none; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.05); font-weight: 500; min-width: 180px;">
                            <option value="">-- Semua Kecamatan --</option>
                            <?php foreach($kecamatan_list as $kec): ?>
                                <option value="<?php echo htmlspecialchars($kec); ?>" <?php echo ($filter_kecamatan == $kec) ? 'selected' : ''; ?>><?php echo htmlspecialchars($kec); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fa-solid fa-chevron-down" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; font-size: 0.8rem;"></i>
                    </div>

                    <label for="kelurahan" style="color: #64748b; font-size: 0.95rem; font-weight: 600; margin-left: 0.5rem;">Kelurahan:</label>
                    <div style="position: relative;">
                        <select name="kelurahan" id="kelurahan" style="appearance: none; padding: 0.6rem 2.5rem 0.6rem 1rem; border-radius: 8px; border: 1px solid #e2e8f0; background: white; color: #0F172A; font-size: 0.95rem; outline: none; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.05); font-weight: 500; min-width: 180px;">
                            <option value="">-- Semua Kelurahan --</option>
                        </select>
                        <i class="fa-solid fa-chevron-down" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; font-size: 0.8rem;"></i>
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding: 0.55rem 1.2rem; border-radius: 8px; border: none; cursor: pointer; font-weight: 500;">Cari / Terapkan</button>
                    <?php if(!empty($filter_kecamatan) || !empty($filter_kelurahan) || !empty($search)): ?>
                        <a href="warga_kecamatan.php" class="btn btn-secondary" style="padding: 0.55rem 1.2rem; border-radius: 8px; text-decoration: none; font-weight: 500;">Reset</a>
                    <?php endif; ?>
                </div>
            </form>

            <div class="glass-card" style="padding: 0; border-radius: 16px; overflow: hidden;">
                <div class="table-responsive" style="box-shadow: none; border: none; border-radius: 0;">
                    <table style="margin-bottom: 0;">
                        <thead>
                            <tr style="background: #F8FAFC;">
                                <th style="width: 5%;">No</th>
                                <th>Identitas Kependudukan</th>
                                <th>Lingkungan / Alamat</th>
                                <th>Status Bantuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->prepare("SELECT * FROM warga {$where_clause} {$order_clause} LIMIT {$limit} OFFSET {$offset}");
                            $stmt->execute($params);
                            $warga = $stmt->fetchAll();
                            $no = $offset + 1;

                            if (count($warga) > 0) {
                                foreach ($warga as $w) {
                                    $s_class = ($w['status_bantuan'] == 'Layak') ? 'badge-layak' : (($w['status_bantuan'] == 'Tidak Layak') ? 'badge-tidak-layak' : (($w['status_bantuan'] == 'Disalurkan') ? 'badge-disalurkan' : 'badge-proses'));

                                    echo "<tr style='transition: all 0.2s; border-bottom: 1px solid #F1F5F9;'>";
                                    echo "<td style='color: var(--text-muted); font-weight: 500;'>{$no}</td>";
                                    echo "<td>
                                            <div style='font-family: monospace; font-size: 1.1rem; color: #0F172A; margin-bottom: 0.2rem;'>" . htmlspecialchars($w['nik']) . "</div>
                                            <div style='font-size:0.85rem; color: var(--text-muted);'><i class='fa-regular fa-user'></i> " . htmlspecialchars($w['nama']) . "</div>
                                          </td>";
                                    echo "<td>
                                            <strong style='color:#1e293b; font-size: 1.05rem; display: block; margin-bottom: 0.2rem;'>" . htmlspecialchars($w['kecamatan'] ?? 'N/A') . " / " . htmlspecialchars($w['kelurahan'] ?? 'N/A') . "</strong>
                                            <span style='font-size:0.85rem; color:#64748b; background: #E2E8F0; padding: 0.2rem 0.5rem; border-radius: 4px;'>" . htmlspecialchars($w['alamat']) . "</span>
                                          </td>";
                                    echo "<td><span class='badge {$s_class}' style='padding: 0.4rem 0.8rem; font-size: 0.85rem;'>" . htmlspecialchars($w['status_bantuan']) . "</span></td>";
                                    echo "</tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='4' style='text-align:center; padding: 4rem; color: #94a3b8;'><i class='fa-solid fa-map-location-dot' style='font-size: 3rem; margin-bottom: 1rem; display: block;'></i><br>Tidak ada warga yang terdaftar di kecamatan ini.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($total_pages > 1): ?>
                <div class="pagination-container" style="border-radius: 0 0 16px 16px; border: none; border-top: 1px solid var(--border-color); box-shadow: none;">
                    <div class="pagination-info">
                        Menampilkan <strong><?php echo min($offset + 1, $total_records); ?></strong> - <strong><?php echo min($offset + $limit, $total_records); ?></strong> dari <strong><?php echo $total_records; ?></strong> warga
                    </div>
                    <div class="pagination-pages">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo !empty($filter_kecamatan) ? '&kecamatan='.urlencode($filter_kecamatan) : ''; ?><?php echo !empty($filter_kelurahan) ? '&kelurahan='.urlencode($filter_kelurahan) : ''; ?><?php echo ($sort != 'terbaru') ? '&sort='.urlencode($sort) : ''; ?>" class="pagination-prev" title="Halaman Sebelumnya"><i class="fa-solid fa-angle-left" style="margin-right: 8px;"></i> Previous</a>
                        <?php endif; ?>

                        <?php
                        $range = 2;
                        $initial_num = $page - $range;
                        $condition_limit_num = ($page + $range) + 1;
                        
                        for ($i = $initial_num; $i < $condition_limit_num; $i++) {
                            if ($i > 0 && $i <= $total_pages) {
                                if ($i == $page) {
                                    echo "<span class='pagination-btn active'>{$i}</span>";
                                } else {
                                    $search_param = !empty($search) ? '&search='.urlencode($search) : '';
                                    $kec_param = !empty($filter_kecamatan) ? '&kecamatan='.urlencode($filter_kecamatan) : '';
                                    $kel_param = !empty($filter_kelurahan) ? '&kelurahan='.urlencode($filter_kelurahan) : '';
                                    $sort_param = ($sort != 'terbaru') ? '&sort='.urlencode($sort) : '';
                                    echo "<a href='?page={$i}{$search_param}{$kec_param}{$kel_param}{$sort_param}' class='pagination-btn'>{$i}</a>";
                                }
                            }
                        }
                        ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo !empty($filter_kecamatan) ? '&kecamatan='.urlencode($filter_kecamatan) : ''; ?><?php echo !empty($filter_kelurahan) ? '&kelurahan='.urlencode($filter_kelurahan) : ''; ?><?php echo ($sort != 'terbaru') ? '&sort='.urlencode($sort) : ''; ?>" class="pagination-next" title="Halaman Berikutnya">Next <i class="fa-solid fa-angle-right" style="margin-left: 8px;"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php elseif ($total_records > 0): ?>
                <div class="pagination-container" style="border-radius: 0 0 16px 16px; border: none; border-top: 1px solid var(--border-color); box-shadow: none;">
                    <div class="pagination-info">
                        Menampilkan <strong>1</strong> - <strong><?php echo $total_records; ?></strong> dari <strong><?php echo $total_records; ?></strong> warga
                    </div>
                </div>
                <?php endif; ?>
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

        const kelSelect = document.getElementById('kelurahan');
        const currentKel = "<?= addslashes($filter_kelurahan) ?>";

        function populateKelurahan(kec) {
            kelSelect.innerHTML = '<option value="">-- Semua Kelurahan --</option>';
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

        // Initialize on load
        const initialKec = document.getElementById('kecamatan').value;
        if (initialKec) {
            populateKelurahan(initialKec);
        }
    </script>
</body>

</html>
