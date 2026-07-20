<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header('Location: login.php');
    exit;
}

// Stats queries
$total_warga = $pdo->query("SELECT COUNT(*) FROM warga")->fetchColumn();
$total_layak = $pdo->query("SELECT COUNT(*) FROM warga WHERE status_bantuan='Layak'")->fetchColumn();
$total_tidak_layak = $pdo->query("SELECT COUNT(*) FROM warga WHERE status_bantuan='Tidak Layak'")->fetchColumn();

// Kelurahan distribution query
$stmt_kel = $pdo->query("SELECT kecamatan, kelurahan, COUNT(*) as total_menerima FROM warga WHERE status_bantuan IN ('Layak', 'Disalurkan') AND kelurahan IS NOT NULL AND kelurahan != '' GROUP BY kecamatan, kelurahan ORDER BY total_menerima DESC");
$kel_data = $stmt_kel->fetchAll();

// Get unique kecamatan list for dropdown
$stmt_kec_list = $pdo->query("SELECT DISTINCT kecamatan FROM warga WHERE kecamatan IS NOT NULL AND kecamatan != '' ORDER BY kecamatan ASC");
$kec_list = $stmt_kec_list->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - BansosKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <!-- Load Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="admin-nav">
        <div class="admin-brand"><i class="fa-solid fa-shield-halved"></i> Halaman Admin</div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <span style="background: rgba(37, 99, 235, 0.08); color: var(--primary-color); padding: 0.45rem 1.1rem; border-radius: 9999px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.85rem;">
                <i class="fa-solid fa-user-shield"></i>
                <?php echo htmlspecialchars($_SESSION['admin_user']); ?>
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
                <a href="index.php" class="sidebar-link active"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
                <a href="warga.php" class="sidebar-link"><i class="fa-solid fa-users"></i> Kelola Warga</a>
                <a href="warga_kecamatan.php" class="sidebar-link"><i class="fa-solid fa-map-location-dot"></i> Filter Kecamatan</a>
            </nav>
        </aside>

        <main class="admin-content" style="padding: 0; padding-left: 2rem;">
            <div style="margin-bottom: 2rem;">
                <h2 style="font-size: 2rem; margin-bottom: 0.5rem; color: #0F172A;">Dashboard Data Bantuan</h2>
                <p style="color: var(--text-muted); font-size: 1.1rem;">Ringkasan pemrosesan dan penyaluran bantuan
                    sosial terkini.</p>
            </div>

            <div class="stat-grid">
                <div class="stat-card" style="border-left-color: #2563EB; background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(239,246,255,0.4) 100%); border-color: rgba(37, 99, 235, 0.15);">
                    <div class="stat-icon" style="background: #EFF6FF; color: #2563EB;">
                        <i class="fa-solid fa-users-viewfinder"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_warga; ?></h3>
                        <p>Total Warga</p>
                    </div>
                </div>

                <div class="stat-card" style="border-left-color: #10B981; background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(240,253,250,0.4) 100%); border-color: rgba(16, 185, 129, 0.15);">
                    <div class="stat-icon" style="background: #D1FAE5; color: #059669;">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3 style="color: #059669;"><?php echo $total_layak; ?></h3>
                        <p>Warga Layak Menerima</p>
                    </div>
                </div>

                <div class="stat-card" style="border-left-color: #EF4444; background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(254,242,242,0.4) 100%); border-color: rgba(239, 68, 68, 0.15);">
                    <div class="stat-icon" style="background: #FEE2E2; color: #DC2626;">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <div class="stat-info">
                        <h3 style="color: #DC2626;"><?php echo $total_tidak_layak; ?></h3>
                        <p>Warga Tidak Layak</p>
                    </div>
                </div>
            </div>

            <!-- Card 1: Distribusi per Kecamatan (Table) -->
            <div class="glass-card" style="margin-top: 2.5rem; border-radius: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                    <h3 style="margin: 0; font-size: 1.3rem; color: #1E293B;"><i class="fa-solid fa-chart-column"
                            style="color: var(--primary-color);"></i> Distribusi Bantuan per Kecamatan</h3>
                    <a href="warga_kecamatan.php" class="btn btn-secondary" style="font-size: 0.9rem;">Lihat Detail <i
                            class="fa-solid fa-arrow-right"></i></a>
                </div>

                <div class="table-responsive" style="box-shadow: none; border: none;">
                    <table style="margin-bottom: 0;">
                        <thead>
                            <tr style="background: #F8FAFC;">
                                <th style="width: 5%;">No</th>
                                <th>Wilayah Kecamatan</th>
                                <th style="text-align: center;">Jumlah Warga Menerima</th>
                                <th style="text-align: center;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT kecamatan, COUNT(*) as total_menerima FROM warga WHERE status_bantuan IN ('Layak', 'Disalurkan') AND kecamatan IS NOT NULL AND kecamatan != '' GROUP BY kecamatan ORDER BY total_menerima DESC");
                            $distribusi = $stmt->fetchAll();

                            if (count($distribusi) > 0) {
                                $no = 1;
                                foreach ($distribusi as $d) {
                                    echo "<tr>";
                                    echo "<td style='color: var(--text-muted); font-weight: 500;'>{$no}</td>";
                                    echo "<td><strong style='color:#1e293b; font-size: 1.05rem;'>" . htmlspecialchars($d['kecamatan']) . "</strong></td>";
                                    echo "<td style='text-align: center;'><span class='badge badge-layak' style='font-size: 0.95rem; padding: 0.4rem 1rem;'><i class='fa-solid fa-house-chimney-user'></i> " . htmlspecialchars($d['total_menerima']) . " Keluarga</span></td>";
                                    echo "<td style='text-align: center;'><a href='warga_kecamatan.php?kecamatan=" . urlencode($d['kecamatan']) . "' class='btn btn-secondary' style='font-size: 0.85rem; padding: 0.4rem 0.8rem;'><i class='fa-solid fa-eye'></i> Lihat Data</a></td>";
                                    echo "</tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='4' style='text-align:center; padding: 3rem; color: #94a3b8;'><i class='fa-solid fa-chart-bar' style='font-size: 2rem; margin-bottom: 1rem; display: block;'></i> Belum ada data warga yang memenuhi kriteria kelayakan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Card 2: Grafik Distribusi per Kelurahan (Chart.js) -->
            <div class="glass-card" style="margin-top: 2.5rem; border-radius: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                    <h3 style="margin: 0; font-size: 1.3rem; color: #1E293B;"><i class="fa-solid fa-chart-bar" style="color: var(--primary-color);"></i> Sebaran per Kelurahan</h3>
                    <div style="position: relative;">
                        <select id="filterKecamatanChart" style="appearance: none; padding: 0.4rem 2rem 0.4rem 0.8rem; border-radius: 8px; border: 1px solid #e2e8f0; background: white; color: #0F172A; font-size: 0.85rem; outline: none; cursor: pointer; font-weight: 600;">
                            <option value="all">Semua Kecamatan</option>
                            <?php foreach($kec_list as $kec): ?>
                                <option value="<?php echo htmlspecialchars($kec); ?>"><?php echo htmlspecialchars($kec); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fa-solid fa-chevron-down" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; font-size: 0.7rem;"></i>
                    </div>
                </div>
                
                <div style="position: relative; flex-grow: 1; min-height: 380px; display: flex; align-items: center; justify-content: center; width: 100%;">
                    <canvas id="kelurahanChart"></canvas>
                    <div id="noDataChart" style="display: none; text-align: center; color: #94a3b8;">
                        <i class="fa-solid fa-chart-line" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                        Tidak ada data penerima bantuan.
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Raw kelurahan data from PHP
        const rawKelData = <?php echo json_encode($kel_data); ?>;
        
        let chartInstance = null;

        function renderChart(kecamatanFilter = 'all') {
            // Filter data
            let filteredData = [];
            if (kecamatanFilter === 'all') {
                // Show top 10 kelurahan overall to avoid overcrowding
                filteredData = rawKelData.slice(0, 10);
            } else {
                // Show all kelurahan in the selected kecamatan
                filteredData = rawKelData.filter(item => item.kecamatan === kecamatanFilter);
            }

            const canvas = document.getElementById('kelurahanChart');
            const noDataEl = document.getElementById('noDataChart');

            if (filteredData.length === 0) {
                canvas.style.display = 'none';
                noDataEl.style.display = 'block';
                return;
            } else {
                canvas.style.display = 'block';
                noDataEl.style.display = 'none';
            }

            const labels = filteredData.map(item => item.kelurahan);
            const values = filteredData.map(item => parseInt(item.total_menerima));

            // If chart already exists, destroy it first
            if (chartInstance) {
                chartInstance.destroy();
            }

            const ctx = canvas.getContext('2d');
            
            // Create nice horizontal gradient for bars
            const gradient = ctx.createLinearGradient(0, 0, ctx.canvas.width || 400, 0);
            gradient.addColorStop(0, 'rgba(37, 99, 235, 0.15)'); // Royal Blue transparent
            gradient.addColorStop(1, 'rgba(99, 102, 241, 0.85)'); // Indigo solid

            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Keluarga Penerima',
                        data: values,
                        backgroundColor: gradient,
                        borderColor: '#2563EB',
                        borderWidth: 1.5,
                        borderRadius: 8,
                        borderSkipped: false,
                        barThickness: 20
                    }]
                },
                options: {
                    indexAxis: 'y', // Makes it a horizontal bar chart
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Hide legend
                        },
                        tooltip: {
                            backgroundColor: '#0F172A',
                            titleFont: { family: 'Inter', size: 13, weight: '600' },
                            bodyFont: { family: 'Inter', size: 12 },
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.raw} Keluarga Penerima`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: '#E2E8F0',
                                drawBorder: false
                            },
                            ticks: {
                                font: { family: 'Inter', size: 11 },
                                color: '#64748B',
                                stepSize: 1
                            }
                        },
                        y: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                font: { family: 'Inter', size: 12, weight: '500' },
                                color: '#1E293B'
                            }
                        }
                    }
                }
            });
        }

        // Initialize chart on load
        document.addEventListener('DOMContentLoaded', () => {
            renderChart('all');

            // Handle dropdown change
            document.getElementById('filterKecamatanChart').addEventListener('change', (e) => {
                renderChart(e.target.value);
            });
        });
    </script>
</body>

</html>